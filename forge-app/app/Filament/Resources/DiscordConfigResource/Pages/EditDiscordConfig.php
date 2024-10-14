<?php

namespace App\Filament\Resources\DiscordConfigResource\Pages;

use App\Filament\Resources\DiscordConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDiscordConfig extends EditRecord
{
    protected static string $resource = DiscordConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
