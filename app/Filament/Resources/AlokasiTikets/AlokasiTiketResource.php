<?php

namespace App\Filament\Resources\AlokasiTikets;

use App\Filament\Resources\AlokasiTikets\Pages\ListAlokasiTikets;
use App\Filament\Resources\AlokasiTikets\Tables\AlokasiTiketsTable;
use App\Models\PemesananTiket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class AlokasiTiketResource extends Resource
{
    protected static ?string $model = PemesananTiket::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute =
        'kode_pemesanan';

    protected static ?string $navigationLabel =
        'Hasil Alokasi';

    protected static ?string $modelLabel =
        'Hasil Alokasi';

    protected static ?string $pluralModelLabel =
        'Hasil Alokasi';

    protected static string|UnitEnum|null $navigationGroup =
        'Optimasi Tiket';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'penumpang',
                'jadwal.kapal',
                'jadwal.rute',
            ]);
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->canViewOptimizationData() ?? false;
    }

    public static function canView(Model $record): bool
    {
        return Auth::user()?->canViewOptimizationData() ?? false;
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

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return AlokasiTiketsTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) PemesananTiket::query()->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah pemesanan yang masuk';
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlokasiTikets::route('/'),
        ];
    }
}