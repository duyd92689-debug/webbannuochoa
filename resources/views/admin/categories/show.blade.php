@extends('layouts.admin')

@section('title', 'Chi tiết danh mục')
@section('page_title', 'Chi tiết danh mục: ' . $category->name)

@section('content')
<div class="admin-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="font-weight-bold mb-1" style="color: var(--text-dark);">Thông tin danh mục</h5>
            <span class="text-muted" style="font-size: 0.88rem;">Mã định danh: #{{ str_pad((string) $category->id, 3, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div>
            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary btn-sm mr-2 font-weight-bold">
                <i class="fa-regular fa-pen-to-square mr-1"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="p-4 rounded mb-4" style="background: linear-gradient(135deg, var(--pink-soft), #fff); border: 1px solid var(--border); border-radius: 12px;">
        <div class="d-flex align-items-center gap-3" style="gap: 16px;">
            <div style="width: 52px; height: 52px; border-radius: 12px; background: linear-gradient(135deg, var(--pink-dark), var(--pink)); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 700; box-shadow: 0 4px 12px rgba(232,114,138,.3);">
                {{ mb_strtoupper(mb_substr($category->name, 0, 1)) }}
            </div>
            <div>
                <h3 class="font-weight-bold text-primary mb-1">{{ $category->name }}</h3>
                <p class="text-muted mb-0" style="font-size: 0.88rem;">
                    <i class="fa-regular fa-calendar mr-1"></i> Ngày tạo: {{ $category->created_at?->format('d/m/Y H:i:s') ?? 'N/A' }} 
                    <span class="mx-2">•</span>
                    <i class="fa-solid fa-boxes-stacked mr-1"></i> Tổng số: <strong>{{ $category->perfumes->count() }}</strong> sản phẩm
                </p>
            </div>
        </div>
    </div>

    <h6 class="font-weight-bold mb-3" style="color: var(--text-dark); display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-spray-can-sparkles" style="color: var(--pink-dark);"></i>
        Sản phẩm thuộc danh mục này
    </h6>
    <div class="table-responsive">
        <table class="table table-hover table-admin">
            <thead>
                <tr>
                    <th width="70">ID</th>
                    <th>Tên sản phẩm</th>
                    <th>Thương hiệu</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th width="120" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($category->perfumes as $product)
                    <tr>
                        <td class="font-weight-bold text-muted">#{{ $product->id }}</td>
                        <td><strong style="color: var(--text-dark);">{{ $product->name }}</strong></td>
                        <td><span class="badge" style="background: var(--pink-soft); color: var(--text-mid); border: 1px solid var(--border);">{{ $product->brand }}</span></td>
                        <td><strong style="color: var(--pink-dark); font-size: 0.95rem;">{{ number_format($product->price, 0, ',', '.') }}₫</strong></td>
                        <td>
                            <div class="d-flex flex-column" style="font-size: 0.8rem; gap: 2px;">
                                <span>10ml: <strong>{{ $product->stock_10ml }}</strong></span>
                                <span>50ml: <strong>{{ $product->stock_50ml }}</strong></span>
                                <span>100ml: <strong>{{ $product->stock }}</strong></span>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-outline-info btn-sm">
                                <i class="fa-regular fa-eye"></i> Xem
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Chưa có sản phẩm nào thuộc danh mục này.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
