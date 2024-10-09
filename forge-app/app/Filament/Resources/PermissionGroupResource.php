<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionGroupResource\Pages;
use App\Filament\Resources\PermissionGroupResource\RelationManagers;
use Spatie\Permission\Models\Permission;
use App\Models\Auth\PermissionGroup;
use App\Models\Auth\PermissionSet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PermissionGroupResource extends Resource
{
    protected static ?string $model = PermissionGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(255),
                Select::make('permission_sets')
                    ->multiple()
                    ->relationship('permissionSets', 'name')
                    ->options(PermissionSet::all()->pluck('name', 'id'))
                    ->preload(),
                Select::make('permissions')
                    ->multiple()
                    ->relationship('permissions', 'name')
                    ->options(Permission::all()->pluck('name', 'id'))
                    ->preload(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('permission_sets_count')->counts('permissionSets')->label('Permission Sets')->badge(),
                TextColumn::make('permissions_count')->counts('permissions')->label('Permissions')->badge(),
            ])
            ->filters([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissionGroups::route('/'),
            'create' => Pages\CreatePermissionGroup::route('/create'),
            'edit' => Pages\EditPermissionGroup::route('/{record}/edit'),
        ];
    }
}
