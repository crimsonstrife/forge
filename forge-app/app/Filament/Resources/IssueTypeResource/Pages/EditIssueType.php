<?php

namespace App\Filament\Resources\IssueTypeResource\Pages;

use App\Filament\Resources\IssueTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Issues\IssueType;

/**
 * Class EditIssueType
 *
 * This class extends the EditRecord class and is used to handle the editing of issue types
 * within the Filament resource management system.
 *
 * @package App\Filament\Resources\IssueTypeResource\Pages
 */
class EditIssueType extends EditRecord
{
    protected static ?string $model = IssueType::class;
    protected static string $resource = IssueTypeResource::class;

    public $selectedIconId;

    protected $listeners = ['iconUpdated' => 'updateIconPreview'];

    /**
     * Get the header actions for the issue type resource.
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
