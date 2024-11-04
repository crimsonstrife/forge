<?php

namespace App\Filament\Resources\IssuePriorityResource\Pages;

use App\Filament\Resources\IssuePriorityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIssuePriority extends EditRecord
{
    protected static ?string $model = IssuePriority::class;

    protected static string $resource = IssuePriorityResource::class;

    public $selectedIconId;

    protected $listeners = ['iconUpdated' => 'updateIconPreview'];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
