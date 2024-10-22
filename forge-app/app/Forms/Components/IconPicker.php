<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Illuminate\Support\HtmlString;

class IconPicker extends Field
{
    protected string $view = 'forms.components.icon-picker';

    public $showPicker = false; // This is the state to toggle the picker modal

    // Optional: If you want to load all icons into the field
    public function getIcons()
    {
        // Load icons from the database or from a file (as per your project)
        $iconModel = new \App\Models\Icon();
        return $iconModel->loadAllIcons();
    }

    /**
     * Toggle the visibility of the picker modal.
     */
    public function togglePicker()
    {
        $this->showPicker = !$this->showPicker;
    }
}
