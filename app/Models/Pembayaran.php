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
