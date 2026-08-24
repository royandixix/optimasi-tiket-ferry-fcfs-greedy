@extends('user.layouts.app')



@section('title', 'Edit Pemesanan')
@section('page-title', 'Edit Pemesanan Tiket')
@section(
    'page-description',
    'Pilih kembali kapal atau jadwal selama pemesanan masih menunggu proses.'
)

@section('content')

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
            <div>
                <small class="text-muted">
                    Kode Pemesanan
                </small>

                <h5 class="fw-bold mb-1">
                    {{ $pemesanan->kode_pemesanan }}
                </h5>

                <p class="text-muted small mb-0">
                    Pilih kapal atau jadwal di bawah untuk mengubah pemesanan.
                </p>
            </div>

            <div>
                <span class="badge text-bg-warning rounded-pill px-3 py-2">
                    <i class="bi bi-hourglass-split me-1"></i>
                    Menunggu Proses
                </span>
            </div>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Periksa kembali data berikut:</strong>

        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    method="POST"
    action="{{ route('user.pemesanan.update', $pemesanan) }}"
>
    @csrf
    @method('PUT')

    @if ($jadwals->count() > 0)
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-1">
                    Pilih Kapal dan Jadwal
                </h5>

                <p class="text-muted small mb-0">
                    Klik salah satu card untuk memilih jadwal keberangkatan.
                </p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            @foreach ($jadwals as $jadwal)
                @include(
                    'user.pemesanan.partials.jadwal-card',
                    [
                        'jadwal' => $jadwal,
                        'selectedJadwalId' => old(
                            'jadwal_id',
                            $pemesanan->jadwal_id
                        ),
                        'rekomendasiIds' => $rekomendasiIds ?? [],
                    ]
                )
            @endforeach
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-1">
                    Detail Pemesanan
                </h5>

                <p class="text-muted small mb-0">
                    Periksa jumlah tiket dan catatan sebelum menyimpan.
                </p>
            </div>

            <div class="card-body p-3 p-md-4">
                @include(
                    'user.pemesanan.partials.tarif-fields',
                    ['pemesanan' => $pemesanan]
                )

                <div class="mt-3">
                    <label
                        for="catatan"
                        class="form-label fw-semibold"
                    >
                        Catatan
                    </label>

                    <textarea
                        name="catatan"
                        id="catatan"
                        class="form-control @error('catatan') is-invalid @enderror"
                        rows="3"
                        placeholder="Catatan tambahan, boleh dikosongkan"
                    >{{ old('catatan', $pemesanan->catatan) }}</textarea>

                    @error('catatan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="card-footer bg-white">
                <div class="d-grid d-md-flex justify-content-md-between gap-2">
                    <a
                        href="{{ route('user.pemesanan.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        <i class="bi bi-arrow-left me-1"></i>
                        Kembali
                    </a>

                    <button
                        type="submit"
                        class="btn btn-success"
                    >
                        <i class="bi bi-save me-1"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x fs-1 text-warning"></i>

                <h5 class="mt-3">
                    Jadwal Tidak Tersedia
                </h5>

                <p class="text-muted">
                    Saat ini belum ada kapal atau jadwal keberangkatan yang tersedia.
                </p>

                <a
                    href="{{ route('user.pemesanan.index') }}"
                    class="btn btn-outline-secondary"
                >
                    Kembali
                </a>
            </div>
        </div>
    @endif
</form>

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

            const estimatedRemaining = Math.max(
                Number.parseInt(selected.dataset.estimatedRemaining || '0', 10) || 0,
                0
            );

            helper.textContent = 'Maksimal ' + capacity + ' tiket/unit dalam satu pemesanan. Perkiraan sisa berdasarkan permintaan aktif: ' + estimatedRemaining + '.';
        };

        radios.forEach(function (radio) {
            radio.addEventListener('change', refreshCapacity);
        });

        refreshCapacity();
    });
</script>

@endsection
