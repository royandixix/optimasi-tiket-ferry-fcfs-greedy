<?php

namespace App\Filament\Resources\PemesananTikets\Tables;

use App\Support\FerryTariff;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PemesananTiketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('waktu_pemesanan', 'desc')
            ->columns([
                TextColumn::make('kode_pemesanan')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('penumpang.nama_penumpang')
                    ->label('Penumpang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jadwal.kapal.nama_kapal')
                    ->label('Kapal')
                    ->searchable(),

                TextColumn::make('tarif_label')
                    ->label('Jenis Tarif')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('jumlah_tiket')
                    ->label('Jumlah')
                    ->formatStateUsing(
                        fn ($state, $record): string =>
                            $state . ' ' . ($record->satuan ?: 'unit')
                    )
                    ->sortable(),

                TextColumn::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->formatStateUsing(
                        fn ($state): string => FerryTariff::rupiah($state)
                    )
                    ->sortable(),

                TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->formatStateUsing(
                        fn ($state): string => FerryTariff::rupiah($state)
                    )
                    ->color('success')
                    ->sortable(),

                TextColumn::make('jadwal.tanggal_berangkat')
                    ->label('Berangkat')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status_pemesanan')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu Konfirmasi',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'diterima' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('waktu_pemesanan')
                    ->label('Waktu Pesan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status_pemesanan')
                    ->label('Filter Status')
                    ->options([
                        'pending' => 'Menunggu Konfirmasi',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                    ]),

                SelectFilter::make('jenis_tarif')
                    ->label('Filter Tarif')
                    ->options(\App\Support\FerryTariff::options()),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Validasi')
                    ->visible(
                        fn ($record): bool =>
                            (auth()->user()?->isAdmin() ?? false)
                            && $record->status_pemesanan === 'pending'
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Data Terpilih')
                        ->visible(
                            fn (): bool =>
                                auth()->user()?->isAdmin() ?? false
                        ),
                ]),
            ])
            ->emptyStateHeading('Belum ada pemesanan tiket')
            ->emptyStateDescription(
                'Pemesanan dibuat oleh penumpang melalui halaman checkout.'
            );
    }
}
