<?php

namespace App\Models;

use App\Traits\HasExternalId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportExportRecord extends Model
{
    use HasExternalId;

    protected $fillable = [
        'project_id', 'direction', 'status', 'file_path', 'options', 'report',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'report'  => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
