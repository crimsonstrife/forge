<?php

namespace App\Filament\Resources\Users\Schemas;

use Exception;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;

class UserInfolist
{
    /**
     * @throws Exception
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile')->schema([
                    TextEntry::make('name')->label('Name'),
                    TextEntry::make('email')->label('Email'),
                    IconEntry::make('email_verified_at')
                        ->label('Verified')
                        ->boolean()
                        ->trueIcon('heroicon-m-check-badge')
                        ->falseIcon('heroicon-m-x-mark')
                        ->state(fn ($record) => (bool) $record->email_verified_at),
                    TextEntry::make('created_at')->date(),
                    TextEntry::make('updated_at')->date(),
                ])->columns(2),
            ]);
    }
}
