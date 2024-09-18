<?php

namespace App\Models\Issues;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Projects\Project;
use App\Traits\IsPermissable;

/**
 *
 * The IssueStatus model represents the status of an issue in a project.
 */
class IssueStatus extends Model
{
    use HasFactory;
    use SoftDeletes;
    use IsPermissable;

    protected $fillable = [
        'name',
        'color',
        'is_default',
        'order',
        'project_id'
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        /**
         * Event handler for the "saved" event of the IssueStatus model.
         *
         * This event handler is triggered when an IssueStatus model is saved.
         * It performs certain actions based on the saved model's properties.
         * If the saved model is marked as the default status, it updates all other IssueStatus models
         * to set their "is_default" property to false, except for the current model.
         * It also updates the "order" property of other IssueStatus models that have a higher or equal order
         * to the current model, incrementing their order by 1.
         *
         * @param \App\Models\Issues\IssueStatus $item The saved IssueStatus model instance.
         * @return void
         */
        static::saved(function (IssueStatus $item) {
            if ($item->is_default) {
                $query = IssueStatus::where('id', '<>', $item->id)
                    ->where('is_default', true);
                if ($item->project_id) {
                    $query->where('project_id', $item->project->id);
                }
                $query->update(['is_default' => false]);
            }

            $query = IssueStatus::where('order', '>=', $item->order)->where('id', '<>', $item->id);
            if ($item->project_id) {
                $query->where('project_id', $item->project->id);
            }
            $toUpdate = $query->orderBy('order', 'asc')
                ->get();
            $order = $item->order;
            foreach ($toUpdate as $i) {
                if ($i->order == $order || $i->order == ($order + 1)) {
                    $i->order = $i->order + 1;
                    $i->save();
                    $order = $i->order;
                }
            }
        });
    }

    /**
     * Get the issues associated with the issue status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'status_id', 'id')->withTrashed();
    }

    /**
     * Get the project associated with the issue status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
