# Perbaikan Final Optimasi Tiket Ferry

## Struktur role

- `user`: memakai halaman Blade `/user` untuk registrasi, login, checkout, riwayat, dan tiket.
- `admin`: memakai Filament `/admin` dan dapat mengelola seluruh data serta melakukan validasi.
- `petugas`: memakai Filament, tetapi hanya dapat membuka menu **Validasi Tiket** dan riwayat validasinya sendiri.

Migration otomatis mengubah role lama:

- `penumpang` menjadi `user`
- `super_admin` dan `pimpinan` menjadi `admin`

## Harga checkout

- Harga tidak lagi dibuat pada Jadwal Keberangkatan.
- User memilih jenis tarif pada checkout.
- Total dihitung otomatis: `harga satuan × jumlah tiket/unit`.
- Server menghitung ulang harga sebelum menyimpan transaksi.
- Admin hanya melihat snapshot jenis tarif, harga satuan, jumlah, dan total.

## Scanner QR

- Kamera realtime tidak memakai batas waktu pemindaian.
- Validasi tidak dibatasi tanggal/jam keberangkatan.
- Tiket tetap harus berstatus diterima, jadwal tidak batal/selesai, dan belum pernah digunakan.
- Tersedia fokus kontinu, fokus ulang, zoom, kontrol lampu jika didukung perangkat, serta pemindaian foto dengan beberapa tahap peningkatan kontras untuk membantu kondisi gelap atau pantulan lampu.
- Pantulan yang menutupi sebagian besar kotak QR secara fisik tetap dapat membuat kode tidak terbaca; miringkan HP sedikit atau matikan lampu kamera.

## Perbaikan lain

- Menghilangkan error `$tarifData`, `satuan`, dan `$nilaiDiterima`.
- Mencegah duplikasi alokasi FCFS/Greedy saat optimasi dijalankan ulang.
- Menyembunyikan pembuatan harga/pemesanan manual yang tidak diperlukan dari admin.

## Setelah extract

Jalankan:

```bash
cd "$HOME/Downloads/optimasi-tiket-ferry"
chmod +x jalankan-setelah-extract.sh run-camera-https.sh
./jalankan-setelah-extract.sh
```

Untuk membuka melalui HTTPS agar kamera HP dapat dipakai:

```bash
./run-camera-https.sh
```
