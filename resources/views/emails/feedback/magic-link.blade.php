<x-mail::message>
# Sign in to {{ $board->name }}

Use this magic link to sign in to the {{ $board->name }} feedback board for {{ $board->product?->name ?? 'this product' }}.

<x-mail::button :url="$url">
Sign in
</x-mail::button>

This link expires at {{ $magicLink->expires_at?->toDayDateTimeString() }}.

If you did not request this email, you can safely ignore it.
</x-mail::message>
