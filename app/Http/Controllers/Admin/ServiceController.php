<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query()
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            )
            ->when($request->type, fn($q) =>
                $q->where('type', $request->type)
            )
            ->when($request->status === 'active', fn($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn($q) => $q->where('is_active', false));

        $services      = $query->latest()->paginate(12)->withQueryString();
        $totalServices = Service::count();
        $activeCount   = Service::where('is_active', true)->count();
        $inactiveCount = Service::where('is_active', false)->count();

        return view('admin.services.index', compact(
            'services', 'totalServices', 'activeCount', 'inactiveCount'
        ));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'type'            => 'required|in:per_kg,per_pcs,flat',
            'price'           => 'required|numeric|min:0',
            'estimated_hours' => 'required|integer|min:1',
            'description'     => 'nullable|string',
            'color'           => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon'            => 'required|string|max:50',
            'is_active'       => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $service = Service::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', "Layanan \"{$service->name}\" berhasil ditambahkan!");
    }

    public function show(Service $service)
    {
        return redirect()->route('admin.services.edit', $service);
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'type'            => 'required|in:per_kg,per_pcs,flat',
            'price'           => 'required|numeric|min:0',
            'estimated_hours' => 'required|integer|min:1',
            'description'     => 'nullable|string',
            'color'           => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon'            => 'required|string|max:50',
            'is_active'       => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $service->update($validated);

        return redirect()->route('admin.services.index')
            ->with('success', "Layanan \"{$service->name}\" berhasil diperbarui!");
    }

    public function destroy(Service $service)
    {
        // Future: cek apakah digunakan transaksi
        $name = $service->name;
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', "Layanan \"{$name}\" berhasil dihapus!");
    }

    /**
     * Toggle is_active via AJAX
     */
    public function toggle(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);

        return response()->json([
            'is_active' => $service->is_active,
            'message'   => $service->is_active
                ? "Layanan \"{$service->name}\" diaktifkan."
                : "Layanan \"{$service->name}\" dinonaktifkan.",
        ]);
    }
}
