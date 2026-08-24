@extends('user.layouts.guest')

@section('title', 'Registrasi Penumpang')

@section('content')
<div class="card border-0 shadow">
    <div class="card-body p-4 p-lg-5">

        <div class="mb-4">
            <span class="badge text-bg-success rounded-pill mb-3">
                <i class="bi bi-person-plus me-1"></i>
                Registrasi Penumpang
            </span>

            <h3 class="mb-2">Buat Akun Penumpang</h3>
            <p class="text-muted mb-0">
                Lengkapi data berikut untuk membuat akun dan mulai melakukan pemesanan tiket kapal ferry.
            </p>
        </div>

        <form method="POST" action="{{ route('user.register.process') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="form-control"
                       placeholder="Masukkan nama lengkap"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat Email</label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="form-control"
                       placeholder="Masukkan alamat email aktif"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">NIK</label>
                <input type="text"
                       name="nik"
                       value="{{ old('nik') }}"
                       class="form-control"
                       placeholder="Masukkan NIK, boleh dikosongkan">
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-select">
                    <option value="">Pilih jenis kelamin</option>
                    <option value="L" @selected(old('jenis_kelamin') === 'L')>
                        Laki-laki
                    </option>
                    <option value="P" @selected(old('jenis_kelamin') === 'P')>
                        Perempuan
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Nomor HP</label>
                <input type="text"
                       name="no_hp"
                       value="{{ old('no_hp') }}"
                       class="form-control"
                       placeholder="Contoh: 081234567890">
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat"
                          class="form-control"
                          rows="3"
                          placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Kata Sandi</label>
                <input type="password"
                       name="password"
                       class="form-control"
                       placeholder="Minimal 8 karakter"
                       required>
            </div>

            <div class="mb-4">
                <label class="form-label">Konfirmasi Kata Sandi</label>
                <input type="password"
                       name="password_confirmation"
                       class="form-control"
                       placeholder="Ulangi kata sandi"
                       required>
            </div>

            <button type="submit" class="btn btn-success w-100 rounded-pill py-2">
                Buat Akun
            </button>
        </form>

        <div class="text-center mt-4">
            <span class="text-muted">Sudah memiliki akun?</span>
            <a href="{{ route('user.login') }}" class="text-success text-decoration-none fw-semibold">
                Masuk di sini
            </a>
        </div>

    </div>
</div>
@endsection