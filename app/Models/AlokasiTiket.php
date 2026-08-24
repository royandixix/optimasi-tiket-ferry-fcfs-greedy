<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlokasiTiket extends Model
{
    protected $fillable = [
        'pemesanan_tiket_id',
        'jadwal_id',
        'metode',
        'jumlah_dialokasikan',
        'nilai_prioritas',
        'sisa_kapasitas_sebelum',
        'sisa_kapasitas_sesudah',
        'status_alokasi',
        'diproses_oleh',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_dialokasikan' => 'integer',
            'nilai_prioritas' => 'integer',
            'sisa_kapasitas_sebelum' => 'integer',
            'sisa_kapasitas_sesudah' => 'integer',
        ];
    }

    public function pemesananTiket(): BelongsTo
    {
        return $this->belongsTo(PemesananTiket::class);
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