<?php

namespace App\Filament\Widgets;

use App\Models\JadwalKeberangkatan;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class JadwalTerbaruWidget extends TableWidget
{
    protected static ?string $heading = 'Jadwal Keberangkatan Terbaru';

    protected static ?string $description = 'Daftar jadwal kapal ferry terbaru beserta kapasitas dan status keberangkatan.';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'sm' => 'full',
        'md' => 'full',
        'lg' => 'full',
        'xl' => 'full',
        '2xl' => 'full',
    ];

    public static function canView(): bool
    {
        return auth()->user()?->canViewReportData() ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                JadwalKeberangkatan::query()
                    ->with(['kapal', 'rute'])
                    ->latest()
                    ->limit(8)
            )
            ->paginated(false)
            ->striped()
            ->columns([
                TextColumn::make('kapal.nama_kapal')
                    ->label('Kapal')
                    ->searchable()
                    ->sortable()
                    ->description(function (JadwalKeberangkatan $record): string {
                        $asal = $record->rute?->pelabuhan_asal ?? '-';
                        $tujuan = $record->rute?->pelabuhan_tujuan ?? '-';

                        return "{$asal} → {$tujuan}";
                    })
                    ->weight('bold'),

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
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('kapasitas_terpakai')
                    ->label('Terpakai')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('sisa_kapasitas')
                    ->label('Sisa')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'tersedia' => 'Tersedia',
                        'penuh' => 'Penuh',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'tersedia' => 'success',
                        'penuh' => 'warning',
                        'selesai' => 'gray',
                        'batal' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->emptyStateHeading('Belum ada jadwal keberangkatan')
            ->emptyStateDescription('Data jadwal kapal ferry akan tampil setelah admin menambahkan jadwal keberangkatan.');
    }
}
