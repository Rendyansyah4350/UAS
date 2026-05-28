<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Gunakan validator manual agar jika gagal di HP, kita bisa lempar pesan yang jelas
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Format email atau password tidak valid.'
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // 2. Cek user & kecocokan password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password salah. Periksa kembali data Anda.'
            ], 401);
        }

        // 🚨 3. BYPASS / LONGGARKAN STATUS VERIFIKASI UNTUK TESTING DI HP ASLI
        // Jika di cPanel kolom email_verified_at masih NULL, kita auto-verifikasi aja biar gak mampet lek!
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
        }

        // 4. Generate Token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'user' => $user
        ], 200); // Pastikan statusnya 200 OK murni
    }

    public function register(Request $request)
    {
        // Ganti ke Validator manual agar tidak auto-redirect di device mobile
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8', // 💡 Hapus 'confirmed' jika form di HP cuma ada 1 field password biar gak ribet lek
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $otp = rand(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'otp_code' => $otp,
            'otp_expiry' => now()->addMinutes(10),
        ]);

        // Kirim email (jika SMTP di cPanel lu aktif)
        $this->sendOtpEmail($user->email, $otp);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil! Silakan cek email untuk kode verifikasi.',
            'user' => $user
        ], 201);
    }

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

    /**
     * Fungsi baru untuk mengirim ulang OTP registrasi
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email tidak ditemukan.'], 404);
        }

        // Generate OTP baru
        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expiry = now()->addMinutes(10);
        $user->save();

        // Kirim email menggunakan fungsi pembantu yang sudah ada
        $this->sendOtpEmail($user->email, $otp);

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP baru telah dikirim ke email Anda.'
        ]);
    }

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
            // Tetap aman jika kirim email gagal
        }
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $userData = User::withCount(['enrollments as total_courses', 'certificates as total_certificates'])
            ->find($user->id);

        return response()->json([
            'success' => true,
            'data' => $userData
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Database berhasil diupdate!',
            'user' => $user
        ]);
    }
}
