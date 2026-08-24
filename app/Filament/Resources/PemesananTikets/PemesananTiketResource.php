<?php

namespace App\Filament\Resources\PemesananTikets;

use App\Filament\Resources\PemesananTikets\Pages\CreatePemesananTiket;
use App\Filament\Resources\PemesananTikets\Pages\EditPemesananTiket;
use App\Filament\Resources\PemesananTikets\Pages\ListPemesananTikets;
use App\Filament\Resources\PemesananTikets\Schemas\PemesananTiketForm;
use App\Filament\Resources\PemesananTikets\Tables\PemesananTiketsTable;
use App\Models\PemesananTiket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;
use Illuminate\Support\Facades\Auth;
class PemesananTiketResource extends Resource
{
    protected static ?string $model = PemesananTiket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $recordTitleAttribute = 'kode_pemesanan';

    protected static ?string $navigationLabel = 'Pemesanan Tiket';

    protected static ?string $modelLabel = 'Pemesanan Tiket';

    protected static ?string $pluralModelLabel = 'Data Pemesanan Tiket';

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi Tiket';

    protected static ?int $navigationSort = 1;

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
        // Pemesanan dibuat oleh penumpang melalui checkout user.
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->canManageBookingData() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->canDeleteImportantData() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->canDeleteImportantData() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return PemesananTiketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PemesananTiketsTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status_pemesanan', 'pending')->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah pemesanan tiket dengan status pending';
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPemesananTikets::route('/'),
            'create' => CreatePemesananTiket::route('/create'),
            'edit' => EditPemesananTiket::route('/{record}/edit'),
        ];
    }
}