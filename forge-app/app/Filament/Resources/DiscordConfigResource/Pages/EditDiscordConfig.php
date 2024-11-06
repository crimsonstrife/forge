<?php

namespace App\Filament\Resources\DiscordConfigResource\Pages;

use App\Filament\Resources\DiscordConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditDiscordConfig
 *
 * This class extends the EditRecord class and is used to handle the editing of Discord configuration records
 * within the Filament resource.
 *
 * @package App\Filament\Resources\DiscordConfigResource\Pages
 */
class EditDiscordConfig extends EditRecord
{
    protected static string $resource = DiscordConfigResource::class;

    /**
     * Get the header actions for the Discord configuration resource.
     *
     * @return array The array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
