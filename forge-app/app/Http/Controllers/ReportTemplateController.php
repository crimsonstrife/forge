<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Templates\ReportTemplate;

/**
 * Class ReportTemplateController
 *
 * This controller handles the operations related to report templates.
 * It extends the base Controller class and provides methods to manage
 * the creation, updating, deletion, and retrieval of report templates.
 *
 * @package App\Http\Controllers
 */
class ReportTemplateController extends Controller
{
    /**
     * Display a listing of the report templates.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $templates = ReportTemplate::all();
        return view('templates.reports.index', compact('templates'));
    }

    /**
     * Show the form for creating a new report template.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('templates.reports.create');
    }

    /**
     * Store a newly created report template in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'nullable|json',
            'settings' => 'nullable|json',
            'filters' => 'nullable|json',
        ]);

        ReportTemplate::create($validated);
        return redirect()->route('templates.reports.index')->with('success', 'Template created successfully.');
    }

    /**
     * Display the specified report template.
     *
     * @param  int  $id  The ID of the report template to display.
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show($id)
    {
        $template = ReportTemplate::findOrFail($id);
        return view('templates.reports.show', compact('template'));
    }

    /**
     * Show the form for editing the specified report template.
     *
     * @param  int  $id  The ID of the report template to edit.
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($id)
    {
        $template = ReportTemplate::findOrFail($id);
        return view('templates.reports.edit', compact('template'));
    }

    /**
     * Update the specified report template.
     *
     * @param \Illuminate\Http\Request $request The request object containing the update data.
     * @param int $id The ID of the report template to update.
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'nullable|json',
            'settings' => 'nullable|json',
            'filters' => 'nullable|json',
        ]);

        $template = ReportTemplate::findOrFail($id);
        $template->update($validated);
        return redirect()->route('templates.reports.index')->with('success', 'Template updated successfully.');
    }

    /**
     * Remove the specified report template from storage.
     *
     * @param  int  $id  The ID of the report template to be deleted.
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $template = ReportTemplate::findOrFail($id);
        $template->delete();
        return redirect()->route('templates.reports.index')->with('success', 'Template deleted successfully.');
    }
}
