<?php

namespace App\Services\Issues;

use App\Models\Issue;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Mews\Purifier\Purifier;

class IssueCommentService
{
    public function __construct(
        private Purifier $purifier,
        private IssueCollaborationService $collaboration,
    ) {
    }

    public function create(Issue $issue, User $author, string $body, ?string $parentId = null): \App\Models\Comment
    {
        $mentionedUsers = $this->extractMentionedUsers($body);
        $sanitizedBody = $this->purifier->clean($body);

        if (str(strip_tags((string) $sanitizedBody))->squish()->length() < 2) {
            throw ValidationException::withMessages([
                'body' => __('Comment cannot be empty.'),
            ]);
        }

        $comment = $issue->comments()->create([
            'user_id' => $author->getKey(),
            'body' => $sanitizedBody,
            'parent_id' => $parentId,
        ]);

        $this->collaboration->notifyMentions($comment, $author, $mentionedUsers);
        $this->collaboration->notifyComment($comment, $author, $mentionedUsers);

        return $comment;
    }

    /**
     * @return Collection<int, User>
     */
    private function extractMentionedUsers(string $body): Collection
    {
        if (! str_contains($body, 'data-mention-type="user"')) {
            return collect();
        }

        $html = '<div>'.$body.'</div>';
        $previous = libxml_use_internal_errors(true);

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        $xpath = new DOMXPath($document);
        $ids = [];

        foreach ($xpath->query('//a[@data-mention-type="user"][@data-mention-id]') ?: [] as $node) {
            $ids[] = (string) $node->attributes?->getNamedItem('data-mention-id')?->nodeValue;
        }

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $ids = array_values(array_unique(array_filter($ids)));
        if ($ids === []) {
            return collect();
        }

        return User::query()
            ->whereIn('id', $ids)
            ->get(['id', 'name', 'email']);
    }
}
