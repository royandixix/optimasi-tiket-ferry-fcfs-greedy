@extends('user.layouts.guest')

@section('title', 'Login Penumpang')

@section('content')
<div class="card border-0 shadow">
    <div class="card-body p-4 p-lg-5">

        <div class="mb-4">
            <span class="badge text-bg-success rounded-pill mb-3">
                <i class="bi bi-ticket-perforated me-1"></i>
                Portal Penumpang
            </span>

            <h3 class="mb-2">Masuk ke Akun Penumpang</h3>
            <p class="text-muted mb-0">
                Silakan masuk untuk melakukan pemesanan tiket, melihat jadwal keberangkatan,
                dan memantau status pemesanan Anda.
            </p>
        </div>

        <form method="POST" action="{{ route('user.login.process') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Alamat Email</label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="form-control"
                       placeholder="Masukkan alamat email"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Kata Sandi</label>
                <input type="password"
                       name="password"
                       class="form-control"
                       placeholder="Masukkan kata sandi"
                       required>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input"
                       type="checkbox"
                       name="remember"
                       id="remember">
                <label class="form-check-label" for="remember">
                    Ingat saya di perangkat ini
                </label>
            </div>

            <button type="submit" class="btn btn-success w-100 rounded-pill py-2">
                Masuk
            </button>
        </form>

        <div class="text-center mt-4">
            <span class="text-muted">Belum memiliki akun?</span>
            <a href="{{ route('user.register') }}" class="text-success text-decoration-none fw-semibold">
                Daftar sebagai penumpang
            </a>
        </div>

    </div>
</div>
@endsection