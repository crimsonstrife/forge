<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use Spatie\Permission\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * PermissionResource class represents a resource for managing permissions.
 *
 * @package Filament\Resources
 */
class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'fas fa-clipboard-check';

    protected static ?int $navigationSort = 2;

    /**
     * Returns the navigation label for the PermissionResource.
     *
     * @return string The navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('Permissions');
    }

    /**
     * Get the plural label for the resource.
     *
     * @return string|null The plural label for the resource, or null if not available.
     */
    public static function getPluralLabel(): ?string
    {
        return static::getNavigationLabel();
    }

    /**
     * Returns the navigation group for the permission resource.
     *
     * @return string|null The navigation group for the permission resource.
     */
    public static function getNavigationGroup(): ?string
    {
        return static::getPluralLabel();
    }

    /**
     * Define the form structure for the PermissionResource.
     *
     * @param Form $form The form instance.
     * @return Form The modified form instance.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Permission name'))
                                    ->unique(table: Permission::class, column: 'name')
                                    ->maxLength(255)
                                    ->required()
                                    ->columnSpan(1),
                            ]),
                    ])
            ]);
    }

    /**
     * Define the table for the PermissionResource.
     *
     * @param  Table  $table  The table instance.
     * @return Table  The modified table instance.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Permission name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /**
     * Get the relations for the PermissionResource.
     *
     * @return array
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Returns an array of pages for the PermissionResource.
     *
     * @return array An array of pages with their corresponding routes.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'view' => Pages\ViewPermission::route('/{record}'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
