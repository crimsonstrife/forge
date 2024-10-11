<?php

namespace App\Filament\Resources\DiscordConfigResource\Pages;

use App\Filament\Resources\DiscordConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDiscordConfig extends ListRecords
{
    protected static string $resource = DiscordConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
