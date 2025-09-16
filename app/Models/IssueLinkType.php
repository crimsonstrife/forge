<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $key
 * @property string $name
 * @property string|null $inverse_name
 * @property bool $is_symmetric
 * @property bool $is_active
 * @property bool $is_system
 */
class IssueLinkType extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'issue_link_types';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'key', 'name', 'inverse_name', 'is_symmetric', 'is_active', 'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_symmetric' => 'bool',
            'is_active' => 'bool',
            'is_system' => 'bool',
        ];
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function outwardLabel(): string
    {
        return $this->name;
    }

    public function inwardLabel(): string
    {
        return $this->is_symmetric ? ($this->name) : ($this->inverse_name ?? $this->name);
    }
}
