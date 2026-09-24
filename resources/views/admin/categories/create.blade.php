@extends('layouts.admin')

@section('title', 'Thêm danh mục mới')
@section('page_title', 'Tạo danh mục sản phẩm mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="font-weight-bold mb-0" style="color: #0f172a;">Thông tin danh mục</h5>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại danh sách
                </a>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name" class="font-weight-bold" style="font-size:0.9rem;">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           placeholder="Ví dụ: Nước hoa Niche, Nước hoa Nam..." 
                           required 
                           autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4" style="gap: 10px;">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-light px-4">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-check mr-1"></i> Lưu danh mục
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
