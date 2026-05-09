<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Fungsi Register & Kirim OTP
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $otp = rand(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'otp_code' => $otp,
            'otp_expiry' => now()->addMinutes(10), // Berlaku 10 menit
        ]);

        $this->sendOtpEmail($user->email, $otp);

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil, silakan cek email untuk kode OTP.',
            'email' => $user->email
        ]);
    }

    // Fungsi Verifikasi OTP (Baru)
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

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
                'status' => 'success',
                'message' => 'Email berhasil diverifikasi! Silakan login.'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Kode OTP salah atau sudah kadaluarsa.'
        ], 400);
    }

    // Fungsi Internal Kirim Email
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
            // Log error bisa ditambahkan di sini jika perlu
        }
    }
}
