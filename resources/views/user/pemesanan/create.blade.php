@extends('user.layouts.app')

@section('title', 'Buat Pemesanan')
@section(
    'page-title',
    request('jadwal_id') ? 'Detail Pemesanan' : 'Pilih Kapal Ferry'
)
@section(
    'page-description',
    request('jadwal_id')
        ? 'Lengkapi detail tiket Anda.'
        : 'Pilih kapal dan jadwal keberangkatan yang tersedia.'
)

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="mb-1">
            {{ request('jadwal_id') ? 'Detail Tiket' : 'Pilih Tiket Ferry' }}
        </h3>
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
    <form method="POST" action="{{ route('user.pemesanan.store') }}" id="booking-form" enctype="multipart/form-data">
        @csrf

        {{--
            Daftar jadwal tetap berada di DOM agar fitur rekomendasi dapat
            membandingkan jadwal lain. Jika user datang setelah menekan
            "Pesan Kapal Ini", daftar ini disembunyikan dan user langsung
            melihat detail pemesanan.
        --}}
        <div
            class="row g-4 mb-5 {{ request('jadwal_id') ? 'd-none' : '' }}"
            id="daftar-jadwal"
        >
            @foreach ($jadwals as $jadwal)
                @include(
                    'user.pemesanan.partials.jadwal-card',
                    [
                        'jadwal' => $jadwal,
                        'selectedJadwalId' => old('jadwal_id', request('jadwal_id')),
                        'rekomendasiIds' => [],
                    ]
                )
            @endforeach
        </div>

        @if (request('jadwal_id'))
            <div class="card border-success shadow-sm rounded-4 mb-4" id="selected-ticket-summary">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <span class="badge text-bg-success rounded-pill mb-2">
                                <i class="bi bi-ticket-perforated me-1"></i>
                                Tiket Dipilih
                            </span>

                            <h4 class="fw-bold mb-1" id="summary-ship-name">Memuat...</h4>
                            <div class="text-muted" id="summary-route">-</div>
                        </div>

                        <div class="text-md-end">
                            <div class="small text-muted">Keberangkatan</div>
                            <div class="fw-semibold" id="summary-time">-</div>

                            <div class="small text-muted mt-2">Sisa Tiket</div>
                            <div class="fw-bold text-success" id="summary-remaining">-</div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a
                            href="{{ route('user.pemesanan.create') }}"
                            class="btn btn-sm btn-outline-secondary rounded-pill"
                        >
                            <i class="bi bi-arrow-left-right me-1"></i>
                            Ganti Tiket
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center mb-4">
                    <div class="col-lg-8">
                        <h4 class="mb-1">Lengkapi Pemesanan</h4>
                        <p class="text-muted mb-0">
                            Pilih jenis tiket, jumlah tiket, lalu buat pemesanan.
                        </p>
                    </div>
                </div>

                <div class="alert alert-light border rounded-3 small" id="schedule-capacity-helper">
                    <i class="bi bi-ticket-perforated me-1"></i>
                    Pilih jadwal terlebih dahulu.
                </div>

                <div class="row g-4">
                    <div class="col-12">
                        @include('user.pemesanan.partials.tarif-fields')
                    </div>

                    @include('user.pemesanan.partials.payment-fields')

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
                        id="submit-booking"
                    >
                        <i class="bi bi-check-circle me-1"></i>
                        Buat Pemesanan
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- Rekomendasi hanya muncul sebagai modal setelah tiket/jadwal diklik. --}}
    @include('user.pemesanan.components.rekomendasi-pilihan-modal')
@else
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body text-center py-5">
            <i class="bi bi-calendar-x display-3 text-warning"></i>
            <h4 class="mt-3">Belum Ada Tiket yang Tersedia</h4>

            <a
                href="{{ route('user.pemesanan.index') }}"
                class="btn btn-outline-secondary rounded-pill mt-2"
            >
                Kembali
            </a>
        </div>
    </div>
