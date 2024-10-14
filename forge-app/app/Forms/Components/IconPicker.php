<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Illuminate\Support\HtmlString;

class IconPicker extends Field
{
    protected string $view = 'forms.components.icon-picker';

    // Optional: If you want to load all icons into the field
    public function getIcons()
    {
        // Load icons from the database or from a file (as per your project)
        return \App\Models\Icon::all();
    }
}
