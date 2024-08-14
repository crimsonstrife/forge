<?php

namespace App\Filament\Resources\IssuePriorityResource\Pages;

use App\Filament\Resources\IssuePriorityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateIssuePriority
 *
 * This class represents a create issue priority page.
 */
class CreateIssuePriority extends CreateRecord
{
    protected static string $resource = IssuePriorityResource::class;
}
