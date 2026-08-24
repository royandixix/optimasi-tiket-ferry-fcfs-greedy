#!/usr/bin/env bash

set -euo pipefail

PROJECT="$HOME/Downloads/optimasi-tiket-ferry"
LARAVEL_PID=""
TUNNEL_PID=""

cd "$PROJECT"

mkdir -p storage/logs

cleanup() {
    echo ""
    echo "Menghentikan Laravel dan Cloudflare..."

    if [ -n "${TUNNEL_PID:-}" ]; then
        kill "$TUNNEL_PID" 2>/dev/null || true
    fi

    if [ -n "${LARAVEL_PID:-}" ]; then
        kill "$LARAVEL_PID" 2>/dev/null || true
    fi

    exit 0
}

trap cleanup INT TERM EXIT

echo "Memperbaiki file .env..."

python3 <<'PY'
from pathlib import Path

env_path = Path(".env")
text = env_path.read_text()

# Memperbaiki ASSET_URL yang menempel pada VITE_APP_NAME.
text = text.replace(
    'VITE_APP_NAME="${APP_NAME}"ASSET_URL=',
    'VITE_APP_NAME="${APP_NAME}"\nASSET_URL='
)

text = text.replace(
    'VITE_APP_NAME="${APP_NAME}".ASSET_URL=',
    'VITE_APP_NAME="${APP_NAME}"\nASSET_URL='
)

lines = []

for line in text.splitlines():
    if line.startswith("APP_URL="):
        continue

    if line.startswith("ASSET_URL="):
        continue

    lines.append(line)

result = []
url_added = False

for line in lines:
    result.append(line)

    if line.startswith("APP_DEBUG="):
        result.append("")
        result.append("APP_URL=http://127.0.0.1:8000")
        url_added = True

if not url_added:
    result.insert(0, "APP_URL=http://127.0.0.1:8000")

env_path.write_text("\n".join(result).rstrip() + "\n")
PY

echo "Menghentikan proses lama..."

pkill -f "php artisan serve" 2>/dev/null || true
pkill -f "cloudflared tunnel" 2>/dev/null || true

sleep 2

echo "Membersihkan cache Laravel..."

php artisan optimize:clear >/dev/null

echo "Menjalankan Laravel..."

php artisan serve \
    --host=127.0.0.1 \
    --port=8000 \
    > storage/logs/laravel-server.log 2>&1 &

LARAVEL_PID=$!

LARAVEL_READY=false

for i in $(seq 1 30); do
    if curl -s --fail http://127.0.0.1:8000 >/dev/null 2>&1; then
        LARAVEL_READY=true
        break
    fi

    sleep 1
done

if [ "$LARAVEL_READY" != "true" ]; then
    echo "Laravel gagal dijalankan."
    cat storage/logs/laravel-server.log
    exit 1
fi

echo "Laravel berhasil dijalankan."
echo "Membuat HTTPS Cloudflare Tunnel..."

rm -f storage/logs/cloudflared.log

cloudflared tunnel \
    --url http://127.0.0.1:8000 \
    > storage/logs/cloudflared.log 2>&1 &

TUNNEL_PID=$!

TUNNEL_URL=""

for i in $(seq 1 60); do
    TUNNEL_URL=$(
        grep -Eo \
        'https://[a-zA-Z0-9-]+\.trycloudflare\.com' \
        storage/logs/cloudflared.log \
        | head -n 1 \
        || true
    )

    if [ -n "$TUNNEL_URL" ]; then
        break
    fi

    if ! kill -0 "$TUNNEL_PID" 2>/dev/null; then
        echo "Proses Cloudflare berhenti."
        cat storage/logs/cloudflared.log
        exit 1
    fi

    sleep 1
done

if [ -z "$TUNNEL_URL" ]; then
    echo "Cloudflare gagal menghasilkan URL."
    cat storage/logs/cloudflared.log
    exit 1
fi

echo "Memasukkan URL Cloudflare ke .env..."

TUNNEL_URL="$TUNNEL_URL" python3 <<'PY'
import os
from pathlib import Path

url = os.environ["TUNNEL_URL"]
env_path = Path(".env")
lines = env_path.read_text().splitlines()

result = []
url_added = False

for line in lines:
    if line.startswith("APP_URL="):
        continue

    if line.startswith("ASSET_URL="):
        continue

    result.append(line)

    if line.startswith("APP_DEBUG="):
        result.append("")
        result.append(f"APP_URL={url}")
        result.append(f"ASSET_URL={url}")
        url_added = True

if not url_added:
    result.insert(0, f"ASSET_URL={url}")
    result.insert(0, f"APP_URL={url}")

env_path.write_text("\n".join(result).rstrip() + "\n")
PY

php artisan optimize:clear >/dev/null

echo "Memulai ulang Laravel dengan URL HTTPS..."

kill "$LARAVEL_PID" 2>/dev/null || true
wait "$LARAVEL_PID" 2>/dev/null || true

php artisan serve \
    --host=127.0.0.1 \
    --port=8000 \
    > storage/logs/laravel-server.log 2>&1 &

LARAVEL_PID=$!

for i in $(seq 1 30); do
    if curl -s --fail http://127.0.0.1:8000 >/dev/null 2>&1; then
        break
    fi

    sleep 1
done

echo ""
echo "=========================================================="
echo "PROJECT BERHASIL DIJALANKAN"
echo "=========================================================="
echo ""
echo "ADMIN:"
echo "$TUNNEL_URL/admin"
echo ""
echo "VALIDASI TIKET:"
echo "$TUNNEL_URL/admin/validasi-tikets"
echo ""
echo "Jangan tutup Terminal ini."
echo "Tekan Control + C untuk menghentikan semuanya."
echo "=========================================================="
echo ""

wait "$TUNNEL_PID"
