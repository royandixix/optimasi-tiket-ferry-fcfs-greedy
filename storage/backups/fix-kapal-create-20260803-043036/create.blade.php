@extends('user.layouts.app')



@section('title', 'Buat Pemesanan')
@section('page-title', 'Pilih Kapal Ferry')
@section(
    'page-description',
    'Pilih kapal dan jadwal keberangkatan yang tersedia, kemudian tentukan jumlah tiket.'
)

@section('content')

<style>
    .kapal-option-card {
        border-radius: 22px;
        overflow: hidden;
        cursor: pointer;
        transition: .25s ease;
        border: 2px solid transparent !important;
    }

    .kapal-option-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 40px rgba(0, 0, 0, .12) !important;
    }

    .btn-check:checked + .kapal-option-card {
        border-color: #198754 !important;
        box-shadow: 0 18px 40px rgba(25, 135, 84, .22) !important;
        transform: translateY(-4px);
    }

    .kapal-image-wrapper {
        position: relative;
        height: 260px;
        overflow: hidden;
        background: #e9ecef;
    }

    .kapal-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .35s ease;
    }

    .kapal-option-card:hover .kapal-image {
        transform: scale(1.04);
    }

    .kapal-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
        background: linear-gradient(135deg, #198754, #0d6efd);
    }

    .kapal-image-placeholder i {
        font-size: 58px;
        margin-bottom: 10px;
    }

    .kapal-photo-count {
        position: absolute;
        top: 16px;
        left: 16px;
        padding: 9px 13px;
    }

    .kapal-selected-indicator {
        position: absolute;
        top: 16px;
        right: 16px;
        background: #198754;
        color: white;
        border-radius: 50rem;
        padding: 9px 14px;
        font-size: 13px;
        font-weight: 600;
        opacity: 0;
        transform: translateY(-6px);
        transition: .2s ease;
    }

    .btn-check:checked + .kapal-option-card .kapal-selected-indicator {
        opacity: 1;
        transform: translateY(0);
    }

    .kapal-info-box {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 12px;
        height: 100%;
        border-radius: 14px;
        background: #f7f9fa;
    }

    .kapal-info-box i {
        color: #198754;
        font-size: 20px;
    }

    .kapal-info-box small,
    .kapal-info-box strong {
        display: block;
    }

    .kapal-info-box small {
        color: #6c757d;
        font-size: 11px;
    }

    .pilih-kapal-text {
        color: #198754;
        font-weight: 600;
        padding: 11px;
        border-radius: 12px;
        background: rgba(25, 135, 84, .08);
    }

    .booking-form-card {
        border-radius: 22px;
    }

    @media (max-width: 767px) {
        .kapal-image-wrapper {
            height: 220px;
        }
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="mb-1">Kapal dan Jadwal Tersedia</h3>
        <p class="text-muted mb-0">
            Klik salah satu card kapal untuk menentukan keberangkatan.
        </p>
    </div>

    <a
        href="{{ route('user.pemesanan.index') }}"
        class="btn btn-outline-secondary rounded-pill"
    >
        <i class="bi bi-arrow-left me-1"></i>
        Riwayat Pemesanan
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger rounded-4">
        <strong>Periksa kembali data berikut:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if ($jadwals->count() > 0)
    <form method="POST" action="{{ route('user.pemesanan.store') }}">
        @csrf

        <div class="row g-4 mb-5">
            @foreach ($jadwals as $jadwal)
                @include(
                    'user.pemesanan.partials.jadwal-card',
                    [
                        'jadwal' => $jadwal,
                        'selectedJadwalId' => old('jadwal_id'),
                    ]
                )
            @endforeach
        </div>

        <div class="card border-0 shadow-sm booking-form-card">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center mb-4">
                    <div class="col-lg-8">
                        <h4 class="mb-1">Detail Pemesanan</h4>
                        <p class="text-muted mb-0">
                            Pilih jenis tarif dan jumlah tiket atau unit. Total checkout dihitung otomatis.
                        </p>
                    </div>
                </div>

                <div class="row g-4">
                                        <div class="col-12">
                        @include('user.pemesanan.partials.tarif-fields')
                    </div>

                    <div class="col-12">
                        <label for="catatan" class="form-label fw-semibold">
                            Catatan
                        </label>

                        <textarea
                            name="catatan"
                            id="catatan"
                            class="form-control"
                            rows="4"
                            placeholder="Contoh: Membawa barang tambahan atau kebutuhan khusus"
                        >{{ old('catatan') }}</textarea>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-between gap-3 mt-4">
                    <a
                        href="{{ route('user.pemesanan.index') }}"
                        class="btn btn-outline-secondary rounded-pill px-4"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn btn-success rounded-pill px-5"
                    >
                        <i class="bi bi-check-circle me-1"></i>
                        Buat Pemesanan
                    </button>
                </div>
            </div>
        </div>
    </form>
@else
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body text-center py-5">
            <i class="bi bi-calendar-x display-3 text-warning"></i>

            <h4 class="mt-3">Belum Ada Kapal yang Tersedia</h4>

            <p class="text-muted">
                Saat ini belum ada jadwal keberangkatan yang dapat dipesan.
            </p>

            <a
                href="{{ route('user.pemesanan.index') }}"
                class="btn btn-outline-secondary rounded-pill"
            >
                Kembali
            </a>
        </div>
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('.jadwal-radio');
        const quantityInput = document.getElementById('jumlah_tiket');
        const helper = document.getElementById('capacity-helper');

        const refreshCapacity = function () {
            const selected = document.querySelector('.jadwal-radio:checked');

            if (! selected || ! quantityInput || ! helper) {
                return;
            }

            const capacity = Math.max(
                Number.parseInt(selected.dataset.capacity || '1', 10) || 1,
                1
            );

            quantityInput.max = capacity;

            if (Number.parseInt(quantityInput.value || '1', 10) > capacity) {
                quantityInput.value = capacity;
                quantityInput.dispatchEvent(new Event('input', { bubbles: true }));
            }

            helper.textContent = 'Maksimal ' + capacity + ' tiket atau unit sesuai sisa kapasitas.';
        };

        radios.forEach(function (radio) {
            radio.addEventListener('change', refreshCapacity);
        });

        refreshCapacity();
    });
</script>

@endsection
