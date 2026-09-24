<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

// Tất cả route admin đều yêu cầu middleware auth + admin
Route::middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Quản lý sản phẩm
    Route::resource('/products', ProductController::class, ['as' => 'admin']);

    // Quản lý danh mục
    Route::resource('/categories', CategoryController::class, ['as' => 'admin']);
});
