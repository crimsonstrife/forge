<?php

namespace App\Models;

use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ServiceProduct extends BaseModel
{
    use HasUlids;
    use IsPermissible;

    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::ulid();
        });
    }

    /** @return BelongsTo<Organization,ServiceProduct> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return BelongsTo<Project,ServiceProduct> */
    public function defaultProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'default_project_id');
    }

    /** @return BelongsToMany<Project> */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_service_product');
    }

    /** @return HasMany<Ticket> */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'service_product_id');
    }

    /** @return HasMany<ServiceProductTypeMap> */
    public function typeMaps(): HasMany
    {
        return $this->hasMany(ServiceProductTypeMap::class);
    }

    /** @return HasMany<ServiceProductStatusMap> */
    public function statusMaps(): HasMany
    {
        return $this->hasMany(ServiceProductStatusMap::class);
    }

    /** @return HasMany<ServiceProductPriorityMap> */
    public function priorityMaps(): HasMany
    {
        return $this->hasMany(ServiceProductPriorityMap::class);
    }

    /**
     * Resolve mapped Issue Type ID for a given Ticket Type, honoring project allowed sets.
     * @param int $ticketTypeId
     * @param Project|null $project
     * @return int|null
     */
    public function resolveIssueTypeId(int $ticketTypeId, ?Project $project = null): ?int
    {
        $issueTypeId = (int) $this->typeMaps()->where('ticket_type_id', $ticketTypeId)->value('issue_type_id');

        if ($issueTypeId && $project) {
            $allowed = $project->allowedTypeIds();
            if (! in_array($issueTypeId, $allowed, true)) {
                $issueTypeId = null;
            }
        }

        return $issueTypeId ?? ($project?->defaultTypeId());
    }

    /**
     * Resolve mapped Issue Priority ID for a given Ticket Priority, honoring project allowed sets.
     * @param int $ticketPriorityId
     * @param Project|null $project
     * @return int|null
     */
    public function resolveIssuePriorityId(int $ticketPriorityId, ?Project $project = null): ?int
    {
        $issuePriorityId = (int) $this->priorityMaps()->where('ticket_priority_id', $ticketPriorityId)->value('issue_priority_id');

        if ($issuePriorityId && $project) {
            $allowed = $project->allowedPriorityIds();
            if (! in_array($issuePriorityId, $allowed, true)) {
                $issuePriorityId = null;
            }
        }

        return $issuePriorityId ?: ($project?->defaultPriorityId());
    }

    /**
     * Resolve mapped Issue Status ID for a given Ticket Status, honoring project allowed sets.
     * Falls back to project initial status if not mapped.
     * @param int $ticketStatusId
     * @param Project|null $project
     * @return int|null
     */
    public function resolveIssueStatusId(int $ticketStatusId, ?Project $project = null): ?int
    {
        $issueStatusId = (int) $this->statusMaps()->where('ticket_status_id', $ticketStatusId)->value('issue_status_id');

        if ($issueStatusId && $project) {
            $allowed = $project->allowedStatusIds();
            if (! in_array($issueStatusId, $allowed, true)) {
                $issueStatusId = null;
            }
        }

        return $issueStatusId ?? ($project?->initialStatusId());
    }
}
