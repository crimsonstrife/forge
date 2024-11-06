<?php

namespace App\Filament\Resources\IssueStatusResource\Pages;

use App\Filament\Resources\IssueStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateIssueStatus
 *
 * This class extends the CreateRecord class and is used to handle the creation of issue statuses
 * within the Filament resource management system.
 *
 * @package App\Filament\Resources\IssueStatusResource\Pages
 */
class CreateIssueStatus extends CreateRecord
{
    protected static string $resource = IssueStatusResource::class;
}
