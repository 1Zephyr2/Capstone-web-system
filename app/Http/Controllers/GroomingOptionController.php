<?php

namespace App\Http\Controllers;

use App\Models\GroomingOption;
use Illuminate\Http\Request;

class GroomingOptionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'        => ['required', 'in:style,addon'],
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        GroomingOption::create(array_merge($validated, ['is_active' => true]));

        return redirect()->route('admin.panel')
            ->with('success', "Grooming {$validated['type']} \"{$validated['name']}\" added.")
            ->with('panel_tab', 'services');
    }

    public function destroy(GroomingOption $groomingOption)
    {
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