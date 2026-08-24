<?php

namespace App\Http\Controllers;

use App\Models\Penumpang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('user.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $login = Auth::guard('web')->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'role' => User::ROLE_USER,
            'status' => 'aktif',
        ], $request->boolean('remember'));

        if (! $login) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Email atau kata sandi tidak sesuai, atau akun bukan akun user.');
        }

        $request->session()->regenerate();

        return redirect()
            ->route('user.dashboard')
            ->with('success', 'Berhasil masuk ke akun user.');
    }

    public function showRegister()
    {
        return view('user.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nik' => ['nullable', 'string', 'max:20', 'unique:penumpangs,nik'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
            'nik.unique' => 'NIK sudah digunakan.',
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_USER,
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]);

            Penumpang::create([
                'user_id' => $user->id,
                'nik' => $validated['nik'] ?? null,
                'nama_penumpang' => $validated['name'],
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
            ]);

            return $user;
        });

        Auth::guard('web')->login($user);

        return redirect()
            ->route('user.dashboard')
            ->with('success', 'Registrasi berhasil. Selamat datang di halaman user.');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        // Jangan invalidate seluruh session.
        // Guard admin mungkin sedang login pada tab lain.
        $request->session()->regenerateToken();

        return redirect()
            ->route('user.login')
            ->with('success', 'Anda berhasil keluar.');
    }
}
