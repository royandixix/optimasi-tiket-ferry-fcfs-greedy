<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Penumpang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        $penumpang = Penumpang::firstOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'nama_penumpang' => $user->name,
                'nik' => null,
                'jenis_kelamin' => null,
                'no_hp' => null,
                'alamat' => null,
            ]
        );

        return view('user.profil.edit', compact('user', 'penumpang'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $penumpang = Penumpang::firstOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'nama_penumpang' => $user->name,
                'nik' => null,
                'jenis_kelamin' => null,
                'no_hp' => null,
                'alamat' => null,
            ]
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'nik' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('penumpangs', 'nik')->ignore($penumpang->id),
            ],

            'jenis_kelamin' => [
                'nullable',
                Rule::in(['L', 'P']),
            ],

            'no_hp' => [
                'nullable',
                'string',
                'max:20',
            ],

            'alamat' => [
                'nullable',
                'string',
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'nik.unique' => 'NIK sudah digunakan oleh penumpang lain.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
        ]);

        DB::transaction(function () use ($user, $penumpang, $validated) {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $user->update($userData);

            $penumpang->update([
                'nik' => $validated['nik'] ?? null,
                'nama_penumpang' => $validated['name'],
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
            ]);
        });

        return redirect()
            ->route('user.profil.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}