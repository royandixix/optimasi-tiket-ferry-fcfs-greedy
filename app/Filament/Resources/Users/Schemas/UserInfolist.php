<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail Akun')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->label('Nama Lengkap'),
                    TextEntry::make('email')->label('Email'),
                    TextEntry::make('email_verified_at')->label('Verifikasi Email')->dateTime('d M Y H:i')->placeholder('-'),
                    TextEntry::make('created_at')->label('Tanggal Dibuat')->dateTime('d M Y H:i')->placeholder('-'),
                    TextEntry::make('updated_at')->label('Terakhir Diubah')->dateTime('d M Y H:i')->placeholder('-'),
                ]),
            Section::make('Hak Akses')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextEntry::make('role')
                        ->label('Role')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            User::ROLE_ADMIN => 'Admin',
                            User::ROLE_PETUGAS => 'Petugas Validasi',
                            User::ROLE_USER => 'User',
                            default => ucfirst($state),
                        })
                        ->color(fn (string $state): string => match ($state) {
                            User::ROLE_ADMIN => 'warning',
                            User::ROLE_PETUGAS => 'success',
                            User::ROLE_USER => 'gray',
                            default => 'gray',
                        }),
                    TextEntry::make('status')
                        ->label('Status Akun')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => $state === 'aktif' ? 'Aktif' : 'Nonaktif')
                        ->color(fn (string $state): string => $state === 'aktif' ? 'success' : 'danger'),
                ]),
        ]);
    }
}
