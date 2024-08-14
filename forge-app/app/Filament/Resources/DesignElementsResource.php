<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DesignElementsResource\Pages;
use App\Filament\Resources\DesignElementsResource\RelationManagers;
use App\Models\DesignElements;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * Class DesignElementsResource
 *
 * This class represents a resource for managing design elements.
 */
class DesignElementsResource extends Resource
{
    protected static ?string $model = DesignElements::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Generate the form for the DesignElementsResource.
     *
     * @param  Form  $form The form instance.
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
     * Define the table configuration for the DesignElementsResource.
     *
     * @param Table $table The table instance.
     * @return Table The configured table instance.
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
     * Get the relations for the DesignElementsResource.
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
     * Retrieves an array of pages for the DesignElementsResource.
     *
     * @return array An array of pages with their corresponding routes.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDesignElements::route('/'),
            'create' => Pages\CreateDesignElements::route('/create'),
            'edit' => Pages\EditDesignElements::route('/{record}/edit'),
        ];
    }
}
