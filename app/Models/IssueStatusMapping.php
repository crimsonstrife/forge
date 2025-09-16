<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class IssueStatusMapping extends Model
{
    use HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['repository_id','provider','external_state','issue_status_id'];
    protected $casts = ['id' => 'string'];

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function repository(): BelongsTo { return $this->belongsTo(Repository::class); }
    public function status(): BelongsTo { return $this->belongsTo(IssueStatus::class, 'issue_status_id'); }
}
