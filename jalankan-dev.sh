#!/usr/bin/env bash
set -Eeuo pipefail
PROJECT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$PROJECT_DIR"

fail() { echo "ERROR: $1"; exit 1; }

command -v php >/dev/null 2>&1 || fail "PHP tidak ditemukan di PATH."
command -v node >/dev/null 2>&1 || fail "Node.js tidak ditemukan. Instal Node.js 20.19+ atau 22.12+."
command -v npm >/dev/null 2>&1 || fail "npm tidak ditemukan."
[ -f vendor/autoload.php ] || fail "Folder vendor tidak lengkap."
if [ ! -x node_modules/.bin/vite ]; then
    echo "Vite lokal belum tersedia. Menjalankan npm ci..."
    npm ci || fail "npm ci gagal. Periksa koneksi internet atau package-lock.json."
fi

# Vite 8 / laravel-vite-plugin 3.1 membutuhkan Node ^20.19 atau >=22.12.
node -e '
const [maj,min] = process.versions.node.split(".").map(Number);
const ok = (maj === 20 && min >= 19) || (maj >= 22 && (maj > 22 || min >= 12));
if (!ok) { console.error(`ERROR: Node ${process.versions.node} terlalu lama. Gunakan Node 20.19+ atau 22.12+.`); process.exit(1); }
'

php artisan optimize:clear --no-interaction
exec composer run dev
