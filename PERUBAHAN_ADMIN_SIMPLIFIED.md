# Perubahan Admin - Optimasi Tiket Disembunyikan dari Sidebar

Menu berikut disembunyikan dari navigasi admin:
- Optimasi Tiket
- Alokasi Tiket
- Hasil Optimasi

Implementasi dilakukan dengan `protected static bool $shouldRegisterNavigation = false;` pada resource Alokasi Tiket dan Hasil Optimasi.

Backend, model, service, tabel database, serta route resource tidak dihapus. Dengan demikian aplikasi tetap dapat menggunakan proses optimasi tanpa menampilkan menu tersebut di sidebar admin.

Route masih tersedia jika memang perlu diakses langsung:
- `/admin/alokasi-tikets`
- `/admin/hasil-optimasis`

Jangan menghapus `TiketAllocationService`, model `AlokasiTiket`, model `HasilOptimasi`, atau migration terkait jika algoritma masih digunakan.
