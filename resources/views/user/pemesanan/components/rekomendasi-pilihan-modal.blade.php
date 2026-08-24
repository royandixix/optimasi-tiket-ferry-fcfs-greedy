<div
    class="modal fade"
    id="rekomendasiPilihanModal"
    tabindex="-1"
    aria-labelledby="rekomendasiPilihanModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-success-subtle px-4 py-3">
                <div>
                    <span class="badge text-bg-success rounded-pill mb-2">
                        <i class="bi bi-stars me-1"></i>Rekomendasi Tiket
                    </span>
                    <h5 class="modal-title fw-bold mb-0" id="rekomendasiPilihanModalLabel">
                        Cek ketersediaan tiket
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3 align-items-stretch">
                    <div class="col-12 col-md-5">
                        <div class="border rounded-4 p-3 h-100 bg-light">
                            <span class="badge text-bg-secondary rounded-pill mb-2">Pilihan Anda</span>
                            <h6 class="fw-bold mb-1" id="pilihanAwalNamaKapal">-</h6>
                            <div class="small text-muted mb-3" id="pilihanAwalRute">-</div>
                            <div class="small mb-2">
                                <i class="bi bi-calendar3 me-1"></i>
                                <span id="pilihanAwalWaktu">-</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                <span class="small">Sisa tiket</span>
                                <strong id="pilihanAwalSisa">-</strong>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-2 d-flex align-items-center justify-content-center">
                        <div class="text-center text-success">
                            <i class="bi bi-arrow-right-circle-fill fs-2 d-none d-md-inline"></i>
                            <i class="bi bi-arrow-down-circle-fill fs-2 d-md-none"></i>
                        </div>
                    </div>

                    <div class="col-12 col-md-5">
                        <div class="border border-success rounded-4 p-3 h-100 bg-success-subtle">
                            <span class="badge text-bg-success rounded-pill mb-2" id="rekomendasiBadge">
                                <i class="bi bi-stars me-1"></i>Rekomendasi
                            </span>
                            <h6 class="fw-bold mb-1" id="pilihanRekomendasiNamaKapal">-</h6>
                            <div class="small text-muted mb-3" id="pilihanRekomendasiRute">-</div>
                            <div class="small mb-2">
                                <i class="bi bi-calendar3 me-1"></i>
                                <span id="pilihanRekomendasiWaktu">-</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-top border-success-subtle pt-2 mt-2">
                                <span class="small">Sisa tiket</span>
                                <strong class="text-success" id="pilihanRekomendasiSisa">-</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                <button
                    type="button"
                    class="btn btn-outline-secondary rounded-pill"
                    id="tetapPilihanAwal"
                    data-bs-dismiss="modal"
                >
                    Tetap Pilih Ini
                </button>

                <button
                    type="button"
                    class="btn btn-success rounded-pill"
                    id="gunakanJadwalRekomendasi"
                >
                    <i class="bi bi-check2-circle me-1"></i>
                    <span id="gunakanJadwalRekomendasiText">Pilih yang Lebih Kosong</span>
                </button>
            </div>
        </div>
    </div>
</div>
