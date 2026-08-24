<?php

namespace App\Filament\Resources\HasilOptimasis\Tables;

use App\Models\HasilOptimasi;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;

class HasilOptimasisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(
                    'pemesananTiket.penumpang.nama_penumpang'
                )
                    ->label('Penumpang')
                    ->searchable(),

                TextColumn::make(
                    'jadwal.kapal.nama_kapal'
                )
                    ->label('Kapal')
                    ->searchable(),

                TextColumn::make('tanggal_pemesanan')
                    ->label('Tanggal')
                    ->state(
                        fn ($record) =>
                            $record->pemesananTiket?->waktu_pemesanan
                    )
                    ->date('d-m-Y'),

                TextColumn::make('jam_pemesanan')
                    ->label('Jam')
                    ->state(
                        fn ($record) =>
                            $record->pemesananTiket?->waktu_pemesanan
                    )
                    ->dateTime('H:i:s'),

                TextColumn::make('nilai_prioritas')
                    ->label('Nilai Prioritas')
                    ->numeric(),

                TextColumn::make('sisa_kapasitas_sebelum')
                    ->label('Sisa Kapasitas Sebelum')
                    ->numeric(),

                TextColumn::make('status_alokasi')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string =>
                            match ($state) {
                                'diterima' => 'Diterima',
                                'ditolak' => 'Ditolak',
                                default => ucfirst($state),
                            }
                    )
                    ->color(
                        fn (string $state): string =>
                            match ($state) {
                                'diterima' => 'success',
                                'ditolak' => 'danger',
                                default => 'gray',
                            }
                    ),

                TextColumn::make('sisa_kapasitas_sesudah')
                    ->label('Sisa Kapasitas Sesudah')
                    ->numeric()
                    ->summarize(
                        Summarizer::make()
                            ->hiddenLabel()
                            ->using(
                                function (Builder $query): string {
                                    $alokasi = (clone $query)
                                        ->select([
                                            'jadwal_id',
                                            'metode',
                                        ])
                                        ->first();

                                    if (! $alokasi) {
                                        return '';
                                    }

                                    $hasil = HasilOptimasi::query()
                                        ->where(
                                            'jadwal_id',
                                            $alokasi->jadwal_id
                                        )
                                        ->where(
                                            'metode',
                                            $alokasi->metode
                                        )
                                        ->first();

                                    if (! $hasil) {
                                        return '';
                                    }

                                    $metode =
                                        $alokasi->metode === 'greedy'
                                            ? 'Greedy'
                                            : 'FCFS';

                                    $loadFactor = number_format(
                                        (float) $hasil->load_factor,
                                        2,
                                        ',',
                                        '.'
                                    );

                                    return
                                        "Load Factor {$metode} = {$loadFactor}%";
                                }
                            ),
                    ),
            ])
            ->paginated(false)
            ->emptyStateHeading(
                'Belum ada hasil optimasi'
            )
            ->emptyStateDescription(
                'Klik Proses Greedy atau Proses FCFS untuk menampilkan hasil.'
            );
    }
}