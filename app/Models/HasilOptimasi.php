<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilOptimasi extends Model
{
    protected $fillable = [
        'jadwal_id',
        'metode',
        'total_pemesanan',
        'total_tiket_diminta',
        'total_tiket_diterima',
        'total_tiket_ditolak',
        'kapasitas_kapal',
        'kapasitas_terpakai',
        'load_factor',
        'waktu_proses_ms',
        'diproses_oleh',
    ];

    protected function casts(): array
    {
        return [
            'total_pemesanan' => 'integer',
            'total_tiket_diminta' => 'integer',
            'total_tiket_diterima' => 'integer',
            'total_tiket_ditolak' => 'integer',
            'kapasitas_kapal' => 'integer',
            'kapasitas_terpakai' => 'integer',
            'load_factor' => 'decimal:2',
            'waktu_proses_ms' => 'decimal:4',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(JadwalKeberangkatan::class, 'jadwal_id');
    }

    public function diprosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}