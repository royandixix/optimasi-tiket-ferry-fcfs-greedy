# Perbaikan Rekomendasi Klik Jadwal - V4

Versi ini menerapkan alur rekomendasi sesuai kebutuhan bimbingan:

1. User membuka halaman Buat Pemesanan.
2. User bebas mengklik jadwal/kapal yang diinginkan (misalnya Jadwal A).
3. Sistem membaca kapasitas total dan estimasi sisa berdasarkan pemesanan aktif (`pending` + `diterima`).
4. Sistem hanya mencari alternatif pada **rute yang sama**.
5. Jika ada Jadwal B yang persentase kapasitas kosongnya lebih besar dari A, sistem menampilkan popup perbandingan A vs B.
6. User dapat memilih **Pilih Jadwal Rekomendasi** atau **Tetap Pilih Jadwal Ini**.
7. Jika tidak ada alternatif yang lebih longgar, popup tidak muncul dan pilihan user langsung dipakai.
8. Rekomendasi tidak menentukan status akhir tiket. Pemesanan tetap berstatus `pending` dan proses alokasi FCFS/Greedy tetap berada di backend.

## Catatan relevansi rekomendasi

- Tujuan/rute tidak diubah oleh sistem.
- Jika tersedia, alternatif pada tanggal yang sama diprioritaskan.
- Setelah itu sistem memilih persentase kosong terbesar, lalu jumlah sisa terbesar.
- Rekomendasi bersifat saran, tidak wajib.

## Perbaikan startup

`composer run dev` sekarang memeriksa `node_modules/.bin/vite`. Jika Vite belum ada, sistem otomatis menjalankan `npm ci` sebelum menjalankan server development.
