<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer(['layouts.store', 'home'], function ($view) {
            try {
                if (!isset($view->categories) && \Illuminate\Support\Facades\Schema::hasTable('categories') && \Illuminate\Support\Facades\Schema::hasTable('perfumes')) {
                    $categories = \App\Models\Category::query()
                        ->withCount(['perfumes' => fn ($query) => $query->where('is_active', true)])
                        ->orderBy('id')
                        ->take(6)
                        ->get();
                    $view->with('categories', $categories);
                }
                if (!isset($view->genderCounts) && \Illuminate\Support\Facades\Schema::hasTable('perfumes')) {
                    $genderCounts = \App\Models\Perfume::query()
                        ->where('is_active', true)
                        ->selectRaw('gender, count(*) as total')
                        ->groupBy('gender')
                        ->pluck('total', 'gender');
                    $view->with('genderCounts', $genderCounts);
                    $view->with('totalPerfumes', $genderCounts->sum());
                }
            } catch (\Throwable $e) {
                // Ignore during migrations or initial setup
            }
        });
    }
}
