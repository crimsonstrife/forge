<?php

namespace App\Livewire;

use App\Models\Dashboard;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class DashboardCreate extends Component
{
    public $name;
    public $description;
    public $is_shared = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:255',
        'is_shared' => 'nullable|boolean',
    ];

    public function createDashboard()
    {
        $this->validate();

        $user = Auth::user();

        if (!$user) {
            session()->flash('error', 'You must be logged in to create a dashboard.');
            return redirect()->route('login');
        }

        $dashboard = Dashboard::create([
            'name' => $this->name,
            'description' => $this->description,
            'is_shared' => $this->is_shared ?? false,
            'owner_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
            'created_by' => $user->id ?? null,
            'updated_by' => $user->id ?? null,
        ]);

        $user->dashboards()->attach($dashboard->id);

        session()->flash('success', 'Dashboard created successfully.');
        return redirect()->route('dashboards.manage');
    }

    public function render()
    {
        return view('livewire.dashboard-create');
    }
}
