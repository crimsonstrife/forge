<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teams\Team;
use App\Models\Teams\TeamRole;
use App\Models\Teams\TeamPermission;

class TeamRoleController extends Controller
{
    public function index(Team $team)
    {
        $roles = $team->roles()->with('permissions')->get();
        return view('teams.roles.index', compact('team', 'roles'));
    }

    public function create(Team $team)
    {
        $permissions = TeamPermission::all();
        return view('teams.roles.create', compact('team', 'permissions'));
    }

    public function store(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:team_permissions,id',
        ]);

        $role = $team->roles()->create(['name' => $validated['name']]);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('teams.roles.index', $team)
            ->with('success', 'Role created successfully.');
    }

    public function edit(Team $team, TeamRole $role)
    {
        $permissions = TeamPermission::all();
        return view('teams.roles.edit', compact('team', 'role', 'permissions'));
    }

    public function update(Request $request, Team $team, TeamRole $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:team_permissions,id',
        ]);

        $role->update(['name' => $validated['name']]);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('teams.roles.index', $team)
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Team $team, TeamRole $role)
    {
        $role->delete();
        return redirect()->route('teams.roles.index', $team)
            ->with('success', 'Role deleted successfully.');
    }
}
