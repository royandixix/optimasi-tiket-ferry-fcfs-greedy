<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rute extends Model
{
    protected $fillable = [
        'pelabuhan_asal',
        'pelabuhan_tujuan',
        'keterangan',
        'status',
    ];

    public function jadwalKeberangkatans(): HasMany
    {
        return $this->hasMany(JadwalKeberangkatan::class);
    }
}