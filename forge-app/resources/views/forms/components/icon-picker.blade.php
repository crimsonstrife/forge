<div>
    <div class="grid grid-cols-4 gap-2">
        @foreach($getIcons() as $icon)
            <div wire:click="$set('{{ $getStatePath() }}', '{{ $icon->name }}')" class="cursor-pointer border p-2">
                @if($icon->type === 'heroicon')
                    <x-heroicon-o-{{ $icon->name }} class="w-6 h-6" />
                @elseif($icon->type === 'fontawesome')
                    <i class="{{ $icon->name }}"></i>
                @elseif($icon->isCustom())
                    @if($icon->svg_file_path)
                        <img src="{{ Storage::url($icon->svg_file_path) }}" class="w-6 h-6" alt="Custom Icon" />
                    @else
                        {!! $icon->svg_code !!}
                    @endif
                @endif
            </div>
        @endforeach
    </div>
</div>
