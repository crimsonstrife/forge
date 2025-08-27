<?php

namespace App\Filament\Resources\IssueStatuses\Pages;

use App\Filament\Resources\IssueStatuses\IssueStatusResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIssueStatus extends CreateRecord
{
    protected static string $resource = IssueStatusResource::class;
}
