<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan Halaman Login
    public function showLoginForm()
    {
        // Jika user sudah login, maka arahkan ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    // Proses Loginnya
    public function login(Request $request)
    {
        // 1. Menvalidasi Input
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // 2. Coba Login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // Redirect ke halaman dashboard
            return redirect()->intended('dashboard')->with('success', 'Berhasil Login');
        }

        // Jika Gagal Login
        return back()->with('error', 'Username atau Password salah!');
    }

    // Proses Logout
    public function logout(Request $request) 
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Anda telah logout!');
    }
}
