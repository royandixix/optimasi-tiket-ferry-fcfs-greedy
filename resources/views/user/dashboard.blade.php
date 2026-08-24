@extends('user.layouts.app')

@section('title', 'Dashboard User')
@section('page-title', 'Dashboard User')
@section('page-description', 'Pantau ringkasan pemesanan tiket ferry, status alokasi, dan jadwal keberangkatan terbaru Anda.')

@section('content')

<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-2">
                    Selamat datang, {{ $user->name }}
                </h4>

                <p class="text-muted mb-0">
                    Melalui portal ini, Anda dapat membuat pemesanan tiket kapal ferry,
                    melihat riwayat pemesanan, dan memantau status pemrosesan tiket Anda.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row gy-4 mb-4">

    <div class="col-lg-3 col-md-6">
        <div class="stats-item text-center w-100 h-100">
            <span>{{ $kapalAktifCount ?? 0 }}</span>
            <p>Kapal Aktif dari Admin</p>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-item text-center w-100 h-100">
            <span>{{ $jadwalTersediaCount ?? 0 }}</span>
            <p>Jadwal Bisa Dipesan</p>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-item text-center w-100 h-100">
            <span>{{ $totalPemesanan }}</span>
            <p>Total Pemesanan</p>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-item text-center w-100 h-100">
            <span>{{ $pending }}</span>
            <p>Menunggu Konfirmasi</p>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-item text-center w-100 h-100">
            <span>{{ $diterima }}</span>
            <p>Pemesanan Diterima</p>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-item text-center w-100 h-100">
            <span>{{ $ditolak }}</span>
            <p>Pemesanan Ditolak</p>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-item text-center w-100 h-100">
            <span class="fs-5">
                Rp{{ number_format((int) $totalNilai, 0, ',', '.') }}
            </span>
            <p>Total Nilai Pemesanan</p>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-item text-center w-100 h-100">
            <span class="fs-5">
                Rp{{ number_format((int) ($nilaiDiterima ?? 0), 0, ',', '.') }}
            </span>
            <p>Nilai Tiket Diterima</p>
        </div>
    </div>

</div>

@if (($kapalAktifCount ?? 0) > 0 && ($jadwalTersediaCount ?? 0) === 0)
    <div class="alert bg-warning-subtle border border-warning-subtle rounded-4 mb-4">
        <div class="d-flex gap-3 align-items-start">
            <i class="bi bi-info-circle fs-4 text-warning-emphasis"></i>
            <div>
                <strong>Data kapal admin sudah terbaca.</strong>
                <div class="small text-muted mt-1">
                    Saat ini belum ada jadwal keberangkatan yang bisa dipesan untuk hari ini atau tanggal berikutnya. Kapal tetap dapat dilihat di menu Pemesanan; admin hanya perlu membuat atau memperbarui jadwal keberangkatannya.
                </div>
                <a href="{{ route('user.pemesanan.index', ['rekomendasi' => 1]) }}" class="btn btn-sm btn-outline-success rounded-pill mt-2">
                    Lihat Data Kapal
                </a>
            </div>
        </div>
    </div>
@endif

<div class="row gy-4">

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-1">Pemesanan Terbaru</h5>
                        <p class="text-muted mb-0">
                            Daftar pemesanan tiket terakhir dari akun Anda.
                        </p>
                    </div>

                    <a href="{{ route('user.pemesanan.index', ['rekomendasi' => 1]) }}" class="btn btn-success btn-sm rounded-pill">
                        Lihat Semua
                    </a>
                </div>

                @if ($pemesananTerbaru->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Jadwal</th>
                                    <th>Jenis Tarif</th>
                                    <th>Jumlah</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pemesananTerbaru as $pemesanan)
                                    <tr>
                                        <td>
                                            <strong>{{ $pemesanan->kode_pemesanan }}</strong>
                                        </td>
                                        <td>
                                            <div>
                                                {{ optional($pemesanan->jadwal)->tanggal_berangkat ?? '-' }}
                                                {{ optional($pemesanan->jadwal)->jam_berangkat ?? '' }}
                                            </div>
                                            <small class="text-muted">
                                                {{ optional(optional($pemesanan->jadwal)->rute)->pelabuhan_asal ?? '-' }}
                                                -
                                                {{ optional(optional($pemesanan->jadwal)->rute)->pelabuhan_tujuan ?? '-' }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge text-bg-info">
                                                {{ $pemesanan->tarif_label ?? 'Penumpang - Dewasa' }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $pemesanan->jumlah_tiket }} {{ $pemesanan->satuan ?: 'unit' }}
                                        </td>
                                        <td class="fw-semibold text-success">
                                            Rp{{ number_format((float) $pemesanan->total_harga, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @if ($pemesanan->status_pemesanan === 'pending')
                                                <span class="badge text-bg-warning">Menunggu</span>
                                            @elseif ($pemesanan->status_pemesanan === 'diterima')
                                                <span class="badge text-bg-success">Diterima</span>
                                            @elseif ($pemesanan->status_pemesanan === 'ditolak')
                                                <span class="badge text-bg-danger">Ditolak</span>
                                            @else
                                                <span class="badge text-bg-secondary">
                                                    {{ ucfirst($pemesanan->status_pemesanan) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-ticket-perforated display-4 text-success"></i>
                        <h5 class="mt-3">Belum Ada Pemesanan</h5>
                        <p class="text-muted">
                            Anda belum memiliki data pemesanan tiket.
                            Silakan buat pemesanan baru untuk memulai.
                        </p>
                        <a href="{{ route('user.pemesanan.create') }}" class="btn btn-success rounded-pill">
                            Buat Pemesanan
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="mb-3">Data Penumpang</h5>

                <p class="mb-2">
                    <strong>Nama:</strong><br>
                    {{ $penumpang->nama_penumpang ?? $user->name }}
                </p>

                <p class="mb-2">
                    <strong>NIK:</strong><br>
                    {{ $penumpang->nik ?? '-' }}
                </p>

                <p class="mb-2">
                    <strong>No. HP:</strong><br>
                    {{ $penumpang->no_hp ?? '-' }}
                </p>

                <p class="mb-0">
                    <strong>Alamat:</strong><br>
                    {{ $penumpang->alamat ?? '-' }}
                </p>

                <div class="mt-4">
                    <a href="{{ route('user.profil.edit') }}" class="btn btn-outline-success w-100 rounded-pill">
                        Lengkapi Profil
                    </a>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="mb-3">Aksi Cepat</h5>

                <div class="d-grid gap-2">
                    <a href="{{ route('user.pemesanan.create') }}" class="btn btn-success rounded-pill">
                        <i class="bi bi-plus-circle me-1"></i>
                        Buat Pemesanan Tiket
                    </a>

                    <a href="{{ route('user.pemesanan.index', ['rekomendasi' => 1]) }}" class="btn btn-outline-success rounded-pill">
                        <i class="bi bi-list-check me-1"></i>
                        Riwayat Pemesanan
                    </a>

                    <a href="{{ route('user.profil.edit') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-person-circle me-1"></i>
                        Profil Saya
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
