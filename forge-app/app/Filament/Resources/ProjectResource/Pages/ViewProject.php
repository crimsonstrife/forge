<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/**
 * Handles the viewing of a project record within the Filament resource interface.
 */
class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    /**
     * Retrieves a list of actions with their respective properties and configurations.
     *
     * @return array An array of actions with specified labels, icons, colors, and URLs.
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('kanban')
                ->label(
                    fn ()
                    => ($this->record->type === 'scrum' ? __('Scrum board') : __('Kanban board'))
                )
                ->icon('fab fa-trello')
                ->color('secondary')
                ->url(function () {
                    if ($this->record->type === 'scrum') {
                        return route('filament.pages.scrum/{project}', ['project' => $this->record->id]);
                    } else {
                        return route('filament.pages.kanban/{project}', ['project' => $this->record->id]);
                    }
                }),

            EditAction::make(),
        ];
    }
}
