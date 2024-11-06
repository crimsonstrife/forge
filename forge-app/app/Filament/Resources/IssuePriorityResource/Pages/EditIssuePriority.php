<?php

namespace App\Filament\Resources\IssuePriorityResource\Pages;

use App\Filament\Resources\IssuePriorityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Issues\IssuePriority;

/**
 * Class EditIssuePriority
 *
 * This class extends the EditRecord class and is used to handle the editing of issue priorities
 * within the Filament resource.
 *
 * @package App\Filament\Resources\IssuePriorityResource\Pages
 */
class EditIssuePriority extends EditRecord
{
    protected static ?string $model = IssuePriority::class;

    protected static string $resource = IssuePriorityResource::class;

    public $selectedIconId;

    protected $listeners = ['iconUpdated' => 'updateIconPreview'];

    /**
     * Get the header actions for the Edit Issue Priority page.
     *
     * @return array The array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
