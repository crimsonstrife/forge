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

    public $required = false; // This is the required state of the field

    // Optional: If you want to load all icons into the field
    public function getIcons($page = 1, $perPage = 25)
    {
        // Initialize the icon model
        $iconModel = new \App\Models\Icon();

        // Load only a limited subset of icons per request
        $loadedIcons = $iconModel->paginate($perPage, ['*'], 'page', $page);

        // Return the loaded icons
        return $loadedIcons;
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
            // Check if the model priority is set to file
            if ($icon->isFile($icon)) {
                // Return the SVG file path
                return new HtmlString($svg);
            } else {
                // Return the SVG code
                return $svg;
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

    /**
     * Is the current icon a file?
     * Returns a boolean value indicating if the icon is a file.
     *
     * @param \App\Models\Icon|int $icon - The icon object or icon id.
     *
     * @return bool
     */
    public function isFile(\App\Models\Icon|int $icon)
    {
        // make sure that the icon is an instance of the Icon model
        if ($icon instanceof \App\Models\Icon) {
            return $icon->isFile($icon);
        } elseif (is_int($icon)) {
            // If the icon is an integer, assume it's an icon id and attempt to load the icon from the database
            $iconModel = new \App\Models\Icon();
            $icon = $iconModel->findOrFail($icon);

            return $icon->isFile($icon);
        }

        // log an error if the icon is not an instance of the Icon model
        Log::error('The icon is not an instance of the Icon model.');

        // return false if the icon is not an instance of the Icon model
        return false;
    }
}
