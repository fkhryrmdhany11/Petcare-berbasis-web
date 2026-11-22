<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('home');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email atau username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = trim($credentials['email']);
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $remember = $request->boolean('remember');

        if (Auth::attempt([$field => $loginInput, 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();

            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali, Admin!')
                : redirect()->route('home')->with('success', 'Selamat datang kembali di PawPaw Pet Care!');
        }

        return back()->withErrors([
            'email' => 'Email/username atau kata sandi tidak sesuai.',
        ])->withInput($request->only('email'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:user,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.confirmed' => 'Kata sandi dan konfirmasi tidak sama.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $localPart = strtolower(explode('@', $data['email'])[0] ?? 'user');
        $baseUsername = preg_replace('/[^a-z0-9._-]/', '', $localPart) ?: 'user';
        $baseUsername = substr($baseUsername, 0, 40);
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = substr($baseUsername, 0, 35) . $counter;
            $counter++;
        }

        $user = User::create([
            'name' => trim($data['name']),
            'username' => $username,
            'email' => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'phone' => $data['phone'] ?? 'Belum diisi',
            'address' => 'Belum diisi',
            'bio' => 'Pet lover & proud pet parent ♡',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Akun berhasil dibuat! Selamat datang di PawPaw Pet Care 🐾');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar dari sesi.');
    }
}
