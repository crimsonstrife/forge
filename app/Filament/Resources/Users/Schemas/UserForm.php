<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

final class UserForm
{
    /**
     * @throws \Exception
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('User')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->rule(fn ($record) => Rule::unique('users', 'email')->ignore($record))
                            ->maxLength(255),
                    ]),
                ]),

            Section::make('Security')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (?object $record) => $record === null)
                            ->rule(fn (?object $record) => $record ? 'nullable|confirmed|min:8' : 'required|confirmed|min:8')
                            ->mutateDehydratedStateUsing(fn (string $state) => Hash::make($state)),

                        TextInput::make('password_confirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->revealable()
                            ->required(fn (?object $record) => $record === null),
                    ]),

                    DateTimePicker::make('email_verified_at')
                        ->label('Email verified at')
                        ->native(false)
                        ->closeOnDateSelection()
                        ->helperText('Set a timestamp to mark this email as verified.'),
                ])->collapsible(),
        ]);
    }
}
