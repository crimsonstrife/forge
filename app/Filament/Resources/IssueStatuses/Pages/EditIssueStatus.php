<?php

namespace App\Filament\Resources\IssueStatuses\Pages;

use App\Filament\Resources\IssueStatuses\IssueStatusResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditIssueStatus extends EditRecord
{
    protected static string $resource = IssueStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
