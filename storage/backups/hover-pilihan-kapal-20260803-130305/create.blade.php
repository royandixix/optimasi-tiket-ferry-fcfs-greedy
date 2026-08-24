@extends('user.layouts.app')


@section('title', 'Buat Pemesanan')
@section('page-title', 'Pilih Kapal Ferry')
@section(
    'page-description',
    'Pilih kapal dan jadwal keberangkatan yang tersedia, kemudian tentukan jumlah tiket.'
)

@section('content')


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
                        'selectedJadwalId' => old('jadwal_id', request('jadwal_id', $jadwals->first()?->id)),
                    ]
                )
            @endforeach
        </div>

        <div class="card border-0 shadow-sm">
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
