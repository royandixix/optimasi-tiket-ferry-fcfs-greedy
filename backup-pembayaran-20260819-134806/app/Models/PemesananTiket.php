<?php

namespace App\Models;

use App\Support\FerryTariff;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class PemesananTiket extends Model
{
    protected $fillable = [
        'kode_pemesanan',
        'qr_token',
        'penumpang_id',
        'jadwal_id',
        'jenis_tarif',
        'tarif_label',
        'satuan',
        'harga_satuan',
        'jumlah_tiket',
        'total_harga',
        'waktu_pemesanan',
        'status_pemesanan',
        'metode_alokasi',
        'created_by',
        'catatan',
        'digunakan_pada',
        'divalidasi_oleh',
    ];

    protected function casts(): array
    {
        return [
            'waktu_pemesanan' => 'datetime',
            'jumlah_tiket' => 'integer',
            'harga_satuan' => 'integer',
            'total_harga' => 'integer',
            'digunakan_pada' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PemesananTiket $pemesanan): void {
            if (blank($pemesanan->qr_token)) {
                $pemesanan->qr_token = (string) Str::uuid();
            }
        });

        static::saving(function (PemesananTiket $pemesanan): void {
            $mustRecalculate = ! $pemesanan->exists
                || $pemesanan->isDirty(['jenis_tarif', 'jumlah_tiket'])
                || (int) $pemesanan->harga_satuan <= 0
                || (int) $pemesanan->total_harga <= 0;

            if (! $mustRecalculate) {
                return;
            }

            $pricing = FerryTariff::calculate(
                $pemesanan->jenis_tarif,
                $pemesanan->jumlah_tiket
            );

            $pemesanan->jenis_tarif = $pricing['jenis_tarif'];
            $pemesanan->tarif_label = $pricing['tarif_label'];
            $pemesanan->satuan = $pricing['satuan'];
            $pemesanan->harga_satuan = $pricing['harga_satuan'];
            $pemesanan->jumlah_tiket = $pricing['jumlah_tiket'];
            $pemesanan->total_harga = $pricing['total_harga'];
        });
    }

    public function penumpang(): BelongsTo
    {
        return $this->belongsTo(Penumpang::class);
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(
            JadwalKeberangkatan::class,
            'jadwal_id'
        );
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function divalidasiOleh(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'divalidasi_oleh'
        );
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(
            Pembayaran::class,
            'pemesanan_tiket_id'
        );
    }

    public function alokasiTikets(): HasMany
    {
        return $this->hasMany(
            AlokasiTiket::class
        );
    }

    public function validasiTikets(): HasMany
    {
        return $this->hasMany(
            ValidasiTiket::class
        );
    }
}