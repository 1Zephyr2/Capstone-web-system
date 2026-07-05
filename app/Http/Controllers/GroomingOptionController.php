<?php

namespace App\Http\Controllers;

use App\Models\GroomingOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GroomingOptionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'        => ['required', 'in:style,addon'],
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('grooming', 'public');
        }

        GroomingOption::create(array_merge($validated, [
            'is_active' => true,
            'image'     => $imagePath,
        ]));

        return redirect()->route('admin.panel')
            ->with('success', "Grooming {$validated['type']} \"{$validated['name']}\" added.")
            ->with('panel_tab', 'services');
    }

    public function updateImage(Request $request, GroomingOption $groomingOption)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        // Delete old image if exists
        if ($groomingOption->image) {
            Storage::disk('public')->delete($groomingOption->image);
        }

        $imagePath = $request->file('image')->store('grooming', 'public');
        $groomingOption->update(['image' => $imagePath]);

        return redirect()->route('admin.panel')
            ->with('success', "Image updated for \"{$groomingOption->name}\".")
            ->with('panel_tab', 'services');
    }

    public function destroy(GroomingOption $groomingOption)
    {
        if ($groomingOption->image) {
            Storage::disk('public')->delete($groomingOption->image);
        }

        $name = $groomingOption->name;
        $groomingOption->delete();

        return redirect()->route('admin.panel')
            ->with('success', "\"{$name}\" removed.")
            ->with('panel_tab', 'services');
    }

    public function toggle(GroomingOption $groomingOption)
    {
        $groomingOption->update(['is_active' => !$groomingOption->is_active]);

        return redirect()->route('admin.panel')
            ->with('success', "\"{$groomingOption->name}\" " . ($groomingOption->is_active ? 'enabled' : 'disabled') . '.')
            ->with('panel_tab', 'services');
    }
}