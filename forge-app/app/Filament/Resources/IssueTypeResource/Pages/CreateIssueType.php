<?php

namespace App\Filament\Resources\IssueTypeResource\Pages;

use App\Filament\Resources\IssueTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Issues\IssueType;

/**
 * Class CreateIssueType
 *
 * This class handles the creation of issue types within the application.
 * It extends the CreateRecord class to inherit the functionality for creating records.
 *
 * @package App\Filament\Resources\IssueTypeResource\Pages
 */
class CreateIssueType extends CreateRecord
{
    protected static ?string $model = IssueType::class;

    protected static string $resource = IssueTypeResource::class;

    public $selectedIconId;

    protected $listeners = ['iconUpdated' => 'updateIconPreview'];
}
