<?php

namespace App\Services\Feedback;

use App\Models\FeedbackIdentity;
use App\Models\FeedbackMagicLink;

final readonly class MagicLinkVerifyResult
{
    public function __construct(
        public FeedbackIdentity $identity,
        public bool $requiresDisplayName,
        public FeedbackMagicLink $magicLink,
    ) {}
}
