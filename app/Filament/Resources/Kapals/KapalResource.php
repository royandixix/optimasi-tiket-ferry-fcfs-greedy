<?php

namespace App\Filament\Resources\Kapals;

use App\Filament\Resources\Kapals\Pages\CreateKapal;
use App\Filament\Resources\Kapals\Pages\EditKapal;
use App\Filament\Resources\Kapals\Pages\ListKapals;
use App\Filament\Resources\Kapals\Schemas\KapalForm;
use App\Filament\Resources\Kapals\Tables\KapalsTable;
use App\Models\Kapal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class KapalResource extends Resource
{
    protected static ?string $model = Kapal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static ?string $recordTitleAttribute = 'nama_kapal';

    protected static ?string $navigationLabel = 'Kapal';

    protected static ?string $modelLabel = 'Kapal';

    protected static ?string $pluralModelLabel = 'Data Kapal';

    protected static string|UnitEnum|null $navigationGroup = 'Data Operasional';

    protected static ?int $navigationSort = 0;

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
        return KapalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KapalsTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'aktif')->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah kapal aktif';
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKapals::route('/'),
            'create' => CreateKapal::route('/create'),
            'edit' => EditKapal::route('/{record}/edit'),
        ];
    }
}
