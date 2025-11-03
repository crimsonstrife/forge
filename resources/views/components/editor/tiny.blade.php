@props([
  'toolbar' => 'undo redo | bold italic link | bullist numlist | checklist mentionUser mentionIssue | code',
  'plugins' => 'link lists code',
  'placeholder' => '',
  'baseUrl' => '/build/vendor/tinymce',
])

<div
    wire:ignore
    x-data="tinyEditor({
      elId: '{{ $id }}',
      name: '{{ $name }}',
      height: {{ $height }},
      baseUrl: '{{ $baseUrl }}',
      externalPlugins: {
        'action-items': '{{ $aiJs }}',
        'mentions-lite': '{{ $mentionsJs }}',
      },
      // Theme-aware UI + content
      skin: (window.__isDarkTheme && window.__isDarkTheme()) ? 'oxide-dark' : 'oxide',
      contentCss: [
        '{{ $aiCss }}',
        (window.__isDarkTheme && window.__isDarkTheme())
          ? '{{ rtrim($baseUrl, '/') }}/skins/content/dark/content.min.css'
          : '{{ rtrim($baseUrl, '/') }}/skins/content/default/content.min.css'
      ],
      toolbar: @js($toolbar),
      plugins: @js($plugins),
      wireModel: @js($wireModel),
      initial: @js($value ?? null),
    })"
    x-init="init()"
    x-on:tiny-reinit.window="
  const isDark = (window.__isDarkTheme && window.__isDarkTheme());
  const ed = window.tinymce?.get('{{ $id }}');
  const html = ed?.getContent?.() ?? document.getElementById('{{ $id }}')?.value ?? null;

  setTheme({
    skin: isDark ? 'oxide-dark' : 'oxide',
    contentCss: [
      '{{ $aiCss }}',
      isDark
        ? '{{ rtrim($baseUrl, '/') }}/skins/content/dark/content.min.css'
        : '{{ rtrim($baseUrl, '/') }}/skins/content/default/content.min.css'
    ]
  });

  ed?.remove?.();
  init(html);
"
    x-on:theme-changed.window="
  const isDark = (window.__isDarkTheme && window.__isDarkTheme());
  const ed = window.tinymce?.get('{{ $id }}');
  const html = ed?.getContent?.() ?? document.getElementById('{{ $id }}')?.value ?? null;

  setTheme({
    skin: isDark ? 'oxide-dark' : 'oxide',
    contentCss: [
      '{{ $aiCss }}',
      isDark
        ? '{{ rtrim($baseUrl, '/') }}/skins/content/dark/content.min.css'
        : '{{ rtrim($baseUrl, '/') }}/skins/content/default/content.min.css'
    ]
  });

  ed?.remove?.();
  init(html);
"
    class="w-100"
>
<textarea id="{{ $id }}" name="{{ $name }}" class="form-control" placeholder="{{ $placeholder }}">
@if($value){!! $value !!}@endif
  </textarea>
</div>
