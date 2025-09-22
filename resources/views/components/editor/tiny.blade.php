@props([
  'toolbar' => 'undo redo | bold italic link | bullist numlist | checklist mentionUser mentionIssue | code',
  'plugins' => 'link lists code',
  'placeholder' => '',
  'baseUrl' => '/build/vendor/tinymce',
])

<script src="{{ $baseUrl }}/tinymce.js"></script>

<div
    x-data="tinyEditor({
    elId: '{{ $id }}',
    name: '{{ $name }}',
    height: {{ $height }},
    baseUrl: '{{ $baseUrl }}',
    externalPlugins: {
      'action-items': '{{ $aiJs }}',
      'mentions-lite': '{{ $mentionsJs }}',
    },
    contentCss: ['{{ $aiCss }}'],
    toolbar: @js($toolbar),
    plugins: @js($plugins),
    wireModel: @js($wireModel),
  })"
    x-init="init()"
    x-on:tiny-reinit.window="destroy(); init();"
    class="w-100"
    wire:ignore
>
  <textarea id="{{ $id }}" name="{{ $name }}" class="form-control" placeholder="{{ $placeholder }}" @if($wireModel) wire:ignore @endif>
@if($value){!! $value !!}@endif
  </textarea>
</div>
