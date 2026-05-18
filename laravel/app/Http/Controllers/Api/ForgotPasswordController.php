<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ResetPasswordOtpNotification;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // 1. KIRIM OTP KE EMAIL
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Email tidak terdaftar di sistem kami.'
        ]);

        $email = $request->email;

        // Generate 6 digit angka random
        $otp = rand(100000, 999999);

        // Hapus OTP lama milik email ini jika ada (biar tidak numpuk)
        DB::table('password_reset_otps')->where('email', $email)->delete();

        // Simpan OTP baru dengan masa aktif 15 menit
        DB::table('password_reset_otps')->insert([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(15),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // Kirim email notifikasi menggunakan Driver Mailhost .env cPanel kamu
        Notification::route('mail', $email)->notify(new ResetPasswordOtpNotification($otp));

        return response()->json([
            'status' => 'success',
            'message' => 'Kode OTP reset password berhasil dikirim ke email kamu.'
        ], 200);
    }

    // 2. VALIDASI OTP (DIKILIK PAS USER INPUT KODE DI IONIC)
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ]);

        $check = DB::table('password_reset_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$check) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode OTP yang kamu masukkan salah.'
            ], 400);
        }

        // Cek apakah OTP sudah kedaluwarsa
        if (Carbon::parse($check->expires_at)->isPast()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode OTP sudah kedaluwarsa, silakan minta kode baru.'
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Kode OTP valid, silakan lanjutkan reset password.'
        ], 200);
    }

    // 3. EKSEKUSI UPDATE PASSWORD BARU
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|min:8|confirmed' // Butuh input 'password_confirmation' dari Ionic
        ]);

        // Validasi ulang untuk memastikan OTP belum hangus saat submit password baru
        $otpCheck = DB::table('password_reset_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$otpCheck || Carbon::parse($otpCheck->expires_at)->isPast()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi reset password tidak valid atau sudah kedaluwarsa.'
            ], 400);
        }

        // Update password user di tabel users
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Hapus OTP dari DB setelah sukses digunakan agar tidak bisa dipakai ulang
            DB::table('password_reset_otps')->where('email', $request->email)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Selamat, password akun kamu berhasil diperbarui!'
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'User tidak ditemukan.'
        ], 404);
    }
}
