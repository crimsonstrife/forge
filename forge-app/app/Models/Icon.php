<?php

namespace App\Models;

use App\Traits\IsPermissable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Icon extends Model
{
    use HasFactory;
    use IsPermissable;

    protected $fillable = [
        'name',
        'type',
        'style',
        'svg_code',
        'svg_file_path',
    ];

    /**
     * Determine if the icon is a custom uploaded SVG.
     */
    public function isCustom(): bool
    {
        // Custom icons are those that have an SVG file path or inline SVG code, and are not Heroicons or Font Awesome icons.
        // If the result would be null, it will be cast to false always.
        if ($this->svg_file_path || $this->svg_code) {
            return !$this->isHeroicon() && !$this->isFontAwesome() && !$this->isOcticon();
        } else {
            return false;
        }
    }

    /**
     * Determine if the icon is a Font Awesome icon.
     */
    public function isFontAwesome(): bool
    {
        return $this->type === 'fontawesome';
    }

    /**
     * Determine if the icon is a Heroicon.
     */
    public function isHeroicon(): bool
    {
        return $this->type === 'heroicon';
    }

    /**
     * Determin if the icon is a Octicon.
     */
    public function isOcticon(): bool
    {
        return $this->type === 'octicons';
    }

    /**
     * Get the SVG content, either from the stored file or inline code.
     */
    public function getSvgContent(): string
    {
        if ($this->svg_file_path) {
            return Storage::disk('public')->get($this->svg_file_path);
        }

        return $this->svg_code ?? '';
    }

    /**
     * Get the style class for the icon.
     *
     * @return string
     */
    public function getStyleClass(): string
    {
        // Create the style class based on the icon style, type, and name. The styles for Heroicons and Font Awesome icons are different.
        if ($this->isHeroicon()) {
            // Heroicons style class starts with "heroicon-" followed by a prefix based on the icon style, e.g., "o" for "outline", "s" for "solid", etc.
            return 'heroicon-' . substr($this->style, 0, 1) . '-' . $this->name;
        }

        if ($this->isFontAwesome()) {
            // Font Awesome style class starts with "fa" followed by the icon style, e.g., "fa-brands", "fa-solid", etc., and then "fa-" and the icon name.
            return 'fa-' . $this->style . ' fa-' . $this->name;
        }

        // Custom icons will use a custom class name based on the type, style, and name.
        return 'icon-' . $this->type . '-' . $this->style . '-' . $this->name;
    }

    /**
     * Get the icon's URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return route('icons.show', $this);
    }

    /**
     * Get the icon's edit URL.
     *
     * @return string
     */
    public function getEditUrl(): string
    {
        return route('icons.edit', $this);
    }

    /**
     * Get the icon's delete URL.
     *
     * @return string
     */
    public function getDeleteUrl(): string
    {
        return route('icons.delete', $this);
    }

    /**
     * Get the icon's restore URL.
     *
     * @return string
     */
    public function getRestoreUrl(): string
    {
        return route('icons.restore', $this);
    }

    /**
     * Get the icon's force delete URL.
     *
     * @return string
     */
    public function getForceDeleteUrl(): string
    {
        return route('icons.force-delete', $this);
    }
}
