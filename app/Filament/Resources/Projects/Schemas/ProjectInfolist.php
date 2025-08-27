<?php

namespace App\Filament\Resources\Projects\Schemas;

use Exception;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProjectInfolist
{
    /**
     * @throws Exception
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('organization.name'),
                TextEntry::make('name'),
                TextEntry::make('key'),
                TextEntry::make('stage'),
                TextEntry::make('lead_id'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('started_at')
                    ->dateTime(),
                TextEntry::make('due_at')
                    ->dateTime(),
                TextEntry::make('ended_at')
                    ->dateTime(),
                TextEntry::make('archived_at')
                    ->dateTime(),
            ]);
    }
}
