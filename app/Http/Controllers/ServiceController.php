<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'category'     => ['required', 'in:' . implode(',', Service::CATEGORIES)],
            'pricing_mode' => ['required', 'in:size,flat'],
            'prices'       => ['required_if:pricing_mode,size', 'array'],
            'prices.*'     => ['nullable', 'numeric', 'min:0'],
            'flat_price'   => ['required_if:pricing_mode,flat', 'nullable', 'numeric', 'min:0'],
        ]);

        $prices = $validated['pricing_mode'] === 'flat'
            ? ['flat' => $validated['flat_price']]
            : collect($validated['prices'] ?? [])->filter(fn($v) => $v !== null && $v !== '')->toArray();

        Service::create([
            'name'     => $validated['name'],
            'category' => $validated['category'],
            'prices'   => $prices,
            'is_active' => true,
        ]);

        return redirect()->route('admin.panel')
            ->with('success', "Service \"{$validated['name']}\" added.")
            ->with('panel_tab', 'booking-services');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'category'     => ['required', 'in:' . implode(',', Service::CATEGORIES)],
            'pricing_mode' => ['required', 'in:size,flat'],
            'prices'       => ['required_if:pricing_mode,size', 'array'],
            'prices.*'     => ['nullable', 'numeric', 'min:0'],
            'flat_price'   => ['required_if:pricing_mode,flat', 'nullable', 'numeric', 'min:0'],
        ]);

        $prices = $validated['pricing_mode'] === 'flat'
            ? ['flat' => $validated['flat_price']]
            : collect($validated['prices'] ?? [])->filter(fn($v) => $v !== null && $v !== '')->toArray();

        $service->update([
            'name'     => $validated['name'],
            'category' => $validated['category'],
            'prices'   => $prices,
        ]);

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
        $service->update(['is_archived' => true, 'is_active' => false]);

        return redirect()->route('admin.panel')
            ->with('success', "\"{$name}\" archived. You can restore it anytime from the Archived list.")
            ->with('panel_tab', 'booking-services');
    }

    public function restore(Service $service)
    {
        $service->update(['is_archived' => false]);

        return redirect()->route('admin.panel')
            ->with('success', "\"{$service->name}\" restored.")
            ->with('panel_tab', 'booking-services');
    }
}