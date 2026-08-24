#!/usr/bin/env bash

set -Eeuo pipefail

PROJECT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$PROJECT_DIR"

line() { printf '%s\n' '======================================================'; }
fail() { echo "ERROR: $1"; exit 1; }

line
echo "SETUP FINAL - OPTIMASI TIKET FERRY"
line

command -v php >/dev/null 2>&1 || fail "PHP belum terpasang / tidak ada di PATH."

# vendor pada paket final sudah disertakan. Minimum ini mengikuti composer.lock
# yang dipakai project sehingga tidak perlu composer install ulang.
php -r 'exit(version_compare(PHP_VERSION, "8.4.1", ">=") ? 0 : 1);' \
    || fail "Project ini membutuhkan PHP 8.4.1 atau lebih baru. Versi saat ini: $(php -r 'echo PHP_VERSION;')"

[ -f artisan ] || fail "File artisan tidak ditemukan. Pastikan menjalankan script dari folder project."
[ -f vendor/autoload.php ] || fail "Folder vendor tidak lengkap. Jalankan composer install terlebih dahulu."

# Ekstensi ini dibutuhkan oleh stack Laravel/Filament/MySQL yang dipakai project.
for ext in dom pdo pdo_mysql; do
    php -r "exit(extension_loaded('$ext') ? 0 : 1);" \
        || fail "Ekstensi PHP '$ext' belum aktif. Aktifkan ekstensi tersebut lalu ulangi setup."
done

if [ ! -f .env ]; then
    [ -f .env.example ] || fail ".env.example tidak ditemukan."
    cp .env.example .env
    php artisan key:generate --force --no-interaction
fi

# Pastikan folder runtime dapat ditulis.
mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache
chmod -R u+rwX storage bootstrap/cache 2>/dev/null || true

# Tidak memanggil composer saat vendor sudah tersedia, sehingga setup tetap dapat
# dijalankan walau command composer tidak terpasang pada Mac.
php artisan optimize:clear --no-interaction

# Database MySQL HARUS sudah dibuat sesuai DB_DATABASE di .env.
# migrate aman dijalankan berkali-kali dan tidak menghapus data existing.
if ! php artisan migrate --force --no-interaction; then
    echo ""
    echo "Migration gagal. Biasanya MySQL belum aktif atau database pada .env belum dibuat."
    echo "Periksa DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, dan DB_PASSWORD di .env."
    exit 1
fi

# Symlink foto kapal dibuat ulang secara portabel.
if [ -L public/storage ] || [ -e public/storage ]; then
    rm -rf public/storage
fi
php artisan storage:link --no-interaction

php artisan optimize:clear --no-interaction

line
echo "SETUP SELESAI"
line
echo "Jalankan aplikasi dengan:"
echo "  php artisan serve --host=127.0.0.1 --port=8000"
echo ""
echo "User : http://127.0.0.1:8000/user/login"
echo "Admin: http://127.0.0.1:8000/admin"
echo ""
echo "Catatan: menu Alokasi Tiket dan Hasil Optimasi sengaja disembunyikan dari sidebar admin."
echo "Fitur rekomendasi user tetap bekerja dari data kapal, jadwal, dan pemesanan aktif."
line
