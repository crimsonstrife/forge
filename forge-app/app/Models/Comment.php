<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Xetaio\Mentions\Models\Traits\HasMentionsTrait as HasMentions;
use App\Traits\IsPermissable;

/**
 * Class Comment
 * Model for the Comment table
 * @package App\Models
 */
class Comment extends Model
{
    use HasFactory;
    use HasMentions;
    use IsPermissable;
}
