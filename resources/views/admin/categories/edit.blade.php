@extends('layouts.admin')

@section('title', 'Chỉnh sửa danh mục')
@section('page_title', 'Cập nhật danh mục: ' . $category->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="font-weight-bold mb-0" style="color: #0f172a;">Chỉnh sửa thông tin</h5>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại danh sách
                </a>
            </div>

            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="font-weight-bold" style="font-size:0.9rem;">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $category->name) }}" 
                           required 
                           autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4" style="gap: 10px;">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-light px-4">Hủy bỏ</a>
                    <button type="submit" class="btn btn-warning px-4 font-weight-bold">
                        <i class="fa-solid fa-save mr-1"></i> Cập nhật danh mục
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
