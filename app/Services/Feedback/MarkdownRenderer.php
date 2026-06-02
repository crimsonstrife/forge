<?php

namespace App\Services\Feedback;

use Illuminate\Support\Str;
use Mews\Purifier\Purifier;

class MarkdownRenderer
{
    public function __construct(private Purifier $purifier) {}

    public function render(string $markdown): string
    {
        $html = Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return (string) $this->purifier->clean($html);
    }
}
