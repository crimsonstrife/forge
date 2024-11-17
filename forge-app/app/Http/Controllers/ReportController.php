<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use App\Models\Report;
use App\Models\Templates\ReportTemplate;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'template_id' => 'nullable|exists:report_templates,id',
            'filters' => 'nullable|json',
        ]);

        $template = ReportTemplate::find($validated['template_id']);
        $reportContent = $template ? $template->content : [];

        $report = Report::create([
            'name' => $validated['name'],
            'content' => $reportContent,
            'filters' => $validated['filters'],
        ]);

        return redirect()->route('reports.index')->with('success', 'Report created.');
    }

    /**
     * Clone a report by its ID.
     *
     * @param int $id The ID of the report to clone.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clone($id)
    {
        $report = Report::findOrFail($id);
        $clonedReport = $report->replicate();
        $clonedReport->title = $report->title . ' (Copy)';
        $clonedReport->save();

        return redirect()->route('reports.show', $clonedReport->id)->with('success', 'Report cloned successfully.');
    }
}
