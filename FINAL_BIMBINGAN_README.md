# FINAL BIMBINGAN — Optimasi Tiket Ferry

Versi ini disiapkan untuk demo/bimbingan dengan alur admin yang lebih sederhana dan rekomendasi jadwal pada portal user.

## Alur Admin

1. Admin login ke `/admin`.
2. Admin mengisi **Kapal** (nama, kapasitas, status, minimal 3 foto).
3. Admin mengisi **Rute**.
4. Admin membuat **Jadwal Keberangkatan** dengan memilih kapal dan rute.
5. `kapasitas_total` jadwal disinkronkan **server-side** dari `kapasitas_penumpang` kapal, bukan dipercaya dari input browser.
6. Admin memantau **Pemesanan Tiket**, **Penumpang**, dan **Validasi Tiket**.
7. Menu **Alokasi Tiket** dan **Hasil Optimasi** tidak ditampilkan pada sidebar admin agar UI tidak membingungkan. Service FCFS/Greedy dan tabel backend tetap dipertahankan.

## Alur Rekomendasi User

Ketika user masuk ke menu **Pemesanan** dari navbar atau dashboard, sistem menampilkan modal rekomendasi (jika ada jadwal yang masih memiliki perkiraan sisa kapasitas).

Rumus tampilan:

`perkiraan_sisa = kapasitas_jadwal - total_tiket_aktif`

`total_tiket_aktif` = pemesanan berstatus `pending` + `diterima`.

Rekomendasi:
- hanya memakai **kapal aktif**;
- hanya memakai **jadwal tersedia** yang belum lewat;
- jadwal dengan perkiraan sisa `0` tidak diberi badge rekomendasi;
- prioritas: persentase kosong terbesar → sisa absolut terbesar → keberangkatan lebih awal;
- rekomendasi **tidak wajib**. User tetap dapat memilih jadwal lain yang tersedia.

## Instalasi pada Mac yang Sudah Menjalankan Project Lama

Database MySQL berada di luar ZIP. Jika project lama sudah berjalan dan `.env` memakai database yang sama, cukup:

```bash
cd /path/ke/optimasi-tiket-ferry-FINAL-BIMBINGAN
chmod +x jalankan-setelah-extract.sh
./jalankan-setelah-extract.sh
php artisan serve
```

`vendor` dan asset build sudah disertakan, jadi script tidak menjalankan Composer jika tidak diperlukan.

## Persyaratan

- PHP >= 8.4.1 (sesuai dependency lock project ini)
- PHP extension: `dom`, `pdo`, `pdo_mysql`
- MySQL aktif dan database sesuai `.env` sudah tersedia

## Jika Memakai Database Baru

Buat database MySQL bernama sesuai `DB_DATABASE` pada `.env` (default `optimasi_tiket_ferry`), lalu jalankan script setup. Migration akan membuat struktur tabel. Data database lama tidak berada di dalam ZIP ini.

## Yang Diperiksa Sebelum ZIP Dibuat

- syntax seluruh file PHP;
- kompilasi dan syntax seluruh Blade view;
- registrasi route Laravel;
- smoke-test pengurutan rekomendasi;
- manifest dan file asset frontend/build yang diperlukan tersedia;
- `public/storage` memakai symlink relatif/portabel;
- menu optimasi admin tetap hidden dan resource tidak menyediakan create/edit manual.

> Tidak ada paket software yang dapat dijamin bebas error pada semua komputer karena PHP extension, MySQL, dan konfigurasi lokal berbeda. Versi ini sudah dibuat untuk meminimalkan error project dan menyertakan preflight setup agar masalah environment muncul dengan pesan yang jelas.
