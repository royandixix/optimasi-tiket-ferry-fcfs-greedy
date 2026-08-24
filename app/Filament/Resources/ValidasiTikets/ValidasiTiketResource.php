<?php

namespace App\Filament\Resources\ValidasiTikets;

use App\Filament\Resources\ValidasiTikets\Pages\ManageValidasiTikets;
use App\Models\ValidasiTiket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ValidasiTiketResource extends Resource
{
    protected static ?string $model = ValidasiTiket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static ?string $recordTitleAttribute = 'nilai_diperiksa';

    protected static ?string $navigationLabel = 'Validasi Tiket';

    protected static ?string $modelLabel = 'Validasi Tiket';

    protected static ?string $pluralModelLabel = 'Riwayat Validasi Tiket';

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi Tiket';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('waktu_validasi', 'desc')
            ->columns([
                TextColumn::make('waktu_validasi')
                    ->label('Waktu Validasi')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),

                TextColumn::make('pemesananTiket.kode_pemesanan')
                    ->label('Kode Booking')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('pemesananTiket.penumpang.nama_penumpang')
                    ->label('Nama Penumpang')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('pemesananTiket.jumlah_tiket')
                    ->label('Jumlah Tiket')
                    ->numeric()
                    ->placeholder('-'),

                TextColumn::make('pemesananTiket.total_harga')
                    ->label('Total Harga')
                    ->money('IDR', locale: 'id', decimalPlaces: 0)
                    ->placeholder('-'),

                TextColumn::make('metode_validasi')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'qr_code' => 'QR Code',
                        'kode_booking' => 'Kode Booking',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'qr_code' => 'info',
                        'kode_booking' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('status_validasi')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'berhasil' => 'Berhasil',
                        'gagal' => 'Gagal',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'berhasil' => 'success',
                        'gagal' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('alasan_gagal')
                    ->label('Keterangan')
                    ->formatStateUsing(
                        fn (?string $state): string => filled($state)
                            ? $state
                            : 'Tiket berhasil divalidasi.'
                    )
                    ->wrap()
                    ->limit(100),

                TextColumn::make('divalidasiOleh.name')
                    ->label('Petugas')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('nilai_diperiksa')
                    ->label('Nilai Dipindai')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dicatat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_validasi')
                    ->label('Filter Status')
                    ->options([
                        'berhasil' => 'Berhasil',
                        'gagal' => 'Gagal',
                    ]),

                SelectFilter::make('metode_validasi')
                    ->label('Filter Metode')
                    ->options([
                        'qr_code' => 'QR Code',
                        'kode_booking' => 'Kode Booking',
                    ]),
            ])
            ->recordActions([])
            ->toolbarActions([])
            ->emptyStateHeading('Belum ada riwayat validasi tiket')
            ->emptyStateDescription(
                'Tekan tombol Validasi Tiket untuk memindai QR Code atau memasukkan kode booking penumpang.'
            )
            ->emptyStateIcon(Heroicon::OutlinedQrCode);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with([
            'pemesananTiket.penumpang',
            'divalidasiOleh',
        ]);

        $user = auth()->user();

        if ($user?->isPetugas()) {
            $query->where('divalidasi_oleh', $user->id);
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canValidateTickets() ?? false;
    }

    public static function canView(Model $record): bool
    {
        $user = auth()->user();

        if (! $user?->canValidateTickets()) {
            return false;
        }

        return $user->isAdmin()
            || (int) $record->divalidasi_oleh === (int) $user->id;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        if (! static::canViewAny()) {
            return null;
        }

        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah riwayat validasi tiket';
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageValidasiTikets::route('/'),
        ];
    }
}
