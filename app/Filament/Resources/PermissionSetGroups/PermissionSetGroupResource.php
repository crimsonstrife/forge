<?php

namespace App\Filament\Resources\PermissionSetGroups;

use App\Filament\Resources\PermissionSetGroups\Pages\CreatePermissionSetGroup;
use App\Filament\Resources\PermissionSetGroups\Pages\EditPermissionSetGroup;
use App\Filament\Resources\PermissionSetGroups\Pages\ListPermissionSetGroups;
use App\Filament\Resources\PermissionSetGroups\Pages\ViewPermissionSetGroup;
use App\Filament\Resources\PermissionSetGroups\Schemas\PermissionSetGroupForm;
use App\Filament\Resources\PermissionSetGroups\Schemas\PermissionSetGroupInfolist;
use App\Filament\Resources\PermissionSetGroups\Tables\PermissionSetGroupsTable;
use App\Models\PermissionSetGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PermissionSetGroupResource extends Resource
{
    protected static ?string $model = PermissionSetGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PermissionSetGroupForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PermissionSetGroupInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermissionSetGroupsTable::configure($table);
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
            'index' => ListPermissionSetGroups::route('/'),
            'create' => CreatePermissionSetGroup::route('/create'),
            'view' => ViewPermissionSetGroup::route('/{record}'),
            'edit' => EditPermissionSetGroup::route('/{record}/edit'),
        ];
    }
}
