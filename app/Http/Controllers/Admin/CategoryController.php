<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\StockItem;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('stockItems')
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            )
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $totalCategories = Category::count();
        $totalItems      = StockItem::count();

        return view('admin.categories.index', compact('categories', 'totalCategories', 'totalItems'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string',
            'color'       => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon'        => 'required|string|max:50',
        ]);

        Category::create($validated);

        // Jika request dari AJAX (modal quick-add)
        if ($request->expectsJson()) {
            $categories = Category::orderBy('name')->get(['id', 'name', 'color', 'icon']);
            return response()->json([
                'success'    => true,
                'message'    => "Kategori '{$validated['name']}' berhasil ditambahkan!",
                'categories' => $categories,
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$validated['name']}' berhasil ditambahkan!");
    }

    public function edit(Category $category)
    {
        $category->loadCount('stockItems');
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'color'       => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon'        => 'required|string|max:50',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil diperbarui!");
    }

    public function destroy(Category $category)
    {
        $count = $category->stockItems()->count();
        if ($count > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Kategori '{$category->name}' tidak bisa dihapus karena masih digunakan oleh {$count} barang.");
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$name}' berhasil dihapus!");
    }
}
