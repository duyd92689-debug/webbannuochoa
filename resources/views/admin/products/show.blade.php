@extends('layouts.admin')

@section('title', 'Chi tiết sản phẩm')
@section('page_title', 'Chi tiết sản phẩm: ' . $product->name)

@section('content')
<div class="admin-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="font-weight-bold mb-1" style="color: #0f172a;">Thông tin chi tiết</h5>
            <span class="text-muted">Mã sản phẩm: #{{ $product->id }}</span>
        </div>
        <div>
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning btn-sm mr-2 font-weight-bold">
                <i class="fa-regular fa-pen-to-square mr-1"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 text-center mb-4">
            @if($product->image_url)
                <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" class="img-fluid rounded border shadow-sm" style="max-height: 320px; object-fit: contain; width: 100%;">
            @else
                <div class="border rounded bg-light d-flex align-items-center justify-content-center text-muted" style="height: 250px;">
                    <i class="fa-solid fa-image" style="font-size:3rem;"></i>
                </div>
            @endif
        </div>

        <div class="col-md-8">
            <h3 class="font-weight-bold text-primary mb-2">{{ $product->name }}</h3>
            <p class="text-muted mb-3" style="font-size:1.05rem;">Thương hiệu: <strong>{{ $product->brand }}</strong></p>

            <div class="row mb-3">
                <div class="col-6 mb-2">
                    <div class="text-muted small text-uppercase">Danh mục</div>
                    <strong>{{ optional($product->category)->name ?? 'Chưa phân loại' }}</strong>
                </div>
                <div class="col-6 mb-2">
                    <div class="text-muted small text-uppercase">Giới tính</div>
                    <span class="badge badge-info">{{ ucfirst($product->gender) }}</span>
                </div>
                <div class="col-6 mb-2">
                    <div class="text-muted small text-uppercase">Giá bán</div>
                    <span class="text-primary font-weight-bold" style="font-size: 1.2rem;">
                        {{ number_format($product->price, 0, ',', '.') }}₫
                    </span>
                    @if($product->sale_price)
                        <small class="text-muted text-decoration-line-through ml-2">{{ number_format($product->sale_price, 0, ',', '.') }}₫</small>
                    @endif
                </div>
                <div class="col-12 my-3">
                    <div class="text-muted small text-uppercase font-weight-bold mb-2">Tồn kho theo từng dung tích:</div>
                    <div class="row">
                        <div class="col-4">
                            <div class="p-2 border rounded text-center bg-light">
                                <span class="text-muted d-block small"><i class="fa-solid fa-vial mr-1 text-info"></i>Chiết 10ml</span>
                                <strong class="h5 {{ $product->stock_10ml > 5 ? 'text-success' : 'text-danger' }}">{{ $product->stock_10ml }}</strong>
                                <small class="text-muted">chai</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded text-center bg-light">
                                <span class="text-muted d-block small"><i class="fa-solid fa-wine-bottle mr-1 text-primary"></i>Vừa 50ml</span>
                                <strong class="h5 {{ $product->stock_50ml > 5 ? 'text-success' : 'text-danger' }}">{{ $product->stock_50ml }}</strong>
                                <small class="text-muted">chai</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded text-center bg-light">
                                <span class="text-muted d-block small"><i class="fa-solid fa-box mr-1 text-success"></i>Full 100ml</span>
                                <strong class="h5 {{ $product->stock > 5 ? 'text-success' : 'text-danger' }}">{{ $product->stock }}</strong>
                                <small class="text-muted">chai</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4 mb-2">
                    <div class="text-muted small text-uppercase">Dung tích / Nồng độ</div>
                    <strong>{{ $product->volume_ml }}ml · {{ $product->concentration ?? 'EDP' }}</strong>
                </div>
                <div class="col-4 mb-2">
                    <div class="text-muted small text-uppercase">KL tính phí GHN</div>
                    <strong class="text-primary"><i class="fa-solid fa-weight-hanging mr-1"></i>{{ $product->weight ?? 200 }}g</strong>
                </div>
                <div class="col-4 mb-2">
                    <div class="text-muted small text-uppercase">Trạng thái</div>
                    @if($product->is_active)
                        <span class="badge badge-success">Đang bán</span>
                    @else
                        <span class="badge badge-secondary">Đã ẩn</span>
                    @endif
                </div>
            </div>

            <hr>

            <h6 class="font-weight-bold text-dark">Mô tả sản phẩm:</h6>
            <p class="text-muted" style="line-height: 1.6;">{{ $product->description ?: 'Chưa có mô tả cho sản phẩm này.' }}</p>
        </div>
    </div>
</div>
@endsection
