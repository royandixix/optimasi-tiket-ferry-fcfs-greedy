#!/usr/bin/env bash
set -euo pipefail

if [ ! -f artisan ]; then
  echo "ERROR: Jalankan script ini dari folder utama project Laravel (yang ada file artisan)."
  exit 1
fi

STAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="backup-pembayaran-$STAMP"
mkdir -p "$BACKUP_DIR"

backup_file() {
  if [ -f "$1" ]; then
    mkdir -p "$BACKUP_DIR/$(dirname "$1")"
    cp "$1" "$BACKUP_DIR/$1"
  fi
}

backup_file app/Models/PemesananTiket.php
backup_file app/Http/Controllers/User/PemesananTiketController.php
backup_file resources/views/user/pemesanan/create.blade.php
backup_file resources/views/user/pemesanan/show.blade.php
backup_file .env

mkdir -p app/Models
mkdir -p config
mkdir -p resources/views/user/pemesanan/partials
mkdir -p app/Filament/Resources/Pembayarans/Pages

if ! ls database/migrations/*create_pembayarans_table.php >/dev/null 2>&1; then
  MIGRATION="database/migrations/$(date +%Y_%m_%d_%H%M%S)_create_pembayarans_table.php"
  cat > "$MIGRATION" <<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pemesanan_tiket_id')->unique()->constrained('pemesanan_tikets')->cascadeOnDelete();
            $table->string('kode_pembayaran')->unique();
            $table->string('metode_pembayaran', 30);
            $table->unsignedBigInteger('total_bayar');
            $table->string('bukti_transfer')->nullable();
            $table->string('status_pembayaran', 30);
            $table->timestamp('dibayar_pada')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
            $table->index('metode_pembayaran');
            $table->index('status_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
PHP
fi

cat > app/Models/Pembayaran.php <<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Pembayaran extends Model
{
    protected $fillable = [
        'pemesanan_tiket_id',
        'kode_pembayaran',
        'metode_pembayaran',
        'total_bayar',
        'bukti_transfer',
        'status_pembayaran',
        'dibayar_pada',
        'diverifikasi_oleh',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'total_bayar' => 'integer',
            'dibayar_pada' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Pembayaran $pembayaran): void {
            if (blank($pembayaran->kode_pembayaran)) {
                $prefix = $pembayaran->metode_pembayaran === 'transfer_bank' ? 'TRF' : 'CASH';

                do {
                    $kode = $prefix . '-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
                } while (static::query()->where('kode_pembayaran', $kode)->exists());

                $pembayaran->kode_pembayaran = $kode;
            }
        });

        static::deleted(function (Pembayaran $pembayaran): void {
            if ($pembayaran->bukti_transfer) {
                Storage::disk('public')->delete($pembayaran->bukti_transfer);
            }
        });
    }

    public function pemesananTiket(): BelongsTo
    {
        return $this->belongsTo(PemesananTiket::class, 'pemesanan_tiket_id');
    }

    public function diverifikasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
PHP

python3 <<'PY'
from pathlib import Path
p = Path('app/Models/PemesananTiket.php')
s = p.read_text()
if 'Relations\\HasOne;' not in s:
    s = s.replace(
        'use Illuminate\\Database\\Eloquent\\Relations\\HasMany;\n',
        'use Illuminate\\Database\\Eloquent\\Relations\\HasMany;\nuse Illuminate\\Database\\Eloquent\\Relations\\HasOne;\n'
    )
if 'function pembayaran()' not in s:
    marker = '    public function alokasiTikets(): HasMany\n'
    method = "    public function pembayaran(): HasOne\n    {\n        return $this->hasOne(Pembayaran::class, 'pemesanan_tiket_id');\n    }\n\n"
    if marker not in s:
        raise SystemExit('Gagal menemukan posisi relasi pembayaran di PemesananTiket.php')
    s = s.replace(marker, method + marker, 1)
p.write_text(s)
PY

cat > config/ferry_payment.php <<'PHP'
<?php

return [
    'bank_name' => env('FERRY_BANK_NAME', 'BRI'),
    'account_number' => env('FERRY_BANK_ACCOUNT_NUMBER', '1234567890'),
    'account_name' => env('FERRY_BANK_ACCOUNT_NAME', 'Nama Pemilik Rekening'),
];
PHP

for line in \
  'FERRY_BANK_NAME=BRI' \
  'FERRY_BANK_ACCOUNT_NUMBER=1234567890' \
  'FERRY_BANK_ACCOUNT_NAME="Nama Pemilik Rekening"'
do
  key="${line%%=*}"
  if ! grep -q "^${key}=" .env; then
    printf '\n%s\n' "$line" >> .env
  fi
done

cat > resources/views/user/pemesanan/partials/payment-fields.blade.php <<'BLADE'
<div class="col-12">
    <div class="border rounded-4 p-4">
        <h5 class="mb-1">Metode Pembayaran</h5>
        <p class="text-muted mb-4">Pilih cash di tempat atau transfer bank.</p>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="border rounded-4 p-3 w-100 h-100">
                    <div class="form-check m-0">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="metode_pembayaran"
                            id="payment-cash"
                            value="cash"
                            {{ old('metode_pembayaran', 'cash') === 'cash' ? 'checked' : '' }}
                        >
                        <span class="form-check-label fw-semibold">Cash di Tempat</span>
                    </div>
                    <div class="small text-muted mt-2">
                        Bayar di tempat menggunakan kode pembayaran yang dibuat setelah pemesanan berhasil.
                    </div>
                </label>
            </div>

            <div class="col-md-6">
                <label class="border rounded-4 p-3 w-100 h-100">
                    <div class="form-check m-0">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="metode_pembayaran"
                            id="payment-transfer"
                            value="transfer_bank"
                            {{ old('metode_pembayaran') === 'transfer_bank' ? 'checked' : '' }}
                        >
                        <span class="form-check-label fw-semibold">Transfer Bank</span>
                    </div>
                    <div class="small text-muted mt-2">
                        Transfer ke rekening tujuan lalu upload gambar bukti transaksi.
                    </div>
                </label>
            </div>
        </div>

        <div id="transfer-payment-fields" class="mt-4 d-none">
            <div class="alert alert-info rounded-4">
                <div class="fw-semibold mb-2">Rekening Tujuan</div>
                <div>Bank: {{ config('ferry_payment.bank_name') }}</div>
                <div>No. Rekening: {{ config('ferry_payment.account_number') }}</div>
                <div>Atas Nama: {{ config('ferry_payment.account_name') }}</div>
            </div>

            <label for="bukti_transfer" class="form-label fw-semibold">Bukti Transfer</label>
            <input
                type="file"
                name="bukti_transfer"
                id="bukti_transfer"
                class="form-control"
                accept="image/jpeg,image/png,image/webp"
            >
            <div class="form-text">Format JPG, JPEG, PNG, atau WEBP. Maksimal 5 MB.</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cash = document.getElementById('payment-cash');
    const transfer = document.getElementById('payment-transfer');
    const transferFields = document.getElementById('transfer-payment-fields');
    const proof = document.getElementById('bukti_transfer');

    function updatePaymentFields() {
        const isTransfer = transfer && transfer.checked;

        if (transferFields) {
            transferFields.classList.toggle('d-none', !isTransfer);
        }

        if (proof) {
            proof.required = Boolean(isTransfer);
        }
    }

    cash?.addEventListener('change', updatePaymentFields);
    transfer?.addEventListener('change', updatePaymentFields);
    updatePaymentFields();
});
</script>
BLADE

python3 <<'PY'
from pathlib import Path
p = Path('resources/views/user/pemesanan/create.blade.php')
s = p.read_text()
s = s.replace(
    '<form method="POST" action="{{ route(\'user.pemesanan.store\') }}" id="booking-form">',
    '<form method="POST" action="{{ route(\'user.pemesanan.store\') }}" id="booking-form" enctype="multipart/form-data">'
)
include = "                    @include('user.pemesanan.partials.payment-fields')\n\n"
needle = "                    <div class=\"col-12\">\n                        <label for=\"catatan\" class=\"form-label fw-semibold\">"
if "partials.payment-fields" not in s:
    if needle not in s:
        raise SystemExit('Gagal menemukan posisi payment-fields pada create.blade.php')
    s = s.replace(needle, include + needle, 1)
p.write_text(s)
PY

python3 <<'PY'
from pathlib import Path
import re
p = Path('app/Http/Controllers/User/PemesananTiketController.php')
s = p.read_text()

if 'use App\\Models\\Pembayaran;' not in s:
    s = s.replace('use App\\Models\\PemesananTiket;\n', 'use App\\Models\\PemesananTiket;\nuse App\\Models\\Pembayaran;\n')
if 'use Illuminate\\Support\\Facades\\Storage;' not in s:
    s = s.replace('use Illuminate\\Support\\Facades\\DB;\n', 'use Illuminate\\Support\\Facades\\DB;\nuse Illuminate\\Support\\Facades\\Storage;\n')
if 'use Throwable;' not in s:
    s = s.replace('use Illuminate\\Validation\\ValidationException;\n', 'use Illuminate\\Validation\\ValidationException;\nuse Throwable;\n')

s = s.replace(
    "->with(['jadwal.kapal', 'jadwal.rute'])",
    "->with(['jadwal.kapal', 'jadwal.rute', 'pembayaran'])",
    1
)

store = r'''    public function store(Request $request)
    {
        $validated = $this->validateBooking($request, true);
        $penumpang = $this->getOrCreatePenumpang();
        $pricing = FerryTariff::calculate(
            $validated['jenis_tarif'],
            $validated['jumlah_tiket']
        );

        $buktiTransfer = null;

        if (
            $validated['metode_pembayaran'] === 'transfer_bank'
            && $request->hasFile('bukti_transfer')
        ) {
            $buktiTransfer = $request
                ->file('bukti_transfer')
                ->store('bukti-transfer', 'public');
        }

        try {
            $pemesanan = DB::transaction(function () use (
                $validated,
                $penumpang,
                $pricing,
                $buktiTransfer
            ): PemesananTiket {
                $jadwal = $this->lockBookableSchedule((int) $validated['jadwal_id']);

                $this->validateQuantityAgainstShipCapacity(
                    $pricing['jumlah_tiket'],
                    $jadwal
                );

                $pemesanan = PemesananTiket::create([
                    'kode_pemesanan' => 'PM-'
                        . now()->format('YmdHis')
                        . '-'
                        . Str::upper(Str::random(4)),
                    'penumpang_id' => $penumpang->id,
                    'jadwal_id' => $validated['jadwal_id'],
                    'jenis_tarif' => $pricing['jenis_tarif'],
                    'tarif_label' => $pricing['tarif_label'],
                    'satuan' => $pricing['satuan'],
                    'harga_satuan' => $pricing['harga_satuan'],
                    'jumlah_tiket' => $pricing['jumlah_tiket'],
                    'total_harga' => $pricing['total_harga'],
                    'waktu_pemesanan' => now(),
                    'status_pemesanan' => 'pending',
                    'metode_alokasi' => null,
                    'created_by' => Auth::guard('web')->id(),
                    'catatan' => $validated['catatan'] ?? null,
                ]);

                Pembayaran::create([
                    'pemesanan_tiket_id' => $pemesanan->id,
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'total_bayar' => $pricing['total_harga'],
                    'bukti_transfer' => $buktiTransfer,
                    'status_pembayaran' => $validated['metode_pembayaran'] === 'cash'
                        ? 'menunggu_pembayaran'
                        : 'menunggu_verifikasi',
                ]);

                return $pemesanan;
            });
        } catch (Throwable $exception) {
            if ($buktiTransfer) {
                Storage::disk('public')->delete($buktiTransfer);
            }

            throw $exception;
        }

        return redirect()
            ->route('user.pemesanan.show', $pemesanan)
            ->with('success', 'Pemesanan dan data pembayaran berhasil dibuat.');
    }

'''
pattern = re.compile(r"    public function store\(Request \$request\)\n    \{.*?\n    \}\n\n(?=    public function show\()", re.S)
if not pattern.search(s):
    raise SystemExit('Gagal menemukan method store() pada controller')
s = pattern.sub(store, s, count=1)

s = s.replace(
    "            'alokasiTikets',\n        ]);",
    "            'alokasiTikets',\n            'pembayaran',\n        ]);",
    1
)

validate = r'''    private function validateBooking(
        Request $request,
        bool $withPayment = false
    ): array {
        $rules = [
            'jadwal_id' => [
                'required',
                'exists:jadwal_keberangkatans,id',
            ],
            'jenis_tarif' => [
                'required',
                Rule::in(FerryTariff::codes()),
            ],
            'jumlah_tiket' => [
                'required',
                'integer',
                'min:1',
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];

        if ($withPayment) {
            $rules['metode_pembayaran'] = [
                'required',
                Rule::in(['cash', 'transfer_bank']),
            ];

            $rules['bukti_transfer'] = [
                Rule::requiredIf(
                    fn (): bool => $request->input('metode_pembayaran') === 'transfer_bank'
                ),
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ];
        }

        return $request->validate($rules, [
            'jadwal_id.required' => 'Jadwal keberangkatan wajib dipilih.',
            'jadwal_id.exists' => 'Jadwal keberangkatan tidak valid.',
            'jenis_tarif.required' => 'Jenis tarif wajib dipilih.',
            'jenis_tarif.in' => 'Jenis tarif yang dipilih tidak valid.',
            'jumlah_tiket.required' => 'Jumlah tiket atau unit wajib diisi.',
            'jumlah_tiket.integer' => 'Jumlah harus berupa angka bulat.',
            'jumlah_tiket.min' => 'Jumlah minimal 1.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in' => 'Metode pembayaran tidak valid.',
            'bukti_transfer.required' => 'Bukti transfer wajib diunggah.',
            'bukti_transfer.image' => 'Bukti transfer harus berupa gambar.',
            'bukti_transfer.mimes' => 'Bukti transfer harus JPG, JPEG, PNG, atau WEBP.',
            'bukti_transfer.max' => 'Ukuran bukti transfer maksimal 5 MB.',
        ]);
    }

'''
pattern = re.compile(r"    private function validateBooking\(Request \$request\): array\n    \{.*?\n    \}\n\n(?=    private function getOrCreatePenumpang\()", re.S)
if not pattern.search(s):
    if 'bool $withPayment = false' not in s:
        raise SystemExit('Gagal menemukan method validateBooking() pada controller')
else:
    s = pattern.sub(validate, s, count=1)


needle_update = """            $pemesanan->update([\n                'jadwal_id' => $validated['jadwal_id'],\n                'jenis_tarif' => $pricing['jenis_tarif'],\n                'tarif_label' => $pricing['tarif_label'],\n                'satuan' => $pricing['satuan'],\n                'harga_satuan' => $pricing['harga_satuan'],\n                'jumlah_tiket' => $pricing['jumlah_tiket'],\n                'total_harga' => $pricing['total_harga'],\n                'catatan' => $validated['catatan'] ?? null,\n            ]);\n"""
replacement_update = needle_update + """\n            $pemesanan->pembayaran()?->update([\n                'total_bayar' => $pricing['total_harga'],\n            ]);\n"""
if needle_update in s and "$pemesanan->pembayaran()?->update" not in s:
    s = s.replace(needle_update, replacement_update, 1)

p.write_text(s)
PY

cat > resources/views/user/pemesanan/partials/payment-summary.blade.php <<'BLADE'
@php
    $pembayaran = $pemesanan->pembayaran;

    $statusClass = match ($pembayaran?->status_pembayaran) {
        'menunggu_pembayaran' => 'warning',
        'menunggu_verifikasi' => 'info',
        'dibayar' => 'success',
        'ditolak' => 'danger',
        default => 'secondary',
    };

    $statusLabel = match ($pembayaran?->status_pembayaran) {
        'menunggu_pembayaran' => 'Menunggu Pembayaran',
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'dibayar' => 'Dibayar',
        'ditolak' => 'Ditolak',
        default => '-',
    };
@endphp

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h5 class="mb-1">Informasi Pembayaran</h5>
                <div class="text-muted small">Detail metode dan status pembayaran tiket.</div>
            </div>

            @if ($pembayaran)
                <span class="badge text-bg-{{ $statusClass }}">{{ $statusLabel }}</span>
            @endif
        </div>

        @if ($pembayaran)
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="small text-muted">Kode Pembayaran</div>
                        <div class="fw-bold fs-5">{{ $pembayaran->kode_pembayaran }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="small text-muted">Metode Pembayaran</div>
                        <div class="fw-semibold">
                            {{ $pembayaran->metode_pembayaran === 'cash' ? 'Cash di Tempat' : 'Transfer Bank' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="small text-muted">Total Bayar</div>
                        <div class="fw-bold text-success fs-5">
                            Rp{{ number_format((float) $pembayaran->total_bayar, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="small text-muted">Status</div>
                        <span class="badge text-bg-{{ $statusClass }} mt-1">{{ $statusLabel }}</span>
                    </div>
                </div>
            </div>

            @if ($pembayaran->metode_pembayaran === 'cash')
                <div class="alert alert-warning rounded-4 mt-4 mb-0">
                    Tunjukkan kode pembayaran <strong>{{ $pembayaran->kode_pembayaran }}</strong> saat melakukan pembayaran di tempat.
                </div>
            @endif

            @if ($pembayaran->metode_pembayaran === 'transfer_bank')
                <div class="alert alert-info rounded-4 mt-4">
                    <div class="fw-semibold mb-2">Transfer Bank</div>
                    <div>Bank: {{ config('ferry_payment.bank_name') }}</div>
                    <div>No. Rekening: {{ config('ferry_payment.account_number') }}</div>
                    <div>Atas Nama: {{ config('ferry_payment.account_name') }}</div>
                </div>

                @if ($pembayaran->bukti_transfer)
                    <div>
                        <div class="fw-semibold mb-2">Bukti Transfer</div>
                        <a href="{{ asset('storage/' . $pembayaran->bukti_transfer) }}" target="_blank" rel="noopener">
                            <img
                                src="{{ asset('storage/' . $pembayaran->bukti_transfer) }}"
                                alt="Bukti Transfer"
                                class="img-fluid rounded-4 border"
                                style="max-height: 320px;"
                            >
                        </a>
                    </div>
                @endif
            @endif

            @if ($pembayaran->catatan_admin)
                <div class="alert alert-light border rounded-4 mt-4 mb-0">
                    <div class="fw-semibold">Catatan Admin</div>
                    <div>{{ $pembayaran->catatan_admin }}</div>
                </div>
            @endif
        @else
            <div class="alert alert-secondary mb-0">Data pembayaran belum tersedia untuk pemesanan ini.</div>
        @endif
    </div>
</div>
BLADE

python3 <<'PY'
from pathlib import Path
p = Path('resources/views/user/pemesanan/show.blade.php')
s = p.read_text()
include = "        @include('user.pemesanan.partials.payment-summary')\n\n"
needle = "        @if ($pemesanan->status_pemesanan === 'diterima')"
if "partials.payment-summary" not in s:
    if needle not in s:
        raise SystemExit('Gagal menemukan posisi payment-summary pada show.blade.php')
    s = s.replace(needle, include + needle, 1)
p.write_text(s)
PY

cat > app/Filament/Resources/Pembayarans/PembayaranResource.php <<'PHP'
<?php

namespace App\Filament\Resources\Pembayarans;

use App\Filament\Resources\Pembayarans\Pages\EditPembayaran;
use App\Filament\Resources\Pembayarans\Pages\ListPembayarans;
use App\Models\Pembayaran;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $recordTitleAttribute = 'kode_pembayaran';

    protected static ?string $navigationLabel = 'Pembayaran';

    protected static ?string $modelLabel = 'Pembayaran';

    protected static ?string $pluralModelLabel = 'Data Pembayaran';

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi Tiket';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewBookingData() ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->canViewBookingData() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->canManageBookingData() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Pembayaran')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('kode_pembayaran')
                        ->label('Kode Pembayaran')
                        ->disabled()
                        ->dehydrated(false),

                    Select::make('pemesanan_tiket_id')
                        ->label('Kode Pemesanan')
                        ->relationship('pemesananTiket', 'kode_pemesanan')
                        ->disabled()
                        ->dehydrated(false),

                    Select::make('metode_pembayaran')
                        ->label('Metode Pembayaran')
                        ->options([
                            'cash' => 'Cash di Tempat',
                            'transfer_bank' => 'Transfer Bank',
                        ])
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('total_bayar')
                        ->label('Total Bayar')
                        ->prefix('Rp')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(false),

                    FileUpload::make('bukti_transfer')
                        ->label('Bukti Transfer')
                        ->disk('public')
                        ->directory('bukti-transfer')
                        ->visibility('public')
                        ->image()
                        ->openable()
                        ->downloadable()
                        ->deletable(false)
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ]),

            Section::make('Verifikasi Admin')
                ->columnSpanFull()
                ->schema([
                    Select::make('status_pembayaran')
                        ->label('Status Pembayaran')
                        ->options([
                            'menunggu_pembayaran' => 'Menunggu Pembayaran',
                            'menunggu_verifikasi' => 'Menunggu Verifikasi',
                            'dibayar' => 'Dibayar',
                            'ditolak' => 'Ditolak',
                        ])
                        ->native(false)
                        ->required(),

                    Textarea::make('catatan_admin')
                        ->label('Catatan Admin')
                        ->rows(3)
                        ->maxLength(1000),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('kode_pembayaran')
                    ->label('Kode Bayar')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pemesananTiket.kode_pemesanan')
                    ->label('Kode Pemesanan')
                    ->searchable(),

                TextColumn::make('pemesananTiket.penumpang.nama_penumpang')
                    ->label('Penumpang')
                    ->searchable(),

                TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Cash di Tempat',
                        'transfer_bank' => 'Transfer Bank',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'warning',
                        'transfer_bank' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('total_bayar')
                    ->label('Total Bayar')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                ImageColumn::make('bukti_transfer')
                    ->label('Bukti Transfer')
                    ->disk('public')
                    ->imageHeight(56),

                TextColumn::make('status_pembayaran')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'menunggu_pembayaran' => 'Menunggu Pembayaran',
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'dibayar' => 'Dibayar',
                        'ditolak' => 'Ditolak',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu_pembayaran' => 'warning',
                        'menunggu_verifikasi' => 'info',
                        'dibayar' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->options([
                        'cash' => 'Cash di Tempat',
                        'transfer_bank' => 'Transfer Bank',
                    ]),

                SelectFilter::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->options([
                        'menunggu_pembayaran' => 'Menunggu Pembayaran',
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'dibayar' => 'Dibayar',
                        'ditolak' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                EditAction::make()->label('Verifikasi'),
            ])
            ->emptyStateHeading('Belum ada pembayaran')
            ->emptyStateDescription('Pembayaran akan muncul setelah user membuat pemesanan.');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::query()
            ->whereIn('status_pembayaran', [
                'menunggu_pembayaran',
                'menunggu_verifikasi',
            ])
            ->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pembayaran yang belum selesai';
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPembayarans::route('/'),
            'edit' => EditPembayaran::route('/{record}/edit'),
        ];
    }
}
PHP

cat > app/Filament/Resources/Pembayarans/Pages/ListPembayarans.php <<'PHP'
<?php

namespace App\Filament\Resources\Pembayarans\Pages;

use App\Filament\Resources\Pembayarans\PembayaranResource;
use Filament\Resources\Pages\ListRecords;

class ListPembayarans extends ListRecords
{
    protected static string $resource = PembayaranResource::class;

    protected static ?string $title = 'Pembayaran';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
PHP

cat > app/Filament/Resources/Pembayarans/Pages/EditPembayaran.php <<'PHP'
<?php

namespace App\Filament\Resources\Pembayarans\Pages;

use App\Filament\Resources\Pembayarans\PembayaranResource;
use Filament\Resources\Pages\EditRecord;

class EditPembayaran extends EditRecord
{
    protected static string $resource = PembayaranResource::class;

    protected static ?string $title = 'Verifikasi Pembayaran';

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (in_array($data['status_pembayaran'], ['dibayar', 'ditolak'], true)) {
            $data['diverifikasi_oleh'] = auth()->id();
        } else {
            $data['diverifikasi_oleh'] = null;
        }

        $data['dibayar_pada'] = $data['status_pembayaran'] === 'dibayar'
            ? now()
            : null;

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Status pembayaran berhasil diperbarui';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
PHP

php -l app/Models/Pembayaran.php
php -l app/Models/PemesananTiket.php
php -l app/Http/Controllers/User/PemesananTiketController.php
php -l app/Filament/Resources/Pembayarans/PembayaranResource.php
php -l app/Filament/Resources/Pembayarans/Pages/ListPembayarans.php
php -l app/Filament/Resources/Pembayarans/Pages/EditPembayaran.php

php artisan storage:link >/dev/null 2>&1 || true
php artisan optimize:clear

echo ""
echo "Menjalankan migration..."
if ! php artisan migrate --force; then
  echo ""
  echo "Kode pembayaran sudah dipasang, tetapi migration gagal."
  echo "Pastikan MySQL/database aktif, lalu jalankan: php artisan migrate"
  echo "Backup file lama: $BACKUP_DIR"
  exit 1
fi

echo ""
echo "SELESAI"
echo "Backup file lama: $BACKUP_DIR"
echo ""
echo "Edit rekening di .env jika perlu:"
echo "FERRY_BANK_NAME=BRI"
echo "FERRY_BANK_ACCOUNT_NUMBER=1234567890"
echo "FERRY_BANK_ACCOUNT_NAME=Nama Pemilik Rekening"
echo ""
echo "Jalankan: php artisan serve"
