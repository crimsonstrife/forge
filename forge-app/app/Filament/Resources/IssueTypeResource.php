<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IssueTypeResource\Pages;
use App\Filament\Resources\IssueTypeResource\RelationManagers;
use App\Models\Issues\IssueType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IssueTypeResource extends Resource
{
    protected static ?string $model = IssueType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Generate the form for the IssueTypeResource.
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
     * Define the table for the IssueTypeResource.
     *
     * @param  Table  $table
     * @return Table
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
     * Retrieve the relations for the IssueTypeResource.
     *
     * @return array The relations for the IssueTypeResource.
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Returns an array of pages for the IssueTypeResource.
     *
     * @return array An array of pages with their corresponding routes.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIssueTypes::route('/'),
            'create' => Pages\CreateIssueType::route('/create'),
            'edit' => Pages\EditIssueType::route('/{record}/edit'),
        ];
    }
}
