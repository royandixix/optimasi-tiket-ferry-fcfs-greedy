# Perbaikan untuk Bimbingan - 11 Agustus 2026

## Alur sistem yang dipakai

1. User membuat **Pemesanan Tiket**.
2. Pemesanan masuk dengan status **Pending / Menunggu Proses**.
3. Admin membuka **Hasil Optimasi**.
4. Admin memilih jadwal yang memang sudah memiliki pemesanan.
5. Admin menjalankan **Proses FCFS** atau **Proses Greedy**.
6. Sistem otomatis membuat **Data Alokasi Tiket** per pemesanan.
7. Sistem otomatis membuat **Laporan Hasil Optimasi**.

`Alokasi Tiket` sekarang bersifat **read-only**. Data alokasi tidak lagi boleh ditambah, diedit, atau dihapus manual dari menu Filament karena merupakan output algoritma.

## Fitur rekomendasi pemesanan untuk user

Saat user menekan menu **Pemesanan**, sistem menampilkan modal rekomendasi jadwal.

Rekomendasi dihitung dari:

- kapasitas total kapal;
- jumlah tiket dari pemesanan aktif (`pending` dan `diterima`);
- perkiraan sisa kapasitas;
- persentase kapasitas yang masih kosong.

Urutan rekomendasi:

1. persentase kosong terbesar;
2. jika sama, sisa kapasitas terbesar;
3. jika masih sama, jadwal keberangkatan yang lebih awal.

Rekomendasi **tidak wajib dipilih**. User tetap dapat membuka seluruh jadwal dan memilih jadwal lain yang masih berstatus tersedia.

Rekomendasi juga **tidak menjamin tiket diterima**. Status akhir tetap ditentukan ketika admin menjalankan FCFS atau Greedy.

## Perbaikan integritas data optimasi

- Jadwal tanpa pemesanan tidak lagi ditawarkan pada modal `Proses FCFS` / `Proses Greedy`.
- Pilihan jadwal optimasi sekarang menampilkan jumlah pemesanan, total tiket diminta, dan kapasitas kapal agar admin tidak salah memilih jadwal.
- `TiketAllocationService` menolak proses optimasi untuk jadwal yang tidak memiliki pemesanan.
- Kapasitas terpakai pada form Jadwal dibuat read-only karena nilainya dihitung oleh proses optimasi.
- Pemesanan di panel admin tidak lagi dapat mengubah status/metode secara manual. Status dan metode merupakan hasil proses algoritma.

## Membersihkan data lama yang tidak konsisten

Jika sebelumnya pernah membuat Alokasi Tiket secara manual dan nilainya tidak konsisten, lakukan:

1. Login Admin.
2. Buka **Hasil Optimasi**.
3. Klik **Reset Hasil Jadwal**.
4. Pilih jadwal yang ingin dibersihkan.
5. Konfirmasi reset.
6. Data pemesanan tidak dihapus; statusnya dikembalikan menjadi **Pending**.
7. Jalankan ulang **Proses FCFS** dan/atau **Proses Greedy**.

Reset hanya menghapus data turunan `alokasi_tikets` dan `hasil_optimasis` untuk jadwal yang dipilih.

## Setelah extract ZIP

Pada Mac, dari Terminal di folder project:

```bash
php artisan optimize:clear
php artisan serve
```

Jika ada migration yang belum pernah dijalankan dari versi sebelumnya:

```bash
php artisan migrate
```

Tidak ada migration baru yang diwajibkan oleh fitur rekomendasi ini.

## Catatan pengujian paket

- Seluruh file PHP pada `app`, `routes`, `database`, dan `tests` telah lolos `php -l`.
- Seluruh Blade pada `resources/views/user` telah dikompilasi dan dilint secara statis tanpa error syntax.
- Route `user.pemesanan.index`, `user.pemesanan.create`, dan `user.pemesanan.store` berhasil diregistrasikan.
- Ranking fungsi rekomendasi telah diuji secara statis.
- Pengujian database penuh tidak dapat dijalankan di lingkungan pembuatan ZIP karena PHP environment tersebut tidak menyediakan PDO MySQL/SQLite; project pengguna tetap menggunakan MySQL sesuai `.env`.
