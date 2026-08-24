@php
    $kapal = $jadwal->kapal;
    $rute = $jadwal->rute;
    $gambarKapal = collect($kapal?->gambar_kapal ?? [])->filter()->values();
    $gambarUtama = $gambarKapal->first();
    $gambarPath = $gambarUtama
        ? ltrim(preg_replace('#^(public/|storage/)#', '', $gambarUtama), '/')
        : null;
    $gambarUrl = $gambarPath ? asset('storage/' . $gambarPath) : null;

    $kapasitasTotal = max((int) ($jadwal->kapasitas_total ?? 0), 0);
    $perkiraanSisa = max(
        (int) ($jadwal->rekomendasi_sisa ?? $jadwal->sisa_kapasitas ?? $kapasitasTotal),
        0
    );
    $persenKosong = (float) ($jadwal->rekomendasi_persen_kosong ?? 0);
    $labelKepadatan = $jadwal->rekomendasi_label ?? 'Tersedia';
    $levelKepadatan = $jadwal->rekomendasi_level ?? 'success';

    $jadwalTerpilih = (string) ($selectedJadwalId ?? '') === (string) $jadwal->id;
@endphp

<div class="col-12 col-md-6 col-xl-4">
    <input
        type="radio"
        class="btn-check jadwal-radio"
        name="jadwal_id"
        id="jadwal-{{ $jadwal->id }}"
        value="{{ $jadwal->id }}"
        data-capacity="{{ $kapasitasTotal }}"
        data-estimated-remaining="{{ $perkiraanSisa }}"
        data-percent-empty="{{ $persenKosong }}"
        data-route-id="{{ $jadwal->rute_id }}"
        data-departure-date="{{ optional($jadwal->tanggal_berangkat)->format('Y-m-d') ?? '' }}"
        data-departure-time="{{ $jadwal->jam_berangkat ? substr((string) $jadwal->jam_berangkat, 0, 5) : '' }}"
        data-ship-name="{{ $kapal?->nama_kapal ?? 'Kapal Ferry' }}"
        data-route-label="{{ ($rute?->pelabuhan_asal ?? '-') . ' → ' . ($rute?->pelabuhan_tujuan ?? '-') }}"
        autocomplete="off"
        @checked($jadwalTerpilih)
        required
    >

    <label
        for="jadwal-{{ $jadwal->id }}"
        class="btn btn-outline-success kapal-option-card w-100 h-100 p-0 overflow-hidden text-start shadow-sm"
    >
        <div class="ratio ratio-16x9 bg-light position-relative">
            @if ($gambarUrl)
                <img
                    src="{{ $gambarUrl }}"
                    alt="{{ $kapal?->nama_kapal ?? 'Kapal Ferry' }}"
                    class="w-100 h-100 object-fit-cover"
                    loading="lazy"
                >
            @else
                <div class="d-flex flex-column align-items-center justify-content-center bg-light text-secondary">
                    <i class="bi bi-image fs-1"></i>
                    <small>Gambar kapal belum tersedia</small>
                </div>
            @endif

            <div class="position-absolute top-0 start-0 p-2">
                <span class="badge text-bg-dark">
                    <i class="bi bi-images me-1"></i>{{ $gambarKapal->count() }} Foto
                </span>
            </div>
        </div>

        <div class="p-3 bg-white text-dark">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                <div class="overflow-hidden">
                    <small class="text-success fw-semibold">
                        {{ $kapal?->kode_kapal ?? 'KAPAL' }}
                    </small>
                    <h6 class="fw-bold text-truncate mb-1">
                        {{ $kapal?->nama_kapal ?? 'Nama Kapal' }}
                    </h6>
                </div>
            </div>

            <div class="small text-muted text-truncate mb-3">
                <i class="bi bi-geo-alt me-1"></i>{{ $rute?->pelabuhan_asal ?? '-' }}
                <i class="bi bi-arrow-right mx-1"></i>{{ $rute?->pelabuhan_tujuan ?? '-' }}
            </div>

            <div class="row g-2">
                <div class="col-6">
                    <div class="border rounded p-2 h-100">
                        <small class="text-muted d-block">
                            <i class="bi bi-calendar3 me-1"></i>Tanggal
                        </small>
                        <span class="small fw-semibold">
                            {{ optional($jadwal->tanggal_berangkat)->format('d M Y') ?? '-' }}
                        </span>
                    </div>
                </div>

                <div class="col-6">
                    <div class="border rounded p-2 h-100">
                        <small class="text-muted d-block">
                            <i class="bi bi-clock me-1"></i>Jam
                        </small>
                        <span class="small fw-semibold">
                            {{ $jadwal->jam_berangkat ? substr((string) $jadwal->jam_berangkat, 0, 5) : '-' }}
                        </span>
                    </div>
                </div>

                <div class="col-6">
                    <div class="border rounded p-2 h-100">
                        <small class="text-muted d-block">
                            <i class="bi bi-ticket-perforated me-1"></i>Sisa Tiket
                        </small>
                        <span class="small fw-semibold">
                            {{ number_format($perkiraanSisa) }} / {{ number_format($kapasitasTotal) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="alert alert-success py-2 px-3 small text-center mb-0 mt-3">
                <i class="bi bi-hand-index-thumb me-1"></i>Klik untuk memilih jadwal
            </div>
        </div>
    </label>
</div>
