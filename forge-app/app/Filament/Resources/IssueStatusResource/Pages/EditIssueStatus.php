<?php

namespace App\Filament\Resources\IssueStatusResource\Pages;

use App\Filament\Resources\IssueStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIssueStatus extends EditRecord
{
    protected static string $resource = IssueStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
