<?php

namespace App\Filament\Resources\ImportExportRecords\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ImportExportRecordInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('external_id')
                    ->placeholder('-'),
                TextEntry::make('project.name')
                    ->label('Project')
                    ->placeholder('-'),
                TextEntry::make('direction'),
                TextEntry::make('status'),
                TextEntry::make('file_path')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
