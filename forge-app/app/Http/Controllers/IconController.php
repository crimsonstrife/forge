<?php

namespace App\Http\Controllers;

use App\Models\Icon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IconController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'icon' => 'required|file|mimes:svg',
            'type' => 'required|string',
            'style' => 'required|string',
        ]);

        // Save the uploaded file using the Icon model method
        $filePath = Icon::saveIconFile($request->file('icon'), $request->input('type'), $request->input('style'));

        // Save the icon details in the database
        Icon::create([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'style' => $request->input('style'),
            // Save the file path in the database, without any preceding slashes
            'svg_file_path' => ltrim($filePath, '/'),
            'svg_code' => null,
        ]);

        return redirect()->back()->with('success', 'Icon uploaded successfully.');
    }

    /**
     * Fetch icons for the icon picker.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchIcons(Request $request)
    {
        // Optional filter variables
        $type = $request->query('type');
        $style = $request->query('style', null); // Can be null, especially if the style is custom
        $prefix = $request->query('prefix'); // Optional filter for prefix, not generally used in the icon picker as the type is sufficient
        $paginate = $request->query('paginate', 60); // Default to 60 icons per page if not specified

        // Query icons with additional filters for non-empty values. Generally a search would look for all records matching the combination of filters provided i.e. AND condition
        $icons = Icon::query()
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when($style, function ($query, $style) {
                return $query->where('style', $style);
            })
            ->when($prefix, function ($query, $prefix) {
                return $query->where('prefix', $prefix);
            })
            ->paginate($paginate);

        // Render each icon preview HTML, filtering out any empty content
        $iconsHtml = $icons->map(function ($icon) {
            // Check for necessary attributes before rendering
            if (empty($icon->name) || empty($icon->prefix) || empty($icon->type)) {
                // Possible issue with the values being misinterpreted as empty or null if they are something like 0 or false, etc, so add a secondary check to ensure they are not null
                if ($icon->name === null || $icon->prefix === null || $icon->type === null) {
                    // Log which attribute failed the check
                    Log::error("Icon preview rendered as undefined or empty for icon ID: {$icon->id}" . PHP_EOL . "Attributes: " . json_encode($icon->only(['name', 'prefix', 'type', 'style'])));
                    return null;
                }

                // Check if the values are 0 or false, as these may be valid values in the case of names or prefixes
                if ($icon->name === '' || $icon->prefix === '' || $icon->type === '') {
                    // Log which attribute failed the check
                    Log::error("Icon preview rendered as undefined or empty for icon ID: {$icon->id}" . PHP_EOL . "Attributes: " . json_encode($icon->only(['name', 'prefix', 'type', 'style'])));
                    return null;
                }
            }

            // Render HTML
            $html = view('components.icon-picker-button', ['icon' => $icon])->render();

            if (trim($html) !== 'undefined' && !empty($html)) {
                return $html;
            } else {
                Log::error("Icon preview rendered as undefined or empty for icon ID: {$icon->id}");
                return null;
            }
        })->filter()->values(); // Filter and re-index

        // Return response with pagination details
        return response()->json([
            'icons' => $iconsHtml,
            'pagination' => [
                'total' => $icons->total(),
                'perPage' => $icons->perPage(),
                'currentPage' => $icons->currentPage(),
                'lastPage' => $icons->lastPage(),
                'hasMorePages' => $icons->hasMorePages(),
            ] // Add more pagination details as needed
        ], 200, ['Content-Type' => 'application/json']);
    }
}
