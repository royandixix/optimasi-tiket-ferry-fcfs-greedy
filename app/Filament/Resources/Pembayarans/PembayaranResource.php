<?php

namespace App\Filament\Resources\Pembayarans;

use App\Filament\Resources\Pembayarans\Pages\EditPembayaran;
use App\Filament\Resources\Pembayarans\Pages\ListPembayarans;
use App\Models\Pembayaran;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $recordTitleAttribute = 'kode_pembayaran';

    protected static ?string $navigationLabel = 'Pembayaran';

    protected static ?string $modelLabel = 'Pembayaran';

    protected static ?string $pluralModelLabel = 'Data Pembayaran';

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi Tiket';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewBookingData() ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->canViewBookingData() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->canManageBookingData() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Pembayaran')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('kode_pembayaran')
                        ->label('Kode Pembayaran')
                        ->disabled()
                        ->dehydrated(false),

                    Select::make('pemesanan_tiket_id')
                        ->label('Kode Pemesanan')
                        ->relationship('pemesananTiket', 'kode_pemesanan')
                        ->disabled()
                        ->dehydrated(false),

                    Select::make('metode_pembayaran')
                        ->label('Metode Pembayaran')
                        ->options([
                            'cash' => 'Cash di Tempat',
                            'transfer_bank' => 'Transfer Bank',
                        ])
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('total_bayar')
                        ->label('Total Bayar')
                        ->prefix('Rp')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(false),

                    FileUpload::make('bukti_transfer')
                        ->label('Bukti Transfer')
                        ->disk('public')
                        ->directory('bukti-transfer')
                        ->visibility('public')
                        ->image()
                        ->openable()
                        ->downloadable()
                        ->deletable(false)
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ]),

            Section::make('Verifikasi Admin')
                ->columnSpanFull()
                ->schema([
                    Select::make('status_pembayaran')
                        ->label('Status Pembayaran')
                        ->options([
                            'menunggu_pembayaran' => 'Menunggu Pembayaran',
                            'menunggu_verifikasi' => 'Menunggu Verifikasi',
                            'dibayar' => 'Dibayar',
                            'ditolak' => 'Ditolak',
                        ])
                        ->native(false)
                        ->required(),

                    Textarea::make('catatan_admin')
                        ->label('Catatan Admin')
                        ->rows(3)
                        ->maxLength(1000),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('kode_pembayaran')
                    ->label('Kode Bayar')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pemesananTiket.kode_pemesanan')
                    ->label('Kode Pemesanan')
                    ->searchable(),

                TextColumn::make('pemesananTiket.penumpang.nama_penumpang')
                    ->label('Penumpang')
                    ->searchable(),

                TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Cash di Tempat',
                        'transfer_bank' => 'Transfer Bank',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'warning',
                        'transfer_bank' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('total_bayar')
                    ->label('Total Bayar')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                ImageColumn::make('bukti_transfer')
                    ->label('Bukti Transfer')
                    ->disk('public')
                    ->imageHeight(56),

                TextColumn::make('status_pembayaran')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'menunggu_pembayaran' => 'Menunggu Pembayaran',
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'dibayar' => 'Dibayar',
                        'ditolak' => 'Ditolak',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu_pembayaran' => 'warning',
                        'menunggu_verifikasi' => 'info',
                        'dibayar' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->options([
                        'cash' => 'Cash di Tempat',
                        'transfer_bank' => 'Transfer Bank',
                    ]),

                SelectFilter::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->options([
                        'menunggu_pembayaran' => 'Menunggu Pembayaran',
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'dibayar' => 'Dibayar',
                        'ditolak' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                EditAction::make()->label('Verifikasi'),
            ])
            ->emptyStateHeading('Belum ada pembayaran')
            ->emptyStateDescription('Pembayaran akan muncul setelah user membuat pemesanan.');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::query()
            ->whereIn('status_pembayaran', [
                'menunggu_pembayaran',
                'menunggu_verifikasi',
            ])
            ->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pembayaran yang belum selesai';
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPembayarans::route('/'),
            'edit' => EditPembayaran::route('/{record}/edit'),
        ];
    }
}
