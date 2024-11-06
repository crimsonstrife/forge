<?php

namespace App\Filament\Resources\DiscordConfigResource\Pages;

use App\Filament\Resources\DiscordConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateDiscordConfig
 *
 * This class extends the CreateRecord class and is used to handle the creation of Discord configuration records
 * within the Filament resource management system.
 *
 * @package App\Filament\Resources\DiscordConfigResource\Pages
 */
class CreateDiscordConfig extends CreateRecord
{
    protected static string $resource = DiscordConfigResource::class;
}
