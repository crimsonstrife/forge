<?php

namespace App\Filament\Pages;

use App\Models\Projects\Project;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

/**
 * Represents a Board page in the application.
 *
 * This class extends the Page class and implements the HasForms interface.
 * It provides functionality for managing forms on the Board page.
 */
class Board extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'fas fa-table-columns';

    protected static string $view = 'filament.pages.board';

    protected static ?string $slug = 'board';

    protected static ?int $navigationSort = 4;

    /**
     * Get the subheading for the Board page.
     *
     * @return string|Htmlable|null The subheading for the Board page.
     */
    public function getSubheading(): string|Htmlable|null
    {
        return __("In this section you can choose one of your projects to show its Scrum or Kanban board");
    }

    /**
     * Mounts the Board page.
     */
    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * Retrieves the navigation label for the Board page.
     *
     * @return string The navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('Board');
    }

    /**
     * Retrieve the navigation group for the Board page.
     *
     * @return string|null The navigation group for the Board page, or null if not set.
     */
    public static function getNavigationGroup(): ?string
    {
        return __('Management');
    }

    /**
     * Retrieves the form schema for the Board page.
     *
     * @return array The form schema.
     */
    protected function getFormSchema(): array
    {
        return [
            Section::make()
                ->schema([
                    Grid::make()
                        ->columns(1)
                        ->schema([
                            Select::make('project')
                                ->label(__('Project'))
                                ->required()
                                ->searchable()
                                ->reactive()
                                ->afterStateUpdated(fn () => $this->search())
                                ->helperText(__("Choose a project to show its board"))
                                ->options(fn() => Project::where('owner_id', Auth::user()->id)
                                    ->orWhereHas('users', function ($query) {
                                        return $query->where('users.id', Auth::user()->id);
                                    })->pluck('name', 'id')->toArray()),
                        ]),
                ]),
        ];
    }

    /**
     * Searches for something.
     *
     * @return void
     */
    public function search(): void
    {
        $data = $this->form->getState();
        $project = Project::find($data['project']);
        if ($project->type === "scrum") {
            $this->redirect(route('filament.pages.scrum/{project}', ['project' => $project]));
        } else {
            $this->redirect(route('filament.pages.kanban/{project}', ['project' => $project]));
        }
    }

    /**
     * Overrides the getFormStatePath method to set its access level to public.
     *
     * @return string
     */
    public function getFormStatePath(): string
    {
        return 'form';
    }
}
