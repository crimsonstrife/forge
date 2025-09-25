<?php

namespace App\Filament\Resources\ImportExportRecords\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ImportExportRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('external_id'),
                Select::make('project_id')
                    ->relationship('project', 'name'),
                TextInput::make('direction')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('queued'),
                TextInput::make('file_path'),
                TextInput::make('options'),
                TextInput::make('report'),
            ]);
    }
}
