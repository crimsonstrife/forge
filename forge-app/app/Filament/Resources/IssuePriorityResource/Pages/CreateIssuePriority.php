<?php

namespace App\Filament\Resources\IssuePriorityResource\Pages;

use App\Filament\Resources\IssuePriorityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Issues\IssuePriority;

/**
 * Class CreateIssuePriority
 *
 * This class extends the CreateRecord class and is used to handle the creation of issue priorities
 * within the Filament resource management system.
 *
 * @package App\Filament\Resources\IssuePriorityResource\Pages
 */
class CreateIssuePriority extends CreateRecord
{
    protected static ?string $model = IssuePriority::class;

    protected static string $resource = IssuePriorityResource::class;

    public $selectedIconId;

    protected $listeners = ['iconUpdated' => 'updateIconPreview'];
}
