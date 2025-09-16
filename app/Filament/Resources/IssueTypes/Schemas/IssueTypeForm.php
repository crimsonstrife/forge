<?php

namespace App\Filament\Resources\IssueTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class IssueTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('icon')
                    ->required(),
                Toggle::make('is_default')
                    ->required(),
                Toggle::make('is_hierarchical')
                    ->required(),
                TextInput::make('key')
                    ->required(),
            ]);
    }
}
