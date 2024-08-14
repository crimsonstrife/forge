<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IssuePriorityResource\Pages;
use App\Filament\Resources\IssuePriorityResource\RelationManagers;
use App\Models\IssuePriority;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * Represents a resource for Issue Priority.
 *
 * This class extends the Resource class and is used to handle operations related to Issue Priority resources.
 *
 * @package Filament\Resources
 */
class IssuePriorityResource extends Resource
{
    protected static ?string $model = IssuePriority::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Generate a form for the IssuePriorityResource.
     *
     * @param  Form  $form The form instance.
     * @return Form       The updated form instance.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    /**
     * Define the table configuration for the IssuePriorityResource.
     *
     * @param  Table  $table  The table instance.
     * @return Table  The modified table instance.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Retrieve the relations for the IssuePriorityResource.
     *
     * @return array The relations for the IssuePriorityResource.
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Returns an array of pages for managing issue priorities.
     *
     * @return array An array of pages with their corresponding routes.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIssuePriorities::route('/'),
            'create' => Pages\CreateIssuePriority::route('/create'),
            'edit' => Pages\EditIssuePriority::route('/{record}/edit'),
        ];
    }
}
