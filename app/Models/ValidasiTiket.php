<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValidasiTiket extends Model
{
    protected $fillable = [
        'pemesanan_tiket_id',
        'divalidasi_oleh',
        'metode_validasi',
        'nilai_diperiksa',
        'status_validasi',
        'alasan_gagal',
        'waktu_validasi',
    ];

    protected function casts(): array
    {
        return [
            'waktu_validasi' => 'datetime',
        ];
    }

    public function pemesananTiket(): BelongsTo
    {
        return $this->belongsTo(PemesananTiket::class);
    }

    public function divalidasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh');
    }
}