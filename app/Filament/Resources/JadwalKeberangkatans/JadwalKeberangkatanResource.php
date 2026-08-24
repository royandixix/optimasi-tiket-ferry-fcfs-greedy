<?php

namespace App\Filament\Resources\JadwalKeberangkatans;

use App\Filament\Resources\JadwalKeberangkatans\Pages\CreateJadwalKeberangkatan;
use App\Filament\Resources\JadwalKeberangkatans\Pages\EditJadwalKeberangkatan;
use App\Filament\Resources\JadwalKeberangkatans\Pages\ListJadwalKeberangkatans;
use App\Filament\Resources\JadwalKeberangkatans\Schemas\JadwalKeberangkatanForm;
use App\Filament\Resources\JadwalKeberangkatans\Tables\JadwalKeberangkatansTable;
use App\Models\JadwalKeberangkatan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;
use Illuminate\Support\Facades\Auth;
class JadwalKeberangkatanResource extends Resource
{
    protected static ?string $model = JadwalKeberangkatan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $recordTitleAttribute = 'tanggal_berangkat';

    protected static ?string $navigationLabel = 'Jadwal Keberangkatan';

    protected static ?string $modelLabel = 'Jadwal Keberangkatan';

    protected static ?string $pluralModelLabel = 'Data Jadwal Keberangkatan';

    protected static string|UnitEnum|null $navigationGroup = 'Data Operasional';

    protected static ?int $navigationSort = 1;

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
        return JadwalKeberangkatanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JadwalKeberangkatansTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'tersedia')->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah jadwal dengan status tersedia';
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJadwalKeberangkatans::route('/'),
            'create' => CreateJadwalKeberangkatan::route('/create'),
            'edit' => EditJadwalKeberangkatan::route('/{record}/edit'),
        ];
    }
}