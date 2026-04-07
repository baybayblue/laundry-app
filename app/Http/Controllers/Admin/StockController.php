<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\StockItem;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = StockItem::with('category')
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('supplier', 'like', '%' . $request->search . '%')
            )
            ->when($request->category, fn($q) =>
                $q->where('category_id', $request->category)
            )
            ->when($request->status === 'low', fn($q) =>
                $q->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0)
            )
            ->when($request->status === 'empty', fn($q) =>
                $q->where('stock', 0)
            );

        $items         = $query->latest()->paginate(10)->withQueryString();
        $totalItems    = StockItem::count();
        $lowStockCount = StockItem::whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0)->count();
        $emptyCount    = StockItem::where('stock', 0)->count();
        $categories    = Category::orderBy('name')->get();

        return view('admin.stock.index', compact(
            'items', 'totalItems', 'lowStockCount', 'emptyCount', 'categories'
        ));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.stock.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'unit'           => 'required|string|max:50',
            'stock'          => 'required|integer|min:0',
            'min_stock'      => 'required|integer|min:0',
            'price_per_unit' => 'nullable|numeric|min:0',
            'supplier'       => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('stock', 'public');
        }

        $item = StockItem::create($validated);

        if ($item->stock > 0) {
            StockLog::create([
                'stock_item_id' => $item->id,
                'user_id'       => auth()->id(),
                'type'          => 'in',
                'quantity'      => $item->stock,
                'stock_before'  => 0,
                'stock_after'   => $item->stock,
                'note'          => 'Stok awal saat barang ditambahkan.',
            ]);
        }

        return redirect()->route('admin.stock.index')
            ->with('success', "Barang '{$item->name}' berhasil ditambahkan ke stok!");
    }

    public function show(StockItem $stock)
    {
        $logs = $stock->logs()->with('user')->latest()->paginate(15);
        return view('admin.stock.show', compact('stock', 'logs'));
    }

    public function edit(StockItem $stock)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.stock.edit', compact('stock', 'categories'));
    }

    public function update(Request $request, StockItem $stock)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'unit'           => 'required|string|max:50',
            'min_stock'      => 'required|integer|min:0',
            'price_per_unit' => 'nullable|numeric|min:0',
            'supplier'       => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($stock->photo) Storage::disk('public')->delete($stock->photo);
            $validated['photo'] = $request->file('photo')->store('stock', 'public');
        }

        $stock->update($validated);

        return redirect()->route('admin.stock.index')
            ->with('success', "Data barang '{$stock->name}' berhasil diperbarui!");
    }

    public function destroy(StockItem $stock)
    {
        if ($stock->photo) Storage::disk('public')->delete($stock->photo);
        $name = $stock->name;
        $stock->delete();

        return redirect()->route('admin.stock.index')
            ->with('success', "Barang '{$name}' berhasil dihapus dari stok!");
    }

    public function adjust(Request $request, StockItem $stock)
    {
        $request->validate([
            'type'     => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'note'     => 'nullable|string|max:255',
        ]);

        $type   = $request->type;
        $qty    = (int) $request->quantity;
        $before = $stock->stock;

        if ($type === 'out' && $qty > $before) {
            return back()->with('error', "Stok keluar ($qty) melebihi stok tersedia ($before)!");
        }

        $after = $type === 'in' ? $before + $qty : $before - $qty;
        $stock->update(['stock' => $after]);

        StockLog::create([
            'stock_item_id' => $stock->id,
            'user_id'       => auth()->id(),
            'type'          => $type,
            'quantity'      => $qty,
            'stock_before'  => $before,
            'stock_after'   => $after,
            'note'          => $request->note,
        ]);

        $label = $type === 'in' ? 'ditambahkan ke' : 'dikurangkan dari';
        return back()->with('success', "$qty {$stock->unit} berhasil $label stok {$stock->name}.");
    }
}
