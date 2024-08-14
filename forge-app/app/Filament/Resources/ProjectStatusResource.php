<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectStatusResource\Pages;
use App\Filament\Resources\ProjectStatusResource\RelationManagers;
use App\Models\ProjectStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * Class ProjectStatusResource
 *
 * This class represents a resource for managing project statuses.
 */
class ProjectStatusResource extends Resource
{
    protected static ?string $model = ProjectStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Generate the form for the ProjectStatusResource.
     *
     * @param Form $form The form instance.
     * @return Form The updated form instance.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    /**
     * Define the table configuration for the ProjectStatusResource.
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
     * Get the relations for the ProjectStatusResource.
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
     * Returns an array of pages for the ProjectStatusResource.
     *
     * @return array An array of pages with their corresponding routes.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectStatuses::route('/'),
            'create' => Pages\CreateProjectStatus::route('/create'),
            'edit' => Pages\EditProjectStatus::route('/{record}/edit'),
        ];
    }
}
