<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Auth\Role;
use App\Models\Auth\PermissionSet;
use App\Models\Auth\PermissionGroup;
use Spatie\Permission\Models\Permission;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * RoleResource class.
 *
 * This class represents a resource for managing roles.
 */
class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'fas fa-user-tag';

    /**
     * Define the form for the RoleResource.
     *
     * @param  Form  $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('permissions')
                    ->multiple()
                    ->relationship('permissions', 'name')
                    ->options(Permission::all()->pluck('name', 'id'))
                    ->preload(),
                Select::make('permissionSets')
                    ->multiple()
                    ->relationship('permissionSets', 'name')
                    ->options(PermissionSet::all()->pluck('name', 'id'))
                    ->preload(),
                Select::make('permissionGroups')
                    ->multiple()
                    ->relationship('permissionGroups', 'name')
                    ->options(PermissionGroup::all()->pluck('name', 'id'))
                    ->preload(),
            ]);
    }

    /**
     * Define the table configuration for the Role resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                SelectColumn::make('permissions')
                    ->relationship('permissions', 'name')
                    ->multiple(),
                SelectColumn::make('permissionSets')
                    ->relationship('permissionSets', 'name')
                    ->multiple(),
                SelectColumn::make('permissionGroups')
                    ->relationship('permissionGroups', 'name')
                    ->multiple(),
            ])
            ->filters([
                //
            ]);
    }

    /**
     * Retrieve the relations for the RoleResource.
     *
     * @return array The relations for the RoleResource.
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Retrieve an array of pages for the RoleResource.
     *
     * @return array An array of pages with their corresponding routes.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
