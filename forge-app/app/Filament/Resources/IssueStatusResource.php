<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IssueStatusResource\Pages;
use App\Filament\Resources\IssueStatusResource\RelationManagers;
use App\Models\Issues\IssueStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * IssueStatusResource class.
 *
 * This class represents a resource for managing issue statuses.
 */
class IssueStatusResource extends Resource
{
    protected static ?string $model = IssueStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Define the form for the IssueStatusResource.
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
     * Define the table configuration for the IssueStatusResource.
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
     * Retrieve the relations for the IssueStatusResource.
     *
     * @return array The relations for the IssueStatusResource.
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Returns an array of pages for the IssueStatusResource.
     *
     * @return array An array of pages with their corresponding routes.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIssueStatuses::route('/'),
            'create' => Pages\CreateIssueStatus::route('/create'),
            'edit' => Pages\EditIssueStatus::route('/{record}/edit'),
        ];
    }
}
