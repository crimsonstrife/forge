<?php

namespace App\Filament\Resources\IssueTypeResource\Pages;

use App\Filament\Resources\IssueTypeResource;
use App\Models\Issues\IssueType;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListIssueTypes
 *
 * This class extends the ListRecords class and is used to list issue types in the application.
 *
 * @package App\Filament\Resources\IssueTypeResource\Pages
 */
class ListIssueTypes extends ListRecords
{
    protected static ?string $model = IssueType::class;

    protected static string $resource = IssueTypeResource::class;

    public $selectedIconId;

    protected $listeners = ['iconUpdated' => 'updateIconPreview'];

    /**
     * Get the header actions for the issue types list page.
     *
     * @return array The array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
