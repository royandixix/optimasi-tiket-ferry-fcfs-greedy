<?php

namespace App\Filament\Resources\JadwalKeberangkatans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JadwalKeberangkatansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_berangkat', 'desc')
            ->columns([
                TextColumn::make('kapal.nama_kapal')
                    ->label('Kapal')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rute.pelabuhan_asal')
                    ->label('Asal')
                    ->searchable(),

                TextColumn::make('rute.pelabuhan_tujuan')
                    ->label('Tujuan')
                    ->searchable(),

                TextColumn::make('tanggal_berangkat')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('jam_berangkat')
                    ->label('Jam')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('kapasitas_total')
                    ->label('Kapasitas')
                    ->numeric(),

                TextColumn::make('kapasitas_terpakai')
                    ->label('Terpakai')
                    ->numeric(),

                TextColumn::make('sisa_kapasitas')
                    ->label('Sisa')
                    ->numeric(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => $state === 'tersedia'
                            ? 'Tersedia'
                            : 'Tidak Tersedia'
                    )
                    ->color(
                        fn (?string $state): string => $state === 'tersedia'
                            ? 'success'
                            : 'danger'
                    ),
            ])
            ->filters([
                SelectFilter::make('kapal_id')
                    ->label('Filter Kapal')
                    ->relationship('kapal', 'nama_kapal'),

                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'tersedia' => 'Tersedia',
                        'penuh' => 'Penuh',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Data Terpilih'),
                ]),
            ])
            ->emptyStateHeading('Belum ada jadwal keberangkatan')
            ->emptyStateDescription(
                'Harga tidak dibuat pada jadwal; harga dipilih saat checkout.'
            );
    }
}