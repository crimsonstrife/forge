<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class IconPicker extends Field
{
    protected string $view = 'forms.components.icon-picker';

    public $showPicker = false; // This is the state to toggle the picker modal

    public $selectedIcon = null; // This is the selected icon - null when no icon is selected

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

    /**
     * Get the Types of the icons.
     */
    public function getTypes()
    {
        // Load types from the database or from a file (as per your project)
        $iconModel = new \App\Models\Icon();
        return $iconModel->loadTypes();
    }

    /**
     * Get the Styles of the icons.
     */
    public function getStyles()
    {
        // Load styles from the database or from a file (as per your project)
        $iconModel = new \App\Models\Icon();
        return $iconModel->loadStyles();
    }

    /**
     * Get the css class of the icon.
     */
    public function getIconClass($icon)
    {
        // Load icon class from the database or from a file (as per your project)
        $iconModel = new \App\Models\Icon();

        // make sure that the icon is an instance of the Icon model
        if ($icon instanceof \App\Models\Icon) {
            return $iconModel->getStyleClass($icon);
        }

        // log an error if the icon is not an instance of the Icon model
        Log::error('The icon is not an instance of the Icon model.');

        // return an empty string if the icon is not an instance of the Icon model
        return '';
    }

    /**
     * Get the icon SVG.
     * Returns the SVG file path, or the SVG code - else a null value.
     *
     * @param \App\Models\Icon|int $icon - The icon object or icon id.
     * @return HtmlString|string|null
     */
    public function getIconSvg(\App\Models\Icon|int $icon)
    {
        // Initialize the SVG variable
        $svg = null;

        // make sure that the icon is an instance of the Icon model
        if ($icon instanceof \App\Models\Icon) {
            // Get the icon SVG
            $svg = $icon->getSvg($icon);
        } elseif (is_int($icon)) {
            // If the icon is an integer, assume it's an icon id and attempt to load the icon from the database
            $iconModel = new \App\Models\Icon();
            $icon = $iconModel->findOrFail($icon);

            // Get the icon SVG
            $svg = $icon->getSvg($icon);
        }

        // Check if the SVG is not empty
        if (!empty($svg)) {
            // Figure out if the SVG is a url or not (if it starts with http:// or https://, it's a url)
            if (strpos($svg, 'http://') === 0 || strpos($svg, 'https://') === 0) {
                // Return the SVG as an svg image string
                $renderString = "<svg class='icon' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><use href='{$svg}'></use></svg>";

                return new HtmlString($renderString);
            } else {
                // Return the SVG as code
                return new HtmlString($svg);
            }
        }

        // log an error if the icon is not an instance of the Icon model
        Log::error('The icon is not an instance of the Icon model.');

        // return null if the icon is not an instance of the Icon model
        return null;
    }

    /**
     * Get the icon associated with the current model.
     * Returns the icon object, or a null value, if no icon is associated.
     * Checks the model for an icon attribute to check the icon id, and returns the icon object if found.
     *
     * @param $model - The model to check for the icon attribute.
     * @return \App\Models\Icon|null
     * @throws \Exception
     */
    public function getIcon($model)
    {
        // Check if the model has an icon attribute
        try {
            if (isset($model->icon)) {
                // Load the icon from the database or from a file (as per your project)
                $iconModel = new \App\Models\Icon();
                return $iconModel->find($model->icon);
            }
        } catch (\Exception $e) {
            // log an error if the icon is not found
            Log::error('The icon is not found.');
            throw new \Exception('The icon is not found.');
        }

        // return null if the model has no icon attribute
        return null;
    }
}
