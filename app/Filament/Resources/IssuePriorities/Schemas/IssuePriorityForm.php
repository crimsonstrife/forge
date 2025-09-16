<?php

namespace App\Filament\Resources\IssuePriorities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IssuePriorityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('key')
                    ->required(),
                TextInput::make('order')
                    ->required()
                    ->numeric(),
                TextInput::make('weight')
                    ->required()
                    ->numeric(),
                TextInput::make('color')
                    ->required(),
                TextInput::make('icon')
                    ->required(),
            ]);
    }
}
