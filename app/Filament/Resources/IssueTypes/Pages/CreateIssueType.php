<?php

namespace App\Filament\Resources\IssueTypes\Pages;

use App\Filament\Resources\IssueTypes\IssueTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIssueType extends CreateRecord
{
    protected static string $resource = IssueTypeResource::class;
}
