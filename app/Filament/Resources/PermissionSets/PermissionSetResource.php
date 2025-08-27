<?php

namespace App\Filament\Resources\PermissionSets;

use App\Filament\Resources\PermissionSets\Pages\CreatePermissionSet;
use App\Filament\Resources\PermissionSets\Pages\EditPermissionSet;
use App\Filament\Resources\PermissionSets\Pages\ListPermissionSets;
use App\Filament\Resources\PermissionSets\Pages\ViewPermissionSet;
use App\Filament\Resources\PermissionSets\Schemas\PermissionSetForm;
use App\Filament\Resources\PermissionSets\Schemas\PermissionSetInfolist;
use App\Filament\Resources\PermissionSets\Tables\PermissionSetsTable;
use App\Models\PermissionSet;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PermissionSetResource extends Resource
{
    protected static ?string $model = PermissionSet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PermissionSetForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PermissionSetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermissionSetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermissionSets::route('/'),
            'create' => CreatePermissionSet::route('/create'),
            'view' => ViewPermissionSet::route('/{record}'),
            'edit' => EditPermissionSet::route('/{record}/edit'),
        ];
    }
}
