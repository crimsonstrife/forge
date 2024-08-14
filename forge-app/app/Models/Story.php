<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Xetaio\Mentions\Models\Traits\HasMentionsTrait as HasMentions;

/**
 * Represents a Story model.
 *
 * @package App\Models
 */
class Story extends Model
{
    use HasFactory;
    use HasMentions;
}
