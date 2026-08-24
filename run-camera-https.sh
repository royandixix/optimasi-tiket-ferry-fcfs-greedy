#!/usr/bin/env bash

set -Eeuo pipefail

PROJECT="$HOME/Downloads/optimasi-tiket-ferry"
LOG_DIR="$PROJECT/storage/logs"

LARAVEL_PID=""
TUNNEL_PID=""
LARAVEL_STARTED=false

cd "$PROJECT"
mkdir -p "$LOG_DIR"

cleanup() {
    echo ""
    echo "Menghentikan Cloudflare Tunnel..."

    if [ -n "${TUNNEL_PID:-}" ]; then
        kill "$TUNNEL_PID" 2>/dev/null || true
    fi

    if [ "$LARAVEL_STARTED" = true ] && [ -n "${LARAVEL_PID:-}" ]; then
        echo "Menghentikan Laravel..."
        kill "$LARAVEL_PID" 2>/dev/null || true
    fi
}

trap cleanup EXIT INT TERM

if [ ! -f artisan ]; then
    echo "ERROR: artisan tidak ditemukan."
    echo "Folder project salah: $PROJECT"
    exit 1
fi

echo "Membersihkan proses Cloudflare lama..."
pkill -f "cloudflared tunnel" 2>/dev/null || true
sleep 2

echo "Memeriksa Laravel pada port 8000..."

if curl -fsS http://127.0.0.1:8000 >/dev/null 2>&1; then
    echo "Laravel sudah berjalan."
else
    EXISTING_PID=$(
        lsof -tiTCP:8000 -sTCP:LISTEN 2>/dev/null || true
    )

    if [ -n "$EXISTING_PID" ]; then
        echo "$EXISTING_PID" | xargs kill 2>/dev/null || true
        sleep 2
    fi

    echo "Menjalankan Laravel..."

    php artisan serve \
        --host=127.0.0.1 \
        --port=8000 \
        > "$LOG_DIR/laravel-camera.log" 2>&1 &

    LARAVEL_PID=$!
    LARAVEL_STARTED=true

    for i in $(seq 1 30); do
        if curl -fsS http://127.0.0.1:8000 >/dev/null 2>&1; then
            break
        fi

        sleep 1
    done

    if ! curl -fsS http://127.0.0.1:8000 >/dev/null 2>&1; then
        echo "ERROR: Laravel gagal dijalankan."
        cat "$LOG_DIR/laravel-camera.log"
        exit 1
    fi
fi

echo "Laravel aktif pada http://127.0.0.1:8000"
echo "Membuat HTTPS Cloudflare Tunnel..."

rm -f "$LOG_DIR/cloudflared-camera.log"

cloudflared tunnel \
    --protocol http2 \
    --url http://127.0.0.1:8000 \
    > "$LOG_DIR/cloudflared-camera.log" 2>&1 &

TUNNEL_PID=$!

TUNNEL_URL=""

for i in $(seq 1 90); do
    TUNNEL_URL=$(
        grep -Eo \
        'https://[a-zA-Z0-9-]+\.trycloudflare\.com' \
        "$LOG_DIR/cloudflared-camera.log" \
        | head -n 1 \
        || true
    )

    if [ -n "$TUNNEL_URL" ]; then
        break
    fi

    if ! kill -0 "$TUNNEL_PID" 2>/dev/null; then
        echo "ERROR: Cloudflare berhenti."
        cat "$LOG_DIR/cloudflared-camera.log"
        exit 1
    fi

    sleep 1
done

if [ -z "$TUNNEL_URL" ]; then
    echo "ERROR: URL HTTPS tidak berhasil dibuat."
    cat "$LOG_DIR/cloudflared-camera.log"
    exit 1
fi

echo "Memperbaiki konfigurasi .env..."

TUNNEL_URL="$TUNNEL_URL" python3 <<'PY'
import os
from pathlib import Path

env_path = Path(".env")
url = os.environ["TUNNEL_URL"]

text = env_path.read_text()

# Memperbaiki kesalahan lama jika ASSET_URL menempel.
text = text.replace(
    'VITE_APP_NAME="${APP_NAME}"ASSET_URL=',
    'VITE_APP_NAME="${APP_NAME}"\nASSET_URL=',
)

text = text.replace(
    'VITE_APP_NAME="${APP_NAME}".',
    'VITE_APP_NAME="${APP_NAME}"',
)

lines = text.splitlines()
cleaned = []

for line in lines:
    if line.startswith("APP_URL="):
        continue

    if line.startswith("ASSET_URL="):
        continue

    if line.startswith("SESSION_SECURE_COOKIE="):
        continue

    cleaned.append(line)

result = []
inserted = False

for line in cleaned:
    result.append(line)

    if line.startswith("APP_DEBUG=") and not inserted:
        result.extend([
            "",
            f"APP_URL={url}",
            f"ASSET_URL={url}",
        ])
        inserted = True

if not inserted:
    result = [
        f"APP_URL={url}",
        f"ASSET_URL={url}",
        "",
        *result,
    ]

session_index = None

for index, line in enumerate(result):
    if line.startswith("SESSION_DOMAIN="):
        session_index = index + 1
        break

if session_index is None:
    result.append("SESSION_SECURE_COOKIE=true")
else:
    result.insert(session_index, "SESSION_SECURE_COOKIE=true")

env_path.write_text("\n".join(result).rstrip() + "\n")
PY

php artisan optimize:clear >/dev/null

echo "Menunggu Laravel memuat konfigurasi HTTPS..."
sleep 4

for i in $(seq 1 20); do
    if curl -fsS http://127.0.0.1:8000 >/dev/null 2>&1; then
        break
    fi

    sleep 1
done

echo "Memeriksa alamat Cloudflare..."

for i in $(seq 1 30); do
    if curl -fsSI "$TUNNEL_URL/admin" >/dev/null 2>&1; then
        break
    fi

    sleep 2
done

echo ""
echo "=============================================================="
echo "HTTPS DAN KAMERA SIAP DIGUNAKAN"
echo "=============================================================="
echo ""
echo "LINK USER:"
echo "$TUNNEL_URL"
echo ""
echo "LINK ADMIN:"
echo "$TUNNEL_URL/admin"
echo ""
echo "LINK VALIDASI TIKET:"
echo "$TUNNEL_URL/admin/validasi-tikets"
echo ""
echo "Jangan tutup Terminal ini."
echo "Tekan Control + C hanya setelah selesai."
echo "=============================================================="
echo ""

wait "$TUNNEL_PID"
