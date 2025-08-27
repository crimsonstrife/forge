<?php

namespace App\Filament\Resources\IssueTypes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class IssueTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('icon'),
                IconEntry::make('is_default')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                IconEntry::make('is_hierarchical')
                    ->boolean(),
                TextEntry::make('key'),
            ]);
    }
}
