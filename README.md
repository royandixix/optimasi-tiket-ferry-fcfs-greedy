Sistem Optimasi Tiket Ferry — FCFS & Greedy

Aplikasi web untuk pemesanan, pengelolaan, pembayaran, validasi, dan optimasi alokasi tiket kapal ferry. Sistem dikembangkan menggunakan Laravel 13 dan Filament 5, dengan implementasi metode First Come First Served (FCFS) dan Greedy untuk membantu mengoptimalkan pemanfaatan kapasitas kapal.

Tentang Project

Sistem Optimasi Tiket Ferry dirancang untuk mendigitalisasi proses pemesanan tiket ferry mulai dari registrasi penumpang, pemilihan jadwal, pemesanan tiket, pembayaran, hingga validasi tiket sebelum keberangkatan.

Pada sisi pengelola, sistem menyediakan dashboard untuk mengelola data kapal, rute, jadwal keberangkatan, penumpang, pemesanan, pembayaran, validasi tiket, alokasi tiket, serta hasil optimasi kapasitas kapal.

Sistem juga menyediakan rekomendasi jadwal kepada penumpang berdasarkan perkiraan sisa kapasitas sehingga distribusi pemesanan dapat diarahkan ke jadwal yang masih memiliki kapasitas lebih longgar.

Fitur Utama

Penumpang

Registrasi dan login akun

Dashboard penumpang

Pengelolaan profil

Melihat kapal dan jadwal keberangkatan

Membuat pemesanan tiket

Memilih jumlah tiket

Melihat rekomendasi jadwal berdasarkan sisa kapasitas

Melihat riwayat dan detail pemesanan

Mengubah pemesanan selama masih memenuhi ketentuan sistem

Pembayaran melalui transfer bank atau cash

Upload bukti transfer untuk pembayaran transfer bank

Melihat status pembayaran dan pemesanan

Tiket dengan data validasi/QR

Admin

Dashboard administrasi menggunakan Filament

Manajemen pengguna

Manajemen data kapal

Manajemen data rute

Manajemen jadwal keberangkatan

Manajemen data penumpang

Manajemen pemesanan tiket

Verifikasi pembayaran

Validasi tiket

Pengelolaan alokasi tiket

Proses optimasi menggunakan FCFS dan Greedy

Monitoring hasil optimasi dan pemanfaatan kapasitas kapal

Petugas

Login ke panel internal

Melakukan validasi tiket penumpang

Mendukung proses pemeriksaan tiket menggunakan data/QR tiket

Metode Optimasi

Project menggunakan dua metode alokasi tiket:

First Come First Served (FCFS)

Pemesanan diproses berdasarkan waktu pemesanan paling awal. Pemesanan yang lebih dahulu masuk akan mendapatkan prioritas selama kapasitas kapal masih tersedia.

Urutan utama:

Waktu pemesanan paling awal

ID pemesanan sebagai urutan tambahan

Pemesanan diterima jika jumlah tiket masih dapat ditampung kapasitas tersisa

Greedy

Pemesanan diprioritaskan berdasarkan jumlah tiket terbesar terlebih dahulu, kemudian waktu pemesanan.

Urutan utama:

Jumlah tiket terbesar

Waktu pemesanan paling awal

ID pemesanan

Pemesanan diterima jika seluruh jumlah tiket masih dapat dialokasikan

Hasil proses optimasi menyimpan informasi seperti:

Total pemesanan

Total tiket diminta

Total tiket diterima

Total tiket ditolak

Kapasitas kapal

Kapasitas terpakai

Load factor

Waktu proses algoritma

Rekomendasi Jadwal

Sistem menghitung perkiraan kapasitas yang masih tersedia menggunakan konsep:

perkiraan_sisa = kapasitas_total - total_tiket_aktif

total_tiket_aktif merupakan jumlah tiket dari pemesanan dengan status aktif seperti pending dan diterima.

Prioritas rekomendasi jadwal:

Persentase kapasitas kosong terbesar

Sisa kapasitas absolut terbesar

Waktu keberangkatan lebih awal

Hanya kapal dan rute aktif, jadwal tersedia, jadwal yang belum lewat, serta jadwal yang masih memiliki sisa kapasitas yang dapat menjadi rekomendasi.

Pembayaran

Sistem mendukung metode pembayaran:

Transfer Bank — penumpang mengunggah bukti transfer dan pembayaran dapat diverifikasi admin.

