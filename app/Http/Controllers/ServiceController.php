<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:' . implode(',', Service::CATEGORIES)],
        ]);

        Service::create(array_merge($validated, ['is_active' => true]));

        return redirect()->route('admin.panel')
            ->with('success', "Service \"{$validated['name']}\" added.")
            ->with('panel_tab', 'booking-services');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:' . implode(',', Service::CATEGORIES)],
        ]);

        $service->update($validated);

        return redirect()->route('admin.panel')
            ->with('success', "Service \"{$service->name}\" updated.")
            ->with('panel_tab', 'booking-services');
    }

    public function toggle(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);

        return redirect()->route('admin.panel')
            ->with('success', "\"{$service->name}\" " . ($service->is_active ? 'enabled' : 'disabled') . '.')
            ->with('panel_tab', 'booking-services');
    }

    public function destroy(Service $service)
    {
        $name = $service->name;
        $service->delete(); // appointments.service_id nulls out via nullOnDelete, history preserved

        return redirect()->route('admin.panel')
            ->with('success', "\"{$name}\" removed.")
            ->with('panel_tab', 'booking-services');
    }
}