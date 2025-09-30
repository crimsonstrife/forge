<?php

namespace App\Domain\Issues\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when an issue's assignee is set or changed.
 */
final class IssueAssigneeChanged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public string $issueId,
        public string $newAssigneeId,
    ) {
    }
}
