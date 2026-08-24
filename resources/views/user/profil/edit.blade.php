@extends('user.layouts.app')

@section('title', 'Profil Penumpang')
@section('page-title', 'Profil Penumpang')
@section('page-description', 'Perbarui data akun dan informasi penumpang agar proses pemesanan tiket berjalan lebih mudah.')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">

                <div class="mb-4">
                    <span class="badge text-bg-success rounded-pill mb-3">
                        <i class="bi bi-person-circle me-1"></i>
                        Data Akun Penumpang
                    </span>

                    <h4 class="mb-2">Ubah Profil</h4>
                    <p class="text-muted mb-0">
                        Lengkapi dan perbarui data diri Anda. Data ini digunakan untuk mendukung proses pemesanan tiket kapal ferry.
                    </p>
                </div>

                <form method="POST" action="{{ route('user.profil.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name', $user->name) }}"
                                   class="form-control"
                                   placeholder="Masukkan nama lengkap"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Alamat Email</label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="form-control"
                                   placeholder="Masukkan alamat email"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text"
                                   name="nik"
                                   value="{{ old('nik', $penumpang->nik) }}"
                                   class="form-control"
                                   placeholder="Masukkan NIK">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="">Pilih jenis kelamin</option>
                                <option value="L" @selected(old('jenis_kelamin', $penumpang->jenis_kelamin) === 'L')>
                                    Laki-laki
                                </option>
                                <option value="P" @selected(old('jenis_kelamin', $penumpang->jenis_kelamin) === 'P')>
                                    Perempuan
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor HP</label>
                            <input type="text"
                                   name="no_hp"
                                   value="{{ old('no_hp', $penumpang->no_hp) }}"
                                   class="form-control"
                                   placeholder="Contoh: 081234567890">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status Akun</label>
                            <input type="text"
                                   value="{{ ucfirst($user->status) }}"
                                   class="form-control"
                                   disabled>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Masukkan alamat lengkap">{{ old('alamat', $penumpang->alamat) }}</textarea>
                        </div>

                    </div>

                    <hr class="my-4">

                    <div class="mb-4">
                        <h5 class="mb-1">Ubah Kata Sandi</h5>
                        <p class="text-muted mb-0">
                            Kosongkan bagian ini jika Anda tidak ingin mengganti kata sandi.
                        </p>
                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kata Sandi Baru</label>
                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   placeholder="Minimal 8 karakter">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="form-control"
                                   placeholder="Ulangi kata sandi baru">
                        </div>

                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
                            Kembali
                        </a>

                        <button type="submit" class="btn btn-success rounded-pill">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection
