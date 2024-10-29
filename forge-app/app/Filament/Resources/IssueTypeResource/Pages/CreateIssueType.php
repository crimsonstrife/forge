<?php

namespace App\Filament\Resources\IssueTypeResource\Pages;

use App\Filament\Resources\IssueTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIssueType extends CreateRecord
{
    protected static ?string $model = IssueType::class;

    protected static string $resource = IssueTypeResource::class;

    public $selectedIconId;

    protected $listeners = ['iconUpdated' => 'updateIconPreview'];
}
