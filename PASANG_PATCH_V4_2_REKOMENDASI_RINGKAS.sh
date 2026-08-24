#!/usr/bin/env bash
set -euo pipefail

if [ ! -f artisan ]; then
  echo "ERROR: Jalankan dari root project Laravel (folder yang ada file artisan)."
  exit 1
fi

python3 <<'PY'
from pathlib import Path
import re


def read(path):
    p = Path(path)
    if not p.exists():
        raise SystemExit(f"File tidak ditemukan: {path}")
    return p, p.read_text()

# ------------------------------------------------------------------
# 1) Modal klik tiket: ringkas, tanpa deskripsi FCFS/Greedy.
# ------------------------------------------------------------------
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
                        Ada pilihan yang lebih kosong
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
                            <span class="badge text-bg-success rounded-pill mb-2">
                                <i class="bi bi-stars me-1"></i>Lebih Banyak Kosong
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
                    Pilih yang Lebih Kosong
                </button>
            </div>
        </div>
    </div>
</div>
''')
print('OK: modal rekomendasi klik dibuat lebih ringkas')

# ------------------------------------------------------------------
# 2) Halaman create: hapus deskripsi rekomendasi umum dan FCFS/Greedy.
#    Modal umum juga tidak dipakai lagi; popup hanya ketika tiket diklik.
# ------------------------------------------------------------------
create_path, text = read('resources/views/user/pemesanan/create.blade.php')

# Hapus paragraf deskripsi di bawah judul jika masih mengandung rekomendasi.
text = re.sub(
    r'''\n\s*<p class="text-muted mb-0">\s*Sistem memberi rekomendasi jadwal yang relatif lebih longgar, tetapi Anda tetap bebas memilih jadwal lain\.\s*</p>''',
    '',
    text,
    count=1,
    flags=re.S,
)

# Hapus alert rekomendasi umum + tombol Lihat Rekomendasi.
text = re.sub(
    r'''\n@if \(\(\$rekomendasiJadwals \?\? collect\(\)\)->isNotEmpty\(\)\)\s*\n\s*<div class="alert bg-success-subtle.*?</div>\s*\n@endif\s*\n''',
    '\n',
    text,
    count=1,
    flags=re.S,
)

# Hapus alert FCFS/Greedy di Detail Pemesanan.
text = re.sub(
    r'''\n\s*<div class="alert bg-warning-subtle border border-warning-subtle rounded-3 small mt-4 mb-0">\s*<i class="bi bi-exclamation-circle me-1"></i>\s*Pemesanan yang dibuat berstatus.*?FCFS/Greedy pada sistem\.\s*</div>''',
    '',
    text,
    count=1,
    flags=re.S,
)

# Modal rekomendasi umum tidak perlu lagi.
text = text.replace("    @include('user.pemesanan.components.rekomendasi-jadwal-modal')\n", '')

# Ringkas helper kapasitas.
text = re.sub(
    r'''helper\.textContent = 'Kapasitas total ' \+ capacity\s*\+ ' unit\. Perkiraan sisa berdasarkan permintaan aktif: '\s*\+ estimatedRemaining \+ ' unit\.';''',
    "helper.textContent = 'Sisa tiket: ' + estimatedRemaining + ' dari ' + capacity + '.';",
    text,
    count=1,
    flags=re.S,
)

text = re.sub(
    r'''scheduleHelper\.innerHTML = '<i class="bi bi-info-circle me-1"></i>'\s*\+ 'Kapasitas total kapal <strong>' \+ capacity \+ '</strong> unit\. '\s*\+ 'Perkiraan sisa berdasarkan permintaan aktif saat ini <strong>'\s*\+ estimatedRemaining \+ '</strong> unit\. Anda tetap boleh memilih jadwal ini\.';''',
    "scheduleHelper.innerHTML = '<i class=\"bi bi-ticket-perforated me-1\"></i>' + 'Sisa tiket <strong>' + estimatedRemaining + '</strong> dari <strong>' + capacity + '</strong>.';",
    text,
    count=1,
    flags=re.S,
)

# Hapus baris JS yang menulis persen ke elemen modal yang sudah tidak ada.
text = re.sub(r'''\n\s*putText\('pilihanAwalPersen'.*?;''', '', text, count=1)
text = re.sub(r'''\n\s*putText\('pilihanRekomendasiPersen'.*?;''', '', text, count=1)

# Handler tombol modal umum boleh tetap ada; tanpa modal/tombol, kode ini tidak melakukan apa-apa.

# Hapus komentar lama tentang modal umum.
text = re.sub(
    r'''\n\s*// Modal rekomendasi umum tetap dapat dibuka manual melalui tombol\s*\n\s*// "Lihat Rekomendasi", tetapi tidak dipaksa muncul ketika halaman dibuka\.''',
    '',
    text,
    count=1,
)

create_path.write_text(text)
print('OK: create.blade.php dibersihkan dari deskripsi panjang')

# ------------------------------------------------------------------
# 3) Card tiket: tidak perlu badge Recomendasi #1/#2/#3 di muka.
#    Rekomendasi baru muncul setelah user klik.
# ------------------------------------------------------------------
card_path, card = read('resources/views/user/pemesanan/partials/jadwal-card.blade.php')

card = re.sub(
    r'''\n\s*\$rekomendasiIndex = collect\(\$rekomendasiIds \?\? \[\]\).*?\$rekomendasiIndex \+ 1;''',
    '',
    card,
    count=1,
    flags=re.S,
)

card = re.sub(
    r'''\n\s*@if \(\$peringkatRekomendasi !== null\).*?@endif''',
    '',
    card,
    count=1,
    flags=re.S,
)

card = card.replace('Perkiraan Sisa', 'Sisa Tiket')
card_path.write_text(card)
print('OK: badge rekomendasi awal dihapus; rekomendasi hanya saat klik')

# ------------------------------------------------------------------
# 4) Jangan tampilkan jadwal yang secara hitungan sudah 0 sisa.
# ------------------------------------------------------------------
service_path, service = read('app/Services/JadwalRecommendationService.php')

if "->filter(fn (JadwalKeberangkatan $jadwal): bool =>\n                (int) ($jadwal->rekomendasi_sisa ?? 0) > 0\n            )\n            ->values();" not in service:
    target = """                return $jadwal;\n            });\n    }\n"""
    replacement = """                return $jadwal;\n            })\n            ->filter(fn (JadwalKeberangkatan $jadwal): bool =>\n                (int) ($jadwal->rekomendasi_sisa ?? 0) > 0\n            )\n            ->values();\n    }\n"""
    if target in service:
        service = service.replace(target, replacement, 1)
        service_path.write_text(service)
        print('OK: jadwal dengan sisa 0 tidak ditampilkan ke user')
    else:
        print('INFO: filter sisa 0 tidak diubah (struktur service berbeda / sudah dipasang)')
else:
    print('OK: filter jadwal sisa 0 sudah ada')

print('\nPATCH REKOMENDASI RINGKAS SELESAI')
PY

php artisan optimize:clear
php -l app/Services/JadwalRecommendationService.php

echo ""
echo "SELESAI. Refresh browser dengan Cmd+Shift+R."
