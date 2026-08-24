<?php

namespace App\Filament\Resources\Penumpangs;

use App\Filament\Resources\Penumpangs\Pages\CreatePenumpang;
use App\Filament\Resources\Penumpangs\Pages\ListPenumpangs;
use App\Filament\Resources\Penumpangs\Schemas\PenumpangForm;
use App\Filament\Resources\Penumpangs\Tables\PenumpangsTable;
use App\Models\Penumpang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PenumpangResource extends Resource
{
    protected static ?string $model = Penumpang::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'nama_penumpang';

    protected static ?string $navigationLabel = 'Penumpang';

    protected static ?string $modelLabel = 'Penumpang';

    protected static ?string $pluralModelLabel = 'Data Penumpang';

    protected static string|UnitEnum|null $navigationGroup =
        'Transaksi Tiket';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canManageBookingData() ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->canManageBookingData() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->canManageBookingData() ?? false;
    }

    /**
     * Semua akun pada panel admin dilarang mengubah data penumpang.
     */
    public static function canEdit(Model $record): bool
    {
        return false;
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
        return PenumpangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenumpangsTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah data penumpang';
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPenumpangs::route('/'),
            'create' => CreatePenumpang::route('/create'),

            // Route edit sengaja dihapus agar tidak dapat dibuka
            // langsung melalui URL.
        ];
    }
}