<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Filament\Resources\ActivityResource\RelationManagers;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * Class ActivityResource
 *
 * This class represents the activity resource.
 */
class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'fas fa-clipboard-list';

    protected static ?int $priority = 1;

    /**
     * Retrieve the navigation label for the ActivityResource.
     *
     * @return string The navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('Activities');
    }

    /**
     * Get the plural label for the activity resource.
     *
     * @return string|null The plural label for the activity resource, or null if not available.
     */
    public static function getPluralLabel(): ?string
    {
        return static::getNavigationLabel();
    }

    /**
     * Get the navigation group for the activity resource.
     *
     * @return string|null The navigation group.
     */
    public static function getNavigationGroup(): ?string
    {
        return __('Referential');
    }

    /**
     * Generates a form for the ActivityResource.
     *
     * @param Form $form The form instance.
     * @return Form The updated form instance.
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
                                    ->label(__('Activity name'))
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\RichEditor::make('description')
                                    ->label(__('Description'))
                                    ->required()
                                    ->columnSpan(2),

                            ])
                    ])
            ]);
    }

    /**
     * Define the table for the ActivityResource.
     *
     * @param Table $table The table instance.
     * @return Table The modified table instance.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Activity name'))
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
            ])
            ->defaultSort('id');
    }

    /**
     * Get the relations for the ActivityResource.
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
     * Returns an array of pages for the ActivityResource.
     *
     * @return array An array of pages with their corresponding routes.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'view' => Pages\ViewActivity::route('/{record}'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
