<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Perfume;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $perfumes = Perfume::query()
            ->where('is_active', true)
            ->when($request->filled('search'), function ($query) use ($request) {
                $keyword = trim((string) $request->input('search'));
                $query->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%")
                        ->orWhere('brand', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('gender'), fn ($query) => $query->where('gender', $request->input('gender')))
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->integer('category')))
            ->when($request->input('sort') === 'sale', fn ($query) => $query->whereNotNull('sale_price'))
            ->when($request->input('sort') === 'price_asc', fn ($query) => $query->orderByRaw('COALESCE(sale_price, price) asc'))
            ->when($request->input('sort') === 'price_desc', fn ($query) => $query->orderByRaw('COALESCE(sale_price, price) desc'))
            ->latest()
            ->take(12)
            ->get();

        $categories = Category::query()
            ->withCount(['perfumes' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('id')
            ->take(6)
            ->get();

        $genderCounts = Perfume::query()
            ->where('is_active', true)
            ->selectRaw('gender, count(*) as total')
            ->groupBy('gender')
            ->pluck('total', 'gender');

        $totalPerfumes = $genderCounts->sum();

        return view('home', compact('perfumes', 'categories', 'genderCounts', 'totalPerfumes'));
    }
}
