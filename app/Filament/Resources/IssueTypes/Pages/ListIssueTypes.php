<?php

namespace App\Filament\Resources\IssueTypes\Pages;

use App\Filament\Resources\IssueTypes\IssueTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIssueTypes extends ListRecords
{
    protected static string $resource = IssueTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
