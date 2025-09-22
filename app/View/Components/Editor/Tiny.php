<?php

namespace App\View\Components\Editor;

use Illuminate\Support\Facades\Vite;
use Illuminate\View\Component;

class Tiny extends Component
{
    public string $id;
    public string $name;
    public ?string $value;
    public ?string $wireModel;
    public int $height;

    // Built asset URLs for external_plugins + content_css
    public string $aiJs;
    public string $aiCss;
    public string $mentionsJs;

    public function __construct(
        string $name,
        string $id = 'tiny-'.null,
        ?string $value = null,
        ?string $wireModel = null,
        int $height = 320
    ) {
        $this->name = $name;
        $this->id = $id === 'tiny-' ? 'tiny-'.str()->uuid() : $id;
        $this->value = $value;
        $this->wireModel = $wireModel;
        $this->height = $height;

        // Resolve hashed build paths from Vite's manifest
        $this->aiJs     = Vite::asset('resources/tiny-plugins/action-items/plugin.js');
        $this->aiCss    = Vite::asset('resources/tiny-plugins/action-items/plugin.css');
        $this->mentionsJs = Vite::asset('resources/tiny-plugins/mentions-lite/plugin.js');
    }

    public function render()
    {
        return view('components.editor.tiny');
    }
}