Cash — pembayaran dicatat menggunakan metode cash sesuai alur operasional sistem.

Setiap pembayaran memiliki kode pembayaran unik serta status pembayaran untuk memudahkan proses verifikasi.

Teknologi

PHP

Laravel 13

Filament 5

MySQL / MariaDB

Blade Template

Tailwind CSS

Vite

JavaScript

Barcode / QR Scanner Field

Role Pengguna

Role

Akses Utama

Admin

Mengelola data utama, pemesanan, pembayaran, optimasi, laporan, dan validasi tiket

Petugas

Validasi tiket penumpang

Penumpang

Registrasi, pemesanan, pembayaran, profil, dan melihat tiket

Struktur Data Utama

Beberapa model utama yang digunakan:

User
Kapal
Rute
JadwalKeberangkatan
Penumpang
PemesananTiket
Pembayaran
AlokasiTiket
HasilOptimasi
ValidasiTiket

Persyaratan

Sebelum menjalankan project, pastikan perangkat sudah memiliki:

PHP 8.3 atau lebih baru

Composer

MySQL atau MariaDB

Node.js dan npm

Pastikan extension PHP yang dibutuhkan Laravel dan koneksi MySQL tersedia pada environment lokal.

Instalasi

1. Clone Repository

git clone https://github.com/USERNAME/optimasi-tiket-ferry-fcfs-greedy.git
cd optimasi-tiket-ferry-fcfs-greedy

2. Install Dependency PHP

composer install

3. Install Dependency Frontend

npm install

4. Buat File Environment

cp .env.example .env

5. Generate Application Key

php artisan key:generate

6. Konfigurasi Database

Sesuaikan bagian berikut pada file .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=optimasi_tiket_ferry
DB_USERNAME=root
DB_PASSWORD=

Buat database bernama optimasi_tiket_ferry atau sesuaikan dengan nilai DB_DATABASE yang digunakan.

7. Jalankan Migration

php artisan migrate

Jika project menyediakan seeder yang ingin digunakan:

php artisan db:seed

8. Buat Storage Link

php artisan storage:link

9. Build Asset Frontend

Untuk development:

npm run dev

Atau build production:

npm run build

10. Jalankan Laravel

php artisan serve

Akses aplikasi melalui:

http://127.0.0.1:8000

Panel admin/internal:

http://127.0.0.1:8000/admin

Portal penumpang:

http://127.0.0.1:8000/user/login

Registrasi penumpang:

http://127.0.0.1:8000/user/register

Menjalankan Project Setelah Clone

Jika dependency dan database sudah siap, gunakan:

php artisan optimize:clear
php artisan migrate
php artisan storage:link
php artisan serve

Pada terminal lain:

npm run dev

Keamanan Repository

Jangan pernah meng-upload file .env ke GitHub karena dapat berisi konfigurasi database, application key, dan data sensitif lainnya.

Project sudah menggunakan .gitignore untuk mengabaikan beberapa file/folder seperti:

.env
/vendor
/node_modules
/public/storage
/storage/logs

Gunakan .env.example sebagai contoh konfigurasi environment.

Alur Singkat Sistem

Penumpang Registrasi
        ↓
Login
        ↓
Pilih Jadwal / Lihat Rekomendasi
        ↓
Buat Pemesanan Tiket
        ↓
Pilih Metode Pembayaran
        ↓
Verifikasi Pembayaran
        ↓
Alokasi / Optimasi Kapasitas
        ↓
Tiket Diterima
        ↓
Validasi Tiket / QR
        ↓
Keberangkatan

Tujuan Pengembangan

Project ini dikembangkan untuk membantu proses pengelolaan tiket ferry secara digital sekaligus menerapkan metode optimasi dalam proses alokasi kapasitas kapal. Perbandingan metode FCFS dan Greedy dapat digunakan untuk melihat efektivitas pemanfaatan kapasitas berdasarkan data pemesanan yang tersedia.

Pengembangan Selanjutnya

Beberapa pengembangan yang dapat ditambahkan:

Integrasi payment gateway

Notifikasi WhatsApp atau email

Cetak tiket PDF

Riwayat perjalanan yang lebih lengkap

Dashboard analitik tambahan

Deployment ke server production

Integrasi API eksternal jadwal pelabuhan

Lisensi

Project ini dibuat untuk kebutuhan pengembangan sistem dan akademik.# optimasi-tiket-ferry-fcfs-greedy
