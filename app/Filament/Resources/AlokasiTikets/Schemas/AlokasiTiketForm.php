<?php

namespace App\Filament\Resources\AlokasiTikets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class AlokasiTiketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pemesanan_tiket_id')
                    ->label('Kode Pemesanan')
                    ->relationship('pemesananTiket', 'kode_pemesanan')
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        $namaPenumpang = $record->penumpang?->nama_penumpang ?? '-';
                        $jumlahTiket = $record->jumlah_tiket ?? 0;

                        return "{$record->kode_pemesanan} - {$namaPenumpang} ({$jumlahTiket} tiket)";
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('jadwal_id')
                    ->label('Jadwal Keberangkatan')
                    ->relationship('jadwal', 'tanggal_berangkat')
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        $tanggal = optional($record->tanggal_berangkat)->format('d-m-Y') ?? '-';
                        $jam = $record->jam_berangkat ?? '-';
                        $kapal = $record->kapal?->nama_kapal ?? '-';
                        $asal = $record->rute?->pelabuhan_asal ?? '-';
                        $tujuan = $record->rute?->pelabuhan_tujuan ?? '-';

                        return "{$tanggal} {$jam} - {$kapal} ({$asal} ke {$tujuan})";
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('metode')
                    ->label('Metode Alokasi')
                    ->options([
                        'fcfs' => 'First Come First Served (FCFS)',
                        'greedy' => 'Greedy Heuristik',
                    ])
                    ->native(false)
                    ->required(),

                TextInput::make('jumlah_dialokasikan')
                    ->label('Jumlah Tiket Dialokasikan')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                TextInput::make('nilai_prioritas')
                    ->label('Nilai Prioritas')
                    ->helperText('Nilai prioritas digunakan untuk mendukung proses pemilihan data pada metode Greedy Heuristik.')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                TextInput::make('sisa_kapasitas_sebelum')
                    ->label('Sisa Kapasitas Sebelum Alokasi')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                TextInput::make('sisa_kapasitas_sesudah')
                    ->label('Sisa Kapasitas Sesudah Alokasi')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                Select::make('status_alokasi')
                    ->label('Status Alokasi')
                    ->options([
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                    ])
                    ->native(false)
                    ->required(),

                Select::make('diproses_oleh')
                    ->label('Diproses Oleh')
                    ->relationship('diprosesOleh', 'name')
                    ->searchable()
                    ->preload()
                    ->default(fn () => Auth::id())
                    ->disabled()
                    ->dehydrated(true),
            ]);
    }
}