<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index($dashboardId)
    {
        $dashboard = Dashboard::with('reports')->findOrFail($dashboardId);

        return view('reports.index', compact('dashboard'));
    }

    public function create($dashboardId)
    {
        $dashboard = Dashboard::findOrFail($dashboardId);

        return view('reports.create', compact('dashboard'));
    }

    public function store(Request $request, $dashboardId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_shared' => 'required|boolean',
        ]);

        $dashboard = Dashboard::findOrFail($dashboardId);

        $dashboard->reports()->create($validated);

        return redirect()->route('reports.index', $dashboardId)->with('success', 'Report created successfully.');
    }
}
