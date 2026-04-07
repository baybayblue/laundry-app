<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $query = Discount::query()
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%")
            )
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->status === 'active', fn($q) =>
                $q->where('is_active', true)
                  ->where(fn($q2) => $q2->whereNull('end_date')->orWhere('end_date', '>=', now()))
                  ->where(fn($q2) => $q2->whereNull('start_date')->orWhere('start_date', '<=', now()))
            )
            ->when($request->status === 'inactive', fn($q) => $q->where('is_active', false))
            ->when($request->status === 'expired',  fn($q) => $q->where('end_date', '<', now()))
            ->when($request->status === 'upcoming', fn($q) => $q->where('start_date', '>', now()));

        $discounts     = $query->latest()->paginate(12)->withQueryString();
        $totalCount    = Discount::count();
        $activeCount   = Discount::where('is_active', true)
            ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))
            ->where(fn($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
            ->count();
        $expiredCount  = Discount::where('end_date', '<', now())->count();
        $upcomingCount = Discount::where('start_date', '>', now())->count();

        return view('admin.discounts.index', compact(
            'discounts', 'totalCount', 'activeCount', 'expiredCount', 'upcomingCount'
        ));
    }

    public function create()
    {
        return view('admin.discounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => 'nullable|string|max:50|unique:discounts,code',
            'type'            => 'required|in:percentage,fixed',
            'value'           => 'required|numeric|min:0.01',
            'min_transaction' => 'nullable|numeric|min:0',
            'max_discount'    => 'nullable|numeric|min:0',
            'usage_limit'     => 'nullable|integer|min:1',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'description'     => 'nullable|string',
            'is_active'       => 'boolean',
        ]);

        if ($request->type === 'percentage' && $validated['value'] > 100) {
            return back()->withErrors(['value' => 'Persentase diskon tidak boleh lebih dari 100%'])->withInput();
        }

        // Auto-generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = strtoupper(Str::random(8));
        } else {
            $validated['code'] = strtoupper($validated['code']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $discount = Discount::create($validated);

        return redirect()->route('admin.discounts.index')
            ->with('success', "Diskon \"{$discount->name}\" berhasil ditambahkan!");
    }

    public function show(Discount $discount)
    {
        return redirect()->route('admin.discounts.edit', $discount);
    }

    public function edit(Discount $discount)
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => "nullable|string|max:50|unique:discounts,code,{$discount->id}",
            'type'            => 'required|in:percentage,fixed',
            'value'           => 'required|numeric|min:0.01',
            'min_transaction' => 'nullable|numeric|min:0',
            'max_discount'    => 'nullable|numeric|min:0',
            'usage_limit'     => 'nullable|integer|min:1',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'description'     => 'nullable|string',
            'is_active'       => 'boolean',
        ]);

        if ($request->type === 'percentage' && $validated['value'] > 100) {
            return back()->withErrors(['value' => 'Persentase diskon tidak boleh lebih dari 100%'])->withInput();
        }

        if (!empty($validated['code'])) {
            $validated['code'] = strtoupper($validated['code']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $discount->update($validated);

        return redirect()->route('admin.discounts.index')
            ->with('success', "Diskon \"{$discount->name}\" berhasil diperbarui!");
    }

    public function destroy(Discount $discount)
    {
        $name = $discount->name;
        $discount->delete();

        return redirect()->route('admin.discounts.index')
            ->with('success', "Diskon \"{$name}\" berhasil dihapus!");
    }

    /** Toggle aktif/nonaktif via AJAX */
    public function toggle(Discount $discount)
    {
        $discount->update(['is_active' => !$discount->is_active]);

        return response()->json([
            'is_active' => $discount->is_active,
            'message'   => $discount->is_active
                ? "Diskon \"{$discount->name}\" diaktifkan."
                : "Diskon \"{$discount->name}\" dinonaktifkan.",
        ]);
    }

    /** Generate kode unik via AJAX */
    public function generateCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Discount::where('code', $code)->exists());

        return response()->json(['code' => $code]);
    }
}
