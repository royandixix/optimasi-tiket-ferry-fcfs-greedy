@php
    $catalogContext = $catalogContext ?? 'index';
@endphp

@if (($kapalAktifs ?? collect())->isNotEmpty())
    <div class="row g-4">
        @foreach ($kapalAktifs as $kapalCatalog)
            @php
                $jadwalKapal = collect(($jadwalsByKapal ?? collect())->get($kapalCatalog->id, collect()))->values();
                $jadwalTerdekat = $jadwalKapal->first();
                $ruteTerdekat = $jadwalTerdekat?->rute;
                $jumlahJadwalAktif = $jadwalKapal->count();

                $gambarKapal = collect($kapalCatalog->gambar_kapal ?? [])->filter()->values();
                $gambarUtama = $gambarKapal->first();
                $gambarPath = $gambarUtama
                    ? ltrim(preg_replace('#^(public/|storage/)#', '', $gambarUtama), '/')
                    : null;
                $gambarUrl = $gambarPath ? asset('storage/' . $gambarPath) : null;

                $isFocused = (string) request('kapal_id') === (string) $kapalCatalog->id;
            @endphp

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden {{ $isFocused ? 'ring-kapal-terpilih' : '' }}">
                    <div class="ratio ratio-16x9 bg-light position-relative">
                        @if ($gambarUrl)
                            <img
                                src="{{ $gambarUrl }}"
                                alt="{{ $kapalCatalog->nama_kapal }}"
                                class="w-100 h-100 object-fit-cover"
                                loading="lazy"
                            >
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center text-secondary">
                                <i class="bi bi-ship fs-1"></i>
                                <small>Foto kapal belum tersedia</small>
                            </div>
                        @endif

                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge text-bg-success rounded-pill">
                                <i class="bi bi-check-circle me-1"></i>Kapal Aktif
                            </span>
                        </div>

                        <div class="position-absolute top-0 end-0 p-2">
                            <span class="badge text-bg-dark rounded-pill">
                                <i class="bi bi-people me-1"></i>{{ number_format((int) $kapalCatalog->kapasitas_penumpang) }} kapasitas
                            </span>
                        </div>
                    </div>

                    <div class="card-body d-flex flex-column p-3">
                        <small class="text-success fw-semibold mb-1">
                            {{ $kapalCatalog->kode_kapal ?: 'KAPAL' }}
                        </small>

                        <h5 class="fw-bold mb-2">
                            {{ $kapalCatalog->nama_kapal }}
                        </h5>

                        @if ($jadwalTerdekat)
                            <div class="alert bg-success-subtle border border-success-subtle rounded-3 small mb-3">
                                <div class="fw-semibold text-success mb-1">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    {{ $jumlahJadwalAktif }} jadwal dapat dipesan
                                </div>
                                <div>
                                    {{ $ruteTerdekat?->pelabuhan_asal ?? '-' }}
                                    <i class="bi bi-arrow-right mx-1"></i>
                                    {{ $ruteTerdekat?->pelabuhan_tujuan ?? '-' }}
                                </div>
                                <div class="mt-1">
                                    {{ optional($jadwalTerdekat->tanggal_berangkat)->format('d M Y') ?? '-' }}
                                    &middot;
                                    {{ $jadwalTerdekat->jam_berangkat ? substr((string) $jadwalTerdekat->jam_berangkat, 0, 5) : '-' }}
                                </div>
                            </div>
                        @else
                        @endif

                        <div class="mt-auto">
                            @if ($jadwalTerdekat)
                                @if ($catalogContext === 'create')
                                    <a
                                        href="{{ route('user.pemesanan.create', ['kapal_id' => $kapalCatalog->id, 'jadwal_id' => $jadwalTerdekat->id, 'cek_rekomendasi' => 1]) }}#daftar-jadwal"
                                        class="btn btn-outline-success w-100 rounded-pill"
                                    >
                                        <i class="bi bi-calendar2-check me-1"></i>
                                        Pilih Jadwal Kapal Ini
                                    </a>
                                @else
                                    <a
                                        href="{{ route('user.pemesanan.create', ['kapal_id' => $kapalCatalog->id, 'jadwal_id' => $jadwalTerdekat->id, 'cek_rekomendasi' => 1]) }}"
                                        class="btn btn-success w-100 rounded-pill"
                                    >
                                        <i class="bi bi-ticket-perforated me-1"></i>
                                        Pesan Kapal Ini
                                    </a>
                                @endif
                            @else
                                <button type="button" class="btn btn-outline-secondary w-100 rounded-pill" disabled>
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    Menunggu Jadwal Admin
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="alert alert-warning rounded-4 mb-0">
        <div class="d-flex gap-3 align-items-start">
            <i class="bi bi-exclamation-triangle fs-4"></i>
            <div>
                <strong>Belum ada kapal aktif dari admin.</strong>
                <div class="small mt-1">
                    Setelah admin menambahkan kapal dengan status <strong>Aktif</strong>, data kapal akan muncul di halaman user ini secara otomatis pada saat halaman dibuka atau direfresh.
                </div>
            </div>
        </div>
    </div>
@endif

@once
    <style>
        .ring-kapal-terpilih {
            outline: 3px solid rgba(25, 135, 84, 0.22);
            outline-offset: 2px;
        }
    </style>
@endonce
