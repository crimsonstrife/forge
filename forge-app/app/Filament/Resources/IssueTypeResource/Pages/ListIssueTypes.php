<?php

namespace App\Filament\Resources\IssueTypeResource\Pages;

use App\Filament\Resources\IssueTypeResource;
use App\Models\Issues\IssueType;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIssueTypes extends ListRecords
{
    protected static ?string $model = IssueType::class;

    protected static string $resource = IssueTypeResource::class;

    public $selectedIconId;

    protected $listeners = ['iconUpdated' => 'updateIconPreview'];

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