@endif

<style>
    label.kapal-option-card {
        position: relative;
        cursor: pointer;
        transition:
            transform 0.18s ease,
            box-shadow 0.18s ease,
            border-color 0.18s ease,
            background-color 0.18s ease;
    }

    label.kapal-option-card:hover {
        transform: translateY(-3px);
        border-color: rgba(25, 135, 84, 0.65) !important;
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.12) !important;
    }

    input.jadwal-radio:checked + label.kapal-option-card {
        transform: translateY(-3px);
        border: 2px solid #198754 !important;
        background-color: rgba(25, 135, 84, 0.035);
        box-shadow:
            0 0 0 0.25rem rgba(25, 135, 84, 0.14),
            0 0.75rem 1.5rem rgba(0, 0, 0, 0.12) !important;
    }

    input.jadwal-radio:checked + label.kapal-option-card::after {
        content: "✓ Dipilih";
        position: absolute;
        right: 12px;
        bottom: 12px;
        z-index: 10;
        padding: 6px 12px;
        border-radius: 999px;
        background: #198754;
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
        pointer-events: none;
    }

    label.kapal-option-card:focus-visible {
        outline: 3px solid rgba(25, 135, 84, 0.35);
        outline-offset: 3px;
    }

    @media (prefers-reduced-motion: reduce) {
        label.kapal-option-card {
            transition: none;
        }

        label.kapal-option-card:hover,
        input.jadwal-radio:checked + label.kapal-option-card {
            transform: none;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const radios = Array.from(
        document.querySelectorAll('input.jadwal-radio[name="jadwal_id"]')
    );
    const quantityInput = document.getElementById('jumlah_tiket');
    const helper = document.getElementById('capacity-helper');
    const scheduleHelper = document.getElementById('schedule-capacity-helper');
    const summaryShipName = document.getElementById('summary-ship-name');
    const summaryRoute = document.getElementById('summary-route');
    const summaryTime = document.getElementById('summary-time');
    const summaryRemaining = document.getElementById('summary-remaining');

    const comparisonModalElement = document.getElementById('rekomendasiPilihanModal');
    const comparisonModal = comparisonModalElement && window.bootstrap
        ? bootstrap.Modal.getOrCreateInstance(comparisonModalElement)
        : null;
    const numberFormatter = new Intl.NumberFormat('id-ID');

    let recommendedScheduleId = null;
    let suppressComparison = false;

    const scheduleData = function (radio) {
        return {
            id: String(radio.value),
            routeId: String(radio.dataset.routeId || ''),
            date: String(radio.dataset.departureDate || ''),
            time: String(radio.dataset.departureTime || ''),
            shipName: String(radio.dataset.shipName || 'Kapal Ferry'),
            routeLabel: String(radio.dataset.routeLabel || '-'),
            capacity: Math.max(Number.parseInt(radio.dataset.capacity || '0', 10) || 0, 0),
            remaining: Math.max(Number.parseInt(radio.dataset.estimatedRemaining || '0', 10) || 0, 0),
            percentEmpty: Math.max(Number.parseFloat(radio.dataset.percentEmpty || '0') || 0, 0),
        };
    };

    const formatScheduleDate = function (date, time) {
        let dateLabel = date || '-';

        if (/^\d{4}-\d{2}-\d{2}$/.test(date)) {
            const parts = date.split('-');
            const monthNames = [
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
            ];
            dateLabel = Number(parts[2]) + ' ' + monthNames[Number(parts[1]) - 1] + ' ' + parts[0];
        }

        return dateLabel + (time ? ' · ' + time : '');
    };

    const updateSelectedSummary = function (radio) {
        if (! radio || ! summaryShipName) {
            return;
        }

        const data = scheduleData(radio);
        summaryShipName.textContent = data.shipName || '-';
        summaryRoute.textContent = data.routeLabel || '-';
        summaryTime.textContent = formatScheduleDate(data.date, data.time);
        summaryRemaining.textContent =
            numberFormatter.format(data.remaining)
            + ' / '
            + numberFormatter.format(data.capacity);
    };

    const synchronizeState = function () {
        radios.forEach(function (radio) {
            const label = radio.id
                ? document.querySelector('label[for="' + radio.id + '"]')
                : null;

            if (label) {
                label.setAttribute('aria-checked', radio.checked ? 'true' : 'false');
            }
        });
    };

    const refreshCapacity = function () {
        const selected = document.querySelector('.jadwal-radio:checked');

        if (! selected || ! quantityInput) {
            return;
        }

        const capacity = Math.max(
            Number.parseInt(selected.dataset.capacity || '1', 10) || 1,
            1
        );
        const estimatedRemaining = Math.max(
            Number.parseInt(selected.dataset.estimatedRemaining || '0', 10) || 0,
            0
        );

        quantityInput.max = capacity;

        if (Number.parseInt(quantityInput.value || '1', 10) > capacity) {
            quantityInput.value = capacity;
            quantityInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        if (helper) {
            helper.textContent = 'Sisa tiket: ' + estimatedRemaining + ' dari ' + capacity + '.';
        }

        if (scheduleHelper) {
            scheduleHelper.innerHTML = '<i class="bi bi-ticket-perforated me-1"></i>'
                + 'Sisa tiket <strong>' + estimatedRemaining + '</strong> dari <strong>' + capacity + '</strong>.';
        }
    };

    const findBetterAlternative = function (selectedRadio) {
        const selected = scheduleData(selectedRadio);
        const candidates = radios
            .filter(function (radio) {
                if (radio === selectedRadio) {
                    return false;
                }

                const candidate = scheduleData(radio);

                if (! selected.routeId || candidate.routeId !== selected.routeId) {
                    return false;
                }

                return candidate.remaining > 0
                    && candidate.percentEmpty > selected.percentEmpty;
            })
            .map(scheduleData)
            .sort(function (a, b) {
                const aSameDate = a.date === selected.date ? 1 : 0;
                const bSameDate = b.date === selected.date ? 1 : 0;

                if (aSameDate !== bSameDate) {
                    return bSameDate - aSameDate;
                }

                if (a.percentEmpty !== b.percentEmpty) {
                    return b.percentEmpty - a.percentEmpty;
                }

                if (a.remaining !== b.remaining) {
                    return b.remaining - a.remaining;
                }

                return (a.date + ' ' + a.time).localeCompare(b.date + ' ' + b.time);
            });

        return candidates.length > 0 ? candidates[0] : null;
    };

    const putText = function (id, value) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    };

    const showComparisonRecommendation = function (selectedRadio) {
        if (! comparisonModal || ! selectedRadio) {
            return;
        }

        const selected = scheduleData(selectedRadio);
        const alternative = findBetterAlternative(selectedRadio);
        const modalTitle = document.getElementById('rekomendasiPilihanModalLabel');
        const badge = document.getElementById('rekomendasiBadge');
        const keepButton = document.getElementById('tetapPilihanAwal');
        const useButtonText = document.getElementById('gunakanJadwalRekomendasiText');

        putText('pilihanAwalNamaKapal', selected.shipName);
        putText('pilihanAwalRute', selected.routeLabel);
        putText('pilihanAwalWaktu', formatScheduleDate(selected.date, selected.time));
        putText(
            'pilihanAwalSisa',
            numberFormatter.format(selected.remaining) + ' / ' + numberFormatter.format(selected.capacity)
        );

        if (alternative) {
            recommendedScheduleId = alternative.id;

            if (modalTitle) {
                modalTitle.textContent = 'Ada tiket yang lebih banyak kosong';
            }
            if (badge) {
                badge.innerHTML = '<i class="bi bi-stars me-1"></i>Lebih Banyak Kosong';
            }
            if (keepButton) {
                keepButton.classList.remove('d-none');
            }
            if (useButtonText) {
                useButtonText.textContent = 'Pilih yang Lebih Kosong';
            }

            putText('pilihanRekomendasiNamaKapal', alternative.shipName);
            putText('pilihanRekomendasiRute', alternative.routeLabel);
            putText('pilihanRekomendasiWaktu', formatScheduleDate(alternative.date, alternative.time));
            putText(
                'pilihanRekomendasiSisa',
                numberFormatter.format(alternative.remaining) + ' / ' + numberFormatter.format(alternative.capacity)
            );
        } else {
            recommendedScheduleId = selected.id;

            if (modalTitle) {
                modalTitle.textContent = 'Tiket ini masih tersedia';
            }
            if (badge) {
                badge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Tersedia';
            }
            if (keepButton) {
                keepButton.classList.add('d-none');
            }
            if (useButtonText) {
                useButtonText.textContent = 'Lanjutkan Tiket Ini';
            }

            putText('pilihanRekomendasiNamaKapal', selected.shipName);
            putText('pilihanRekomendasiRute', selected.routeLabel);
            putText('pilihanRekomendasiWaktu', formatScheduleDate(selected.date, selected.time));
            putText(
                'pilihanRekomendasiSisa',
                numberFormatter.format(selected.remaining) + ' / ' + numberFormatter.format(selected.capacity)
            );
        }

        comparisonModal.show();
    };

    const selectSchedule = function (radio, showRecommendation) {
        if (! radio) {
            return;
        }

        suppressComparison = ! showRecommendation;
        radio.checked = true;
        radio.dispatchEvent(new Event('change', { bubbles: true }));
    };

    radios.forEach(function (radio) {
        const label = radio.id
            ? document.querySelector('label[for="' + radio.id + '"]')
            : null;

        if (label) {
            label.setAttribute('tabindex', '0');
            label.setAttribute('role', 'radio');

            label.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter' && event.key !== ' ') {
                    return;
                }

                event.preventDefault();
                selectSchedule(radio, true);
            });
        }

        radio.addEventListener('change', function () {
            synchronizeState();
            refreshCapacity();
            updateSelectedSummary(radio);

            if (suppressComparison) {
                suppressComparison = false;
                return;
            }

            showComparisonRecommendation(radio);
        });
    });

    synchronizeState();
    refreshCapacity();

    const initialSelectedRadio = document.querySelector('.jadwal-radio:checked');
    if (initialSelectedRadio) {
        updateSelectedSummary(initialSelectedRadio);
    }

    // Hanya muncul saat user memang datang dari klik tombol tiket/kapal.
    const recommendationParams = new URLSearchParams(window.location.search);
    if (recommendationParams.get('cek_rekomendasi') === '1') {
        const selectedFromClick = document.querySelector('.jadwal-radio:checked');

        if (selectedFromClick) {
            window.setTimeout(function () {
                showComparisonRecommendation(selectedFromClick);
            }, 180);
        }

        recommendationParams.delete('cek_rekomendasi');
        const cleanQuery = recommendationParams.toString();
        const cleanUrl = window.location.pathname
            + (cleanQuery ? '?' + cleanQuery : '')
            + window.location.hash;
        window.history.replaceState({}, document.title, cleanUrl);
    }

    const useRecommendedButton = document.getElementById('gunakanJadwalRekomendasi');
    if (useRecommendedButton) {
        useRecommendedButton.addEventListener('click', function () {
            if (! recommendedScheduleId) {
                return;
            }

            const radio = document.getElementById('jadwal-' + recommendedScheduleId);
            if (! radio) {
                return;
            }

            selectSchedule(radio, false);
            if (comparisonModal) {
                comparisonModal.hide();
            }

            updateSelectedSummary(radio);
            refreshCapacity();
        });
    }
});
</script>

@endsection
