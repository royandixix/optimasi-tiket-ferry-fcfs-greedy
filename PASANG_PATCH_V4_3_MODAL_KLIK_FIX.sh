#!/usr/bin/env bash
set -euo pipefail

if [ ! -f artisan ]; then
  echo "ERROR: Jalankan dari root project Laravel (folder yang ada file artisan)."
  exit 1
fi

python3 <<'PY'
from pathlib import Path
import re

# ============================================================
# 1. Saat user klik tombol tiket, tandai bahwa rekomendasi
#    memang diminta oleh aksi klik tersebut.
# ============================================================
catalog_path = Path('resources/views/user/pemesanan/partials/kapal-admin-catalog.blade.php')
if not catalog_path.exists():
    raise SystemExit('File katalog kapal tidak ditemukan.')

catalog = catalog_path.read_text()

catalog = catalog.replace(
    "['kapal_id' => $kapalCatalog->id, 'jadwal_id' => $jadwalTerdekat->id]",
    "['kapal_id' => $kapalCatalog->id, 'jadwal_id' => $jadwalTerdekat->id, 'cek_rekomendasi' => 1]"
)

catalog_path.write_text(catalog)
print('OK: klik Pesan/Pilih Jadwal sekarang membawa flag cek_rekomendasi=1')

# ============================================================
# 2. Modal: selalu dapat tampil setelah KLIK tiket.
#    - Kalau ada jadwal lebih kosong -> rekomendasikan jadwal itu.
#    - Kalau tidak ada -> beri tahu bahwa pilihan ini paling kosong.
# ============================================================
modal_path = Path('resources/views/user/pemesanan/components/rekomendasi-pilihan-modal.blade.php')
modal_path.parent.mkdir(parents=True, exist_ok=True)
modal_path.write_text(r'''<div
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
''')
print('OK: modal rekomendasi diperbarui')

# ============================================================
# 3. Perbaiki JS create.blade.php.
# ============================================================
create_path = Path('resources/views/user/pemesanan/create.blade.php')
if not create_path.exists():
    raise SystemExit('create.blade.php tidak ditemukan.')

text = create_path.read_text()

new_function = r'''    const showComparisonRecommendation = function (selectedRadio) {
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
            // Tidak ada tiket lain yang lebih kosong.
            // Modal tetap muncul karena user memang baru saja mengklik tiket.
            recommendedScheduleId = selected.id;

            if (modalTitle) {
                modalTitle.textContent = 'Pilihan ini paling kosong saat ini';
            }

            if (badge) {
                badge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Pilihan Terbaik';
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
'''

pattern = re.compile(
    r"    const showComparisonRecommendation = function \(selectedRadio\) \{.*?\n    \};\n\n    const selectSchedule",
    re.S,
)

if not pattern.search(text):
    raise SystemExit('Fungsi showComparisonRecommendation tidak ditemukan.')

text = pattern.sub(new_function + "\n    const selectSchedule", text, count=1)

# Setelah halaman create dibuka karena KLIK tiket dari katalog,
# trigger modal sekali. Masuk menu Pemesanan biasa tidak memakai flag ini.
marker = '''    synchronizeState();
    refreshCapacity();
'''
addition = '''    synchronizeState();
    refreshCapacity();

    const recommendationParams = new URLSearchParams(window.location.search);
    if (recommendationParams.get('cek_rekomendasi') === '1') {
        const selectedFromClick = document.querySelector('.jadwal-radio:checked');

        if (selectedFromClick) {
            window.setTimeout(function () {
                showComparisonRecommendation(selectedFromClick);
            }, 180);
        }

        // Hapus flag agar refresh halaman tidak memunculkan modal lagi.
        recommendationParams.delete('cek_rekomendasi');
        const cleanQuery = recommendationParams.toString();
        const cleanUrl = window.location.pathname
            + (cleanQuery ? '?' + cleanQuery : '')
            + window.location.hash;
        window.history.replaceState({}, document.title, cleanUrl);
    }
'''

if 'recommendationParams.get(\'cek_rekomendasi\')' not in text:
    if marker not in text:
        raise SystemExit('Lokasi trigger rekomendasi tidak ditemukan.')
    text = text.replace(marker, addition, 1)

create_path.write_text(text)
print('OK: modal sekarang dipicu setelah user KLIK tiket')

# ============================================================
# 4. Pastikan halaman index tidak punya auto modal lama.
# ============================================================
index_path = Path('resources/views/user/pemesanan/index.blade.php')
if index_path.exists():
    index_text = index_path.read_text()
    index_text = index_text.replace("@include('user.pemesanan.components.rekomendasi-index-modal')\n", '')
    index_text = index_text.replace(
        "route('user.pemesanan.index', ['rekomendasi' => 1])",
        "route('user.pemesanan.index')"
    )
    index_text = index_text.replace('Refresh & Cek Rekomendasi', 'Refresh Data')
    index_path.write_text(index_text)
    print('OK: masuk menu Pemesanan tetap tanpa modal otomatis')

print('\nPATCH v4.3 SELESAI')
PY

php artisan optimize:clear
php -l app/Services/JadwalRecommendationService.php

echo ""
echo "SELESAI. Refresh browser dengan Cmd+Shift+R."
