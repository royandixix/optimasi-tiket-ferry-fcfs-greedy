<?php

namespace App\Filament\Resources\Penumpangs\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PenumpangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')

            ->columns([
                TextColumn::make('nama_penumpang')
                    ->label('Nama Penumpang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'L' => 'Laki-laki',
                            'P' => 'Perempuan',
                            default => '-',
                        }
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'L' => 'info',
                            'P' => 'warning',
                            default => 'gray',
                        }
                    ),

                TextColumn::make('no_hp')
                    ->label('Nomor HP')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('user.name')
                    ->label('Akun User')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Input manual'),

                TextColumn::make('user.email')
                    ->label('Email Akun')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('pemesananTikets_count')
                    ->label('Total Pemesanan')
                    ->counts('pemesananTikets')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                SelectFilter::make('jenis_kelamin')
                    ->label('Filter Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),

                SelectFilter::make('user_id')
                    ->label('Filter Sumber Data')
                    ->options([
                        'memiliki_akun' => 'Memiliki Akun',
                        'input_manual' => 'Input Manual',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'memiliki_akun' =>
                                $query->whereNotNull('user_id'),

                            'input_manual' =>
                                $query->whereNull('user_id'),

                            default => $query,
                        };
                    }),
            ])

            ->recordActions([
                /*
                 * Ini bukan EditAction.
                 * Tombol hanya menampilkan notifikasi dan tidak membuka form.
                 */
                Action::make('ubah_ditolak')
                    ->label('Ubah')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->color('gray')
                    ->tooltip('Data penumpang tidak dapat diubah')
                    ->action(function (): void {
                        Notification::make()
                            ->warning()
                            ->title('Data penumpang tidak dapat diubah')
                            ->body(
                                'Data identitas penumpang telah dikunci dan tidak dapat diubah melalui panel admin.'
                            )
                            ->persistent()
                            ->send();
                    }),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Data Terpilih'),
                ]),
            ])

            ->emptyStateHeading('Belum ada data penumpang')
            ->emptyStateDescription(
                'Tambahkan data penumpang untuk digunakan pada proses pemesanan tiket kapal ferry.'
            );
    }
}