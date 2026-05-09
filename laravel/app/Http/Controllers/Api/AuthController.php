<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; // Tambahan untuk validasi yang lebih fleksibel
use PHPMailer\PHPMailer\PHPMailer; // Tambahan untuk kirim email
use PHPMailer\PHPMailer\Exception;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau Password salah'], 401);
        }

        // Cek apakah email sudah diverifikasi jika kamu ingin mewajibkan verifikasi sebelum login
        if (!$user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email anda belum diverifikasi.',
                'needs_verification' => true,
                'email' => $user->email
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'user' => $user
        ]);
    }

    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Generate OTP 6 Digit
        $otp = rand(100000, 999999);

        // 3. Simpan ke Database dengan OTP
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student', // Menambahkan role default sesuai kebutuhan EduVan
            'otp_code' => $otp,
            'otp_expiry' => now()->addMinutes(10),
        ]);

        // 4. Kirim Email via PHPMailer
        $this->sendOtpEmail($user->email, $otp);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil! Silakan cek email untuk kode verifikasi.',
            'user' => $user
        ], 201);
    }

    // LOGIKA BARU: Verifikasi OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        $user = User::where('email', $request->email)
                    ->where('otp_code', $request->otp)
                    ->where('otp_expiry', '>', now())
                    ->first();

        if ($user) {
            $user->email_verified_at = now();
            $user->otp_code = null;
            $user->otp_expiry = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Email berhasil diverifikasi! Silakan login.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kode OTP salah atau sudah kadaluarsa.'
        ], 400);
    }

    // LOGIKA BARU: Fungsi Internal Kirim Email
    private function sendOtpEmail($email, $otp)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = (env('MAIL_PORT') == 465) ? 'ssl' : 'tls';
            $mail->Port       = env('MAIL_PORT');

            $mail->setFrom(env('MAIL_USERNAME'), 'EduVan Team');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Kode Verifikasi Akun EduVan';
            $mail->Body    = "Halo! Kode verifikasi Anda adalah: <b>$otp</b>. Kode ini berlaku selama 10 menit.";

            $mail->send();
        } catch (Exception $e) {
            // Email gagal kirim, tetap biarkan user terdaftar (bisa ditangani lewat resend otp nanti)
        }
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
