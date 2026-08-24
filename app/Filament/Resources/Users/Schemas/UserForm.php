<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Akun')
                ->description('Data akun digunakan untuk login sesuai role pengguna.')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->maxLength(150)
                        ->required(),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(150)
                        ->unique(ignoreRecord: true)
                        ->required(),
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->helperText('Kosongkan saat mengubah data bila password tidak diganti.')
                        ->minLength(8)
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(fn (?string $state): bool => filled($state)),
                    DateTimePicker::make('email_verified_at')
                        ->label('Waktu Verifikasi Email')
                        ->native(false)
                        ->seconds(false)
                        ->placeholder('Opsional'),
                ]),

            Section::make('Role dan Status Akses')
                ->description('Sistem hanya menggunakan role User, Admin, dan Petugas Validasi.')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Select::make('role')
                        ->label('Role User')
                        ->options([
                            User::ROLE_USER => 'User',
                            User::ROLE_ADMIN => 'Admin',
                            User::ROLE_PETUGAS => 'Petugas Validasi',
                        ])
                        ->native(false)
                        ->default(User::ROLE_USER)
                        ->helperText('Petugas hanya dapat membuka menu Validasi Tiket.')
                        ->required(),
                    Select::make('status')
                        ->label('Status Akun')
                        ->options([
                            'aktif' => 'Aktif',
                            'nonaktif' => 'Nonaktif',
                        ])
                        ->native(false)
                        ->default('aktif')
                        ->required(),
                ]),
        ]);
    }
}
