<?php

namespace App\Filament\Resources\Rutes;

use App\Filament\Resources\Rutes\Pages\CreateRute;
use App\Filament\Resources\Rutes\Pages\EditRute;
use App\Filament\Resources\Rutes\Pages\ListRutes;
use App\Filament\Resources\Rutes\Schemas\RuteForm;
use App\Filament\Resources\Rutes\Tables\RutesTable;
use App\Models\Rute;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;
use Illuminate\Support\Facades\Auth;
class RuteResource extends Resource
{
    protected static ?string $model = Rute::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static ?string $recordTitleAttribute = 'pelabuhan_asal';

    protected static ?string $navigationLabel = 'Rute';

    protected static ?string $modelLabel = 'Rute';

    protected static ?string $pluralModelLabel = 'Data Rute Penyeberangan';

    protected static string|UnitEnum|null $navigationGroup = 'Data Operasional';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewOperationalData() ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->canViewOperationalData() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->canManageOperationalData() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->canManageOperationalData() ?? false;
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
        return RuteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RutesTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'aktif')->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah rute aktif';
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRutes::route('/'),
            'create' => CreateRute::route('/create'),
            'edit' => EditRute::route('/{record}/edit'),
        ];
    }
}