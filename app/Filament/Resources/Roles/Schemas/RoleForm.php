<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('team_id'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('guard_name')
                    ->required(),
            ]);
    }
}
