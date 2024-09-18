<?php

namespace App\Models\Issues;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents the IssueGitPullRequest model.
 *
 * This model extends the base Model class and uses the HasFactory trait.
 * It is used to interact with the "issue_git_pull_requests" table in the database.
 */
class IssueGitPullRequest extends Model
{
    use HasFactory;
}
