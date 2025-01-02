<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectRole;
use App\Models\Projects\ProjectPermission;

class ProjectRoleController extends Controller
{
    public function index(Project $project)
    {
        $roles = $project->roles()->with('permissions')->get();
        return view('projects.roles.index', compact('project', 'roles'));
    }

    public function create(Project $project)
    {
        $permissions = ProjectPermission::all();
        return view('projects.roles.create', compact('project', 'permissions'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:project_permissions,id',
        ]);

        $role = $project->roles()->create(['name' => $validated['name']]);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('projects.roles.index', $project)
            ->with('success', 'Role created successfully.');
    }

    public function edit(Project $project, ProjectRole $role)
    {
        $permissions = ProjectPermission::all();
        return view('projects.roles.edit', compact('project', 'role', 'permissions'));
    }

    public function update(Request $request, Project $project, ProjectRole $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:project_permissions,id',
        ]);

        $role->update(['name' => $validated['name']]);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('projects.roles.index', $project)
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Project $project, ProjectRole $role)
    {
        $role->delete();
        return redirect()->route('projects.roles.index', $project)
            ->with('success', 'Role deleted successfully.');
    }
}
