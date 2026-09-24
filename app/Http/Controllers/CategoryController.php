<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('perfumes')->latest()->get();

        return view(request()->routeIs('admin.*') ? 'admin.categories.index' : 'categories.index', compact('categories'));
    }

    public function create()
    {
        return view(request()->routeIs('admin.*') ? 'admin.categories.create' : 'categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create($validated);

        $targetRoute = $request->routeIs('admin.*') ? 'admin.categories.index' : 'categories.index';

        return redirect()->route($targetRoute)
            ->with('success', 'Đã thêm danh mục mới thành công.');
    }

    public function show(Category $category)
    {
        $category->load(['perfumes' => fn ($query) => $query->latest()]);

        return view(request()->routeIs('admin.*') ? 'admin.categories.show' : 'categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view(request()->routeIs('admin.*') ? 'admin.categories.edit' : 'categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($validated);

        $targetRoute = $request->routeIs('admin.*') ? 'admin.categories.index' : 'categories.index';

        return redirect()->route($targetRoute)
            ->with('success', 'Đã cập nhật danh mục thành công.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        $targetRoute = request()->routeIs('admin.*') ? 'admin.categories.index' : 'categories.index';

        return redirect()->route($targetRoute)
            ->with('success', 'Đã xóa danh mục thành công.');
    }
}
