<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * Menampilkan halaman form login khusus admin.
     */
    public function showLoginForm()
    {
        // Jika admin sudah login sebelumnya, langsung lempar ke dashboard
        if (Auth::check() && Auth::user()->role === 'admin')
        {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Memproses request login admin.
     */
    public function login(Request $request)
    {
        // 1. Validasi input email dan password
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba lakukan autentikasi ke database
        if (Auth::attempt($credentials))
        {
            // 3. PROTEKSI KETAT: Cek apakah user yang login rolenya benar 'admin'
            if (Auth::user()->role === 'admin')
            {
                $request->session()->regenerate();

                // Berhasil masuk, lempar ke dashboard admin
                return redirect()->intended(route('admin.dashboard'));
            }

            // Jika dia student atau role lain, langsung gagalkan login & paksa logout
            Auth::logout();
            return back()->withErrors([
                'email' => 'Akses ditolak! Halaman ini hanya untuk Administrator.',
            ])->withInput($request->only('email'));
        }

        // Jika email atau password salah
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->withInput($request->only('email'));
    }

    /**
     * Memproses logout admin.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
