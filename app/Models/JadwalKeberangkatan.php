<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalKeberangkatan extends Model
{
    protected $fillable = [
        'kapal_id',
        'rute_id',
        'tanggal_berangkat',
        'jam_berangkat',
        'kapasitas_total',
        'kapasitas_terpakai',
        'sisa_kapasitas',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_berangkat' => 'date',
            'kapasitas_total' => 'integer',
            'kapasitas_terpakai' => 'integer',
            'sisa_kapasitas' => 'integer',
        ];
    }

    public function kapal(): BelongsTo
    {
        return $this->belongsTo(Kapal::class);
    }

    public function rute(): BelongsTo
    {
        return $this->belongsTo(Rute::class);
    }

    public function pemesananTikets(): HasMany
    {
        return $this->hasMany(PemesananTiket::class, 'jadwal_id');
    }

    public function alokasiTikets(): HasMany
    {
        return $this->hasMany(AlokasiTiket::class, 'jadwal_id');
    }

    public function hasilOptimasis(): HasMany
    {
        return $this->hasMany(HasilOptimasi::class, 'jadwal_id');
    }
}
