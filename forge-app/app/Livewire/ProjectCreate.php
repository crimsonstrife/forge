<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectType;
use App\Models\Projects\ProjectStatus;
use Illuminate\Support\Facades\Auth;
use Xetaio\Mentions\Parser\MentionParser;
use App\Services\CrucibleService;

/**
 * Class ProjectCreate
 *
 * This Livewire component handles the creation of new projects.
 *
 * @package App\Livewire
 */
class ProjectCreate extends Component
{
    public $name;
    public $description;
    public $type_id;
    public $status_id;
    public $repository_url;
    public $projectTypes;
    public $projectStatuses;

    /**
     * The mount method is called when the Livewire component is initialized.
     * It is used to set up any initial state or perform any actions needed
     * before the component is rendered.
     *
     * @return void
     */
    public function mount()
    {
        $this->projectTypes = ProjectType::all();
        $this->projectStatuses = ProjectStatus::all();
    }

    /**
     * Save the project data.
     *
     * This method handles the saving of project data to the database.
     *
     * @return void
     */
    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type_id' => 'required|exists:project_types,id',
            'status_id' => 'required|exists:project_statuses,id',
            'repository_url' => 'nullable|url',
        ]);

        $project = Project::create([
            'name' => $this->name,
            'description' => $this->description,
            'type_id' => $this->type_id,
            'status_id' => $this->status_id,
            'owner_id' => Auth::id(),
        ]);

        // Add tags to the project
        $project->tags()->sync($this->tags);

        // Handle repository syncing if a URL is provided
        if (!empty($this->repository_url)) {
            $success = $project->syncRepository($this->repository_url);
            if (!$success) {
                $this->addError('repository_url', 'Failed to sync repository. Check the URL or Crucible settings.');
                return;
            }
        }

        return redirect()->route('dashboard')->with('success', 'Project created successfully!');
    }

    /**
     * Render the view for the ProjectCreate component.
     *
     * @return \Illuminate\View\View The view to be rendered.
     */
    public function render()
    {
        return view('livewire.project-create');
    }
}
