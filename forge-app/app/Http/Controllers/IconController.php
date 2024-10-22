<?php

namespace App\Http\Controllers;

use App\Models\Icon;
use Illuminate\Http\Request;

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
}
