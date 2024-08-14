<?php

namespace App\Filament\Resources\StoryResource\Pages;

use App\Filament\Resources\StoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateStory
 *
 * This class represents a create story page.
 */
class CreateStory extends CreateRecord
{
    protected static string $resource = StoryResource::class;
}
