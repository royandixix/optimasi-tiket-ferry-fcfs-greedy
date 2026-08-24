<?php

namespace App\Filament\Resources\HasilOptimasis\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class HasilOptimasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jadwal dan Metode')
                    ->description('Pilih jadwal keberangkatan dan metode optimasi yang digunakan.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('jadwal_id')
                            ->label('Jadwal Keberangkatan')
                            ->relationship('jadwal', 'tanggal_berangkat')
                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                $tanggal = optional($record->tanggal_berangkat)->format('d-m-Y') ?? '-';
                                $jam = $record->jam_berangkat ? substr((string) $record->jam_berangkat, 0, 5) : '-';
                                $kapal = $record->kapal?->nama_kapal ?? '-';
                                $asal = $record->rute?->pelabuhan_asal ?? '-';
                                $tujuan = $record->rute?->pelabuhan_tujuan ?? '-';

                                return "{$tanggal} {$jam} - {$kapal} ({$asal} ke {$tujuan})";
                            })
                            ->placeholder('Pilih jadwal keberangkatan')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('metode')
                            ->label('Metode Optimasi')
                            ->options([
                                'fcfs' => 'First Come First Served (FCFS)',
                                'greedy' => 'Greedy Heuristik',
                            ])
                            ->placeholder('Pilih metode optimasi')
                            ->native(false)
                            ->required(),
                    ]),

                Section::make('Rekap Pemesanan Tiket')
                    ->description('Data rekap pemesanan tiket berdasarkan hasil proses optimasi.')
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([
                        TextInput::make('total_pemesanan')
                            ->label('Total Pemesanan')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),

                        TextInput::make('total_tiket_diminta')
                            ->label('Total Tiket Diminta')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),

                        TextInput::make('total_tiket_diterima')
                            ->label('Total Tiket Diterima')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),

                        TextInput::make('total_tiket_ditolak')
                            ->label('Total Tiket Ditolak')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),
                    ]),

                Section::make('Evaluasi Pemanfaatan Kapasitas Kapal')
                    ->description('Load factor menunjukkan tingkat pemanfaatan kapasitas kapal.')
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([
                        TextInput::make('kapasitas_kapal')
                            ->label('Kapasitas Kapal')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),

                        TextInput::make('kapasitas_terpakai')
                            ->label('Kapasitas Terpakai')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),

                        TextInput::make('load_factor')
                            ->label('Load Factor')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step('0.01')
                            ->suffix('%')
                            ->default(0.00)
                            ->required(),

                        TextInput::make('waktu_proses_ms')
                            ->label('Waktu Proses')
                            ->numeric()
                            ->minValue(0)
                            ->step('0.0001')
                            ->suffix('ms')
                            ->default(0.0000)
                            ->required(),
                    ]),

                Section::make('Informasi Proses')
                    ->description('User internal yang menjalankan atau mencatat hasil optimasi.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('diproses_oleh')
                            ->label('Diproses Oleh')
                            ->relationship('diprosesOleh', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => Auth::id())
                            ->disabled()
                            ->dehydrated(true),
                    ]),
            ]);
    }
}
