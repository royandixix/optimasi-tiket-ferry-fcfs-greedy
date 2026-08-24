<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penumpang extends Model
{
    protected $fillable = [
        'user_id',
        'nik',
        'nama_penumpang',
        'jenis_kelamin',
        'no_hp',
        'alamat',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pemesananTikets(): HasMany
    {
        return $this->hasMany(PemesananTiket::class);
    }
}