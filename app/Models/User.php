<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_PETUGAS = 'petugas';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status === 'aktif'
            && in_array(
                $this->role,
                [
                    self::ROLE_ADMIN,
                    self::ROLE_PETUGAS,
                ],
                true
            );
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPetugas(): bool
    {
        return $this->role === self::ROLE_PETUGAS;
    }

    public function isPenumpang(): bool
    {
        return $this->isUser();
    }

    public function isSuperAdmin(): bool
    {
        return $this->isAdmin();
    }

    public function isPimpinan(): bool
    {
        return false;
    }

    public function isInternalUser(): bool
    {
        return $this->isAdmin() || $this->isPetugas();
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canViewOperationalData(): bool
    {
        return $this->isAdmin();
    }

    public function canManageOperationalData(): bool
    {
        return $this->isAdmin();
    }

    public function canViewBookingData(): bool
    {
        return $this->isAdmin();
    }

    public function canManageBookingData(): bool
    {
        return $this->isAdmin();
    }

    public function canViewOptimizationData(): bool
    {
        return $this->isAdmin();
    }

    public function canManageOptimizationData(): bool
    {
        return $this->isAdmin();
    }

    public function canViewReportData(): bool
    {
        return $this->isAdmin();
    }

    public function canValidateTickets(): bool
    {
        return $this->isAdmin() || $this->isPetugas();
    }

    public function canDeleteImportantData(): bool
    {
        return $this->isAdmin();
    }

    public function penumpang(): HasOne
    {
        return $this->hasOne(
            Penumpang::class,
            'user_id'
        );
    }

    public function pemesananTikets(): HasMany
    {
        return $this->hasMany(
            PemesananTiket::class,
            'created_by'
        );
    }

    public function alokasiDiproses(): HasMany
    {
        return $this->hasMany(
            AlokasiTiket::class,
            'diproses_oleh'
        );
    }

    public function hasilOptimasiDiproses(): HasMany
    {
        return $this->hasMany(
            HasilOptimasi::class,
            'diproses_oleh'
        );
    }

    public function validasiDilakukan(): HasMany
    {
        return $this->hasMany(
            ValidasiTiket::class,
            'divalidasi_oleh'
        );
    }

    public function tiketDivalidasi(): HasMany
    {
        return $this->hasMany(
            PemesananTiket::class,
            'divalidasi_oleh'
        );
    }

    public function pembayaranDiverifikasi(): HasMany
    {
        return $this->hasMany(
            Pembayaran::class,
            'diverifikasi_oleh'
        );
    }
}