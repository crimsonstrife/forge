<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Pages\CreateRecord;

/**
 * UserResource class represents a resource for managing users.
 *
 * @package Filament\Resources
 */
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'fas fa-users';

    protected static ?int $navigationSort = 1;

    /**
     * Returns the navigation label for the UserResource.
     *
     * @return string The navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('Users');
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
     * Get the navigation group for the user resource.
     *
     * @return string|null The navigation group for the user resource.
     */
    public static function getNavigationGroup(): ?string
    {
        return __('Permissions');
    }

    /**
     * Define the form structure for the User resource.
     *
     * @param  Form  $form  The form instance.
     * @return Form  The updated form instance.
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
                                    ->label(__('Full name'))
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label(__('Email address'))
                                    ->email()
                                    ->required()
                                    ->rule(
                                        fn($record) => 'unique:users,email,'
                                            . ($record ? $record->id : 'NULL')
                                            . ',id,deleted_at,NULL'
                                    )
                                    ->maxLength(255),

                                Forms\Components\CheckboxList::make('roles')
                                    ->label(__('Permission roles'))
                                    ->required()
                                    ->columns(3)
                                    ->relationship('roles', 'name'),
                            ]),
                    ])
            ]);
    }

    /**
     * Define the table structure and configuration for the User resource.
     *
     * @param Table $table The table instance.
     * @return Table The configured table instance.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Full name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email address'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('Roles'))
                    ->limit(2),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label(__('Email verified at'))
                    ->dateTime()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('socials')
                    ->label(__('Linked social networks'))
                    ->view('partials.filament.resources.social-icon'),

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
     * Retrieve the relations for the UserResource.
     *
     * @return array The relations for the UserResource.
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Retrieves an array of pages for the UserResource.
     *
     * @return array An array of pages with their corresponding routes.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
