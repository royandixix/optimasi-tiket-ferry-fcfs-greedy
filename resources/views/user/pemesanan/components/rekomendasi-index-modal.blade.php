@if (($rekomendasiJadwals ?? collect())->isNotEmpty())
    <div
        class="modal fade"
        id="rekomendasiPemesananModal"
        tabindex="-1"
        aria-labelledby="rekomendasiPemesananModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 bg-success-subtle px-4 py-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge text-bg-success rounded-pill">
                                <i class="bi bi-stars me-1"></i>Rekomendasi Pemesanan
                            </span>
                            <small class="text-muted">Opsional</small>
                        </div>
                        <h5 class="modal-title fw-bold" id="rekomendasiPemesananModalLabel">
                            Ingin memilih jadwal yang relatif lebih longgar?
                        </h5>
                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Tutup"
                    ></button>
                </div>

                <div class="modal-body p-4">
                    <p class="text-muted mb-4">
                        Sistem menghitung rekomendasi dari kapasitas kapal dan jumlah permintaan tiket aktif. Anda <strong>tidak wajib</strong> mengikuti rekomendasi dan tetap dapat memilih jadwal lain yang tersedia.
                    </p>

                    <div class="row g-3">
                        @foreach ($rekomendasiJadwals as $jadwalRekomendasi)
                            @php
                                $kapal = $jadwalRekomendasi->kapal;
                                $rute = $jadwalRekomendasi->rute;
                                $sisa = (int) ($jadwalRekomendasi->rekomendasi_sisa ?? 0);
                                $kapasitas = (int) ($jadwalRekomendasi->kapasitas_total ?? 0);
                                $persenKosong = (float) ($jadwalRekomendasi->rekomendasi_persen_kosong ?? 0);
                                $level = $jadwalRekomendasi->rekomendasi_level ?? 'success';
                            @endphp

                            <div class="col-12 col-md-4">
                                <div class="border rounded-4 p-3 h-100 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                        <span class="badge text-bg-success rounded-pill">
                                            Rekomendasi #{{ $loop->iteration }}
                                        </span>
                                        <span class="badge text-bg-{{ $level }}">
                                            {{ $jadwalRekomendasi->rekomendasi_label ?? 'Tersedia' }}
                                        </span>
                                    </div>

                                    <h6 class="fw-bold mb-1">
                                        {{ $kapal?->nama_kapal ?? 'Kapal Ferry' }}
                                    </h6>

                                    <div class="small text-muted mb-3">
                                        {{ $rute?->pelabuhan_asal ?? '-' }}
                                        <i class="bi bi-arrow-right mx-1"></i>
                                        {{ $rute?->pelabuhan_tujuan ?? '-' }}
                                    </div>

                                    <div class="small mb-2">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ optional($jadwalRekomendasi->tanggal_berangkat)->format('d M Y') ?? '-' }}
                                        &middot;
                                        {{ $jadwalRekomendasi->jam_berangkat ? substr((string) $jadwalRekomendasi->jam_berangkat, 0, 5) : '-' }}
                                    </div>

                                    <div class="small mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Perkiraan sisa</span>
                                            <strong>{{ number_format($sisa) }} / {{ number_format($kapasitas) }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Kapasitas kosong</span>
                                            <strong>{{ number_format($persenKosong, 1, ',', '.') }}%</strong>
                                        </div>
                                    </div>

                                    <a
                                        href="{{ route('user.pemesanan.create', ['jadwal_id' => $jadwalRekomendasi->id]) }}"
                                        class="btn btn-outline-success btn-sm rounded-pill mt-auto"
                                    >
                                        <i class="bi bi-ticket-perforated me-1"></i>
                                        Pesan Jadwal Ini
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="alert alert-light border rounded-3 small mt-4 mb-0">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        Rekomendasi hanya membantu memilih jadwal. Status akhir tiket tetap mengikuti proses alokasi FCFS/Greedy pada sistem.
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <a
                        href="{{ route('user.pemesanan.create') }}"
                        class="btn btn-success rounded-pill"
                    >
                        Lihat Semua Jadwal
                    </a>
                    <button
                        type="button"
                        class="btn btn-outline-secondary rounded-pill"
                        data-bs-dismiss="modal"
                    >
                        Tetap di Riwayat
                    </button>
                </div>
            </div>
        </div>
    </div>

    
@endif
