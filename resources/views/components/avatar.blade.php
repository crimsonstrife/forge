@props([
    // Common inputs
    'src'       => null,      // URL for the image
    'name'      => null,      // Used for label + fallback initials
    'initials'  => null,      // Force specific initials (takes priority over name)
    'label'     => null,      // Accessible label (defaults to "<name> avatar" or "User avatar")
    'shape'     => 'circle',  // 'circle' | 'rounded' | 'square'
    'loading'   => 'eager',   // 'eager' | 'lazy'
    'size'      => null,      // CSS size e.g. '32px', '2rem' (sets --size)
    'preset'    => null,      // 'xs'|'sm'|'md'|'lg'|'xl' => maps to common sizes
    'icon'      => null,      // Optional fallback icon name for <wa-icon slot="icon">
])

@php
    // Map presets to pixels (tweak as you like)
    $presetMap = ['xs' => '20px', 'sm' => '24px', 'md' => '32px', 'lg' => '40px', 'xl' => '56px'];
    $resolvedSize = $size ?? ($presetMap[$preset] ?? null);

    // Resolve label
    $resolvedLabel = $label ?? ($name ? ($name . ' avatar') : 'User avatar');

    // Resolve initials
    $resolvedInitials = $initials;
    if (!$src && !$resolvedInitials && $name) {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $first = mb_substr($parts[0] ?? '', 0, 1);
        $second = mb_substr($parts[1] ?? '', 0, 1);
        $resolvedInitials = mb_strtoupper($first . $second);
    }

    // Compose style so callers can still add inline styles
    $style = trim(($resolvedSize ? "--size: {$resolvedSize};" : '') . ' ' . ($attributes->get('style') ?? ''));
@endphp

<wa-avatar
    label="{{ $resolvedLabel }}"
    @if($src) image="{{ $src }}" @endif
    @if($resolvedInitials) initials="{{ $resolvedInitials }}" @endif
    @if($shape) shape="{{ $shape }}" @endif
    @if($loading === 'lazy') loading="lazy" @endif
    {{ $attributes->merge(['style' => $style]) }}
>
    @if(!$src && !$resolvedInitials && $icon)
        <wa-icon slot="icon" name="{{ $icon }}" variant="solid"></wa-icon>
    @endif
</wa-avatar>
