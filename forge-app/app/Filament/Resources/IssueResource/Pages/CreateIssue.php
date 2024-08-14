<?php

namespace App\Filament\Resources\IssueResource\Pages;

use App\Filament\Resources\IssueResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateIssue
 *
 * This class represents a create issue page.
 */
class CreateIssue extends CreateRecord
{
    protected static string $resource = IssueResource::class;
}
