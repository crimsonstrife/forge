<?php

namespace App\Filament\Resources\IssuePriorities\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class IssuePriorityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('key'),
                TextEntry::make('order')
                    ->numeric(),
                TextEntry::make('weight')
                    ->numeric(),
                TextEntry::make('color'),
                TextEntry::make('icon'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
