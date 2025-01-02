<?php

namespace App\Models;

use App\Models\Templates\ReportTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\IsPermissible;

class Report extends Model
{
    use SoftDeletes;
    use IsPermissible;

    protected $fillable = ['title', 'description', 'content', 'settings', 'filters', 'dashboard_id', 'template_id', 'order'];

    /**
     * Define the Dashboard relationship.
     *
     * @return mixed The dashboard data.
     */
    public function dashboard()
    {
        return $this->belongsTo(Dashboard::class, 'dashboard_id');
    }

    /**
     * Define the owner relationship.
     *
     * @return mixed The owner data.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the user that owns the report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the template associated with the report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template()
    {
        return $this->belongsTo(ReportTemplate::class);
    }

    /**
     * Get the dashboards associated with the report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dashboards()
    {
        return $this->belongsToMany(Dashboard::class, 'dashboard_report');
    }

    /**
     * Display the specified report.
     *
     * @param int $dashboardId The ID of the dashboard.
     * @param int $reportId The ID of the report.
     * @return \Illuminate\Http\Response
     */
    public function show($dashboardId, $reportId)
    {
        $report = Report::findOrFail($reportId);

        if (!$report->is_shared && $report->dashboard_id != $dashboardId) {
            abort(403);
        }

        return response()->view('reports.show', compact('report'));
    }
}
