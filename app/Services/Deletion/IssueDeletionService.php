<?php

namespace App\Services\Deletion;

use App\Exceptions\CannotDeleteWithOpenChildren;
use App\Models\Issue;
use Illuminate\Support\Facades\DB;
use Throwable;

final class IssueDeletionService
{
    /**
     * @throws Throwable
     */
    public function delete(Issue $issue): void
    {
        // Authorization handled at controller/policy level.

        DB::transaction(function () use ($issue): void {
            // Block if any child issue is NOT done
            $hasOpenChildren = $issue->children()
                ->whereHas('status', fn ($q) => $q->where('is_done', false))
                ->exists();

            if ($hasOpenChildren) {
                throw CannotDeleteWithOpenChildren::forIssue($issue->summary ?? $issue->key ?? (string) $issue->getKey());
            }

            // Recursively delete done children first (safe cascade)
            $issue->children()
                ->with(['status:id,is_done'])
                ->get()
                ->each(function (Issue $child): void {
                    if ($child->status?->is_done === true) {
                        $this->delete($child);
                    }
                });

            // Detach/cleanup related models
            // Adjust relation names if they differ in your app.
            if (method_exists($issue, 'tags')) {
                $issue->tags()->detach();
            }

            if (method_exists($issue, 'timeEntries')) {
                $issue->timeEntries()->delete();
            }

            if (method_exists($issue, 'externalRefs')) {
                $issue->externalRefs()->delete();
            }

            if (method_exists($issue, 'vcsLinks')) {
                $issue->vcsLinks()->delete();
            }

            if (method_exists($issue, 'comments')) {
                // If using a morphMany comments relation
                $issue->comments()->delete();
            }

            // Spatie Media Library attachments (collection: 'attachments')
            // Clear explicitly to also remove files from disk.
            if (method_exists($issue, 'clearMediaCollection')) {
                $issue->clearMediaCollection('attachments');
            }

            // Finally delete the Issue
            $issue->delete();
        });
    }
}
