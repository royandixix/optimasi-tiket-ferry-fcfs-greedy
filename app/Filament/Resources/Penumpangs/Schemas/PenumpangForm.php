<?php

namespace App\Filament\Resources\Penumpangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PenumpangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun Penumpang')
                    ->description('Hubungkan data penumpang dengan akun user jika penumpang melakukan registrasi melalui halaman Blade.')
                    ->columnSpanFull()
                    ->columns(1)
                    ->schema([
                        Select::make('user_id')
                            ->label('Akun User Penumpang')
                            ->relationship(
                                'user',
                                'name',
                                modifyQueryUsing: fn ($query) => $query->where('role', 'user')
                            )
                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                return "{$record->name} - {$record->email}";
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder('Opsional, pilih jika penumpang memiliki akun login')
                            ->helperText('Kosongkan jika data penumpang diinput manual oleh admin.'),
                    ]),

                Section::make('Identitas Penumpang')
                    ->description('Data identitas digunakan untuk kebutuhan pemesanan tiket kapal ferry.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('nik')
                            ->label('NIK')
                            ->placeholder('Masukkan NIK penumpang')
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->helperText('NIK bersifat opsional, tetapi jika diisi tidak boleh sama dengan penumpang lain.'),

                        TextInput::make('nama_penumpang')
                            ->label('Nama Penumpang')
                            ->placeholder('Masukkan nama lengkap penumpang')
                            ->maxLength(150)
                            ->required(),

                        Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->native(false)
                            ->placeholder('Pilih jenis kelamin'),

                        TextInput::make('no_hp')
                            ->label('Nomor HP')
                            ->placeholder('Contoh: 081234567890')
                            ->tel()
                            ->maxLength(20),
                    ]),

                Section::make('Alamat Penumpang')
                    ->description('Alamat digunakan sebagai informasi tambahan apabila diperlukan.')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->placeholder('Masukkan alamat penumpang')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}