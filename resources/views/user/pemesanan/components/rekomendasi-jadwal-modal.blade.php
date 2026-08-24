@if (($rekomendasiJadwals ?? collect())->isNotEmpty())
    <div
        class="modal fade"
        id="rekomendasiJadwalModal"
        tabindex="-1"
        aria-labelledby="rekomendasiJadwalModalLabel"
        aria-hidden="true"
        data-auto-show="0"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 bg-success-subtle px-4 py-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge text-bg-success rounded-pill">
                                <i class="bi bi-stars me-1"></i>Rekomendasi Sistem
                            </span>
                            <small class="text-muted">Tidak wajib dipilih</small>
                        </div>
                        <h5 class="modal-title fw-bold" id="rekomendasiJadwalModalLabel">
                            Jadwal dengan kapasitas yang relatif lebih longgar
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
                    <div class="alert alert-light border rounded-3 small mb-4">
                        <i class="bi bi-info-circle text-success me-1"></i>
                        Rekomendasi dihitung dari <strong>kapasitas kapal dibandingkan jumlah permintaan tiket aktif</strong>.
                        Anda tetap boleh memilih jadwal lain yang tersedia. Status akhir diterima atau ditolak tetap ditentukan saat admin menjalankan proses FCFS/Greedy.
                    </div>

                    <div class="row g-3">
                        @foreach ($rekomendasiJadwals as $jadwalRekomendasi)
                            @php
                                $kapalRekomendasi = $jadwalRekomendasi->kapal;
                                $ruteRekomendasi = $jadwalRekomendasi->rute;
                                $sisaRekomendasi = (int) ($jadwalRekomendasi->rekomendasi_sisa ?? 0);
                                $kapasitasRekomendasi = (int) ($jadwalRekomendasi->kapasitas_total ?? 0);
                                $persenKosongRekomendasi = (float) ($jadwalRekomendasi->rekomendasi_persen_kosong ?? 0);
                                $levelRekomendasi = $jadwalRekomendasi->rekomendasi_level ?? 'success';
                            @endphp

                            <div class="col-12 col-md-4">
                                <div class="border rounded-4 p-3 h-100 d-flex flex-column">
                                    <div class="d-flex justify-content-between gap-2 align-items-start mb-2">
                                        <span class="badge text-bg-success rounded-pill">
                                            #{{ $loop->iteration }} Pilihan
                                        </span>
                                        <span class="badge text-bg-{{ $levelRekomendasi }}">
                                            {{ $jadwalRekomendasi->rekomendasi_label ?? 'Tersedia' }}
                                        </span>
                                    </div>

                                    <h6 class="fw-bold mb-1">
                                        {{ $kapalRekomendasi?->nama_kapal ?? 'Kapal Ferry' }}
                                    </h6>

                                    <p class="small text-muted mb-3">
                                        {{ $ruteRekomendasi?->pelabuhan_asal ?? '-' }}
                                        <i class="bi bi-arrow-right mx-1"></i>
                                        {{ $ruteRekomendasi?->pelabuhan_tujuan ?? '-' }}
                                    </p>

                                    <div class="small mb-2">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ optional($jadwalRekomendasi->tanggal_berangkat)->format('d M Y') ?? '-' }}
                                        &middot;
                                        {{ $jadwalRekomendasi->jam_berangkat ? substr((string) $jadwalRekomendasi->jam_berangkat, 0, 5) : '-' }}
                                    </div>

                                    <div class="small mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Perkiraan sisa</span>
                                            <strong>{{ number_format($sisaRekomendasi) }} / {{ number_format($kapasitasRekomendasi) }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Persentase kosong</span>
                                            <strong>{{ number_format($persenKosongRekomendasi, 1, ',', '.') }}%</strong>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        class="btn btn-outline-success btn-sm rounded-pill mt-auto pilih-rekomendasi"
                                        data-jadwal-id="{{ $jadwalRekomendasi->id }}"
                                    >
                                        <i class="bi bi-check2-circle me-1"></i>
                                        Pilih Jadwal Ini
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button
                        type="button"
                        class="btn btn-outline-secondary rounded-pill"
                        data-bs-dismiss="modal"
                    >
                        Tetap Lihat Semua Jadwal
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
