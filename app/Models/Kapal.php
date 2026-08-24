<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kapal extends Model
{
    protected $fillable = [
        'kode_kapal',
        'nama_kapal',
        'kapasitas_penumpang',
        'status',
        'gambar_kapal',
    ];

    protected function casts(): array
    {
        return [
            'gambar_kapal' => 'array',
        ];
    }

    public function jadwalKeberangkatans(): HasMany
    {
        return $this->hasMany(JadwalKeberangkatan::class);
    }
}
