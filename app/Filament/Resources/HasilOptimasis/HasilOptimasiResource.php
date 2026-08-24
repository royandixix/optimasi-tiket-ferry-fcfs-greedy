<?php

namespace App\Filament\Resources\HasilOptimasis;

use App\Filament\Resources\HasilOptimasis\Pages\ListHasilOptimasis;
use App\Filament\Resources\HasilOptimasis\Tables\HasilOptimasisTable;
use App\Models\AlokasiTiket;
use App\Models\HasilOptimasi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class HasilOptimasiResource extends Resource
{
    protected static ?string $model = AlokasiTiket::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedChartBar;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel =
        'Proses Optimasi';

    protected static ?string $modelLabel =
        'Proses Optimasi';

    protected static ?string $pluralModelLabel =
        'Proses Optimasi';

    protected static string|UnitEnum|null $navigationGroup =
        'Optimasi Tiket';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewOptimizationData() ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->canViewOptimizationData() ?? false;
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
        return HasilOptimasisTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) HasilOptimasi::query()->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah hasil optimasi';
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHasilOptimasis::route('/'),
        ];
    }
}