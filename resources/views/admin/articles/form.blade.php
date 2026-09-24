@extends('layouts.admin')

@section('title', $article->exists ? 'Sửa bài viết' : 'Viết bài mới')
@section('page_title')
    <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary btn-sm mr-3" style="border-radius: 6px;">
        <i class="fa-solid fa-arrow-left"></i> Quay lại
    </a>
    {{ $article->exists ? 'Chỉnh sửa bài viết' : 'Soạn bài viết mới' }}
@endsection

@section('content')
<div class="admin-article-form-page">
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation mr-2"></i> {{ $errors->first() }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="admin-card">
        <h5 class="font-weight-bold mb-3" style="color: #0f172a;">
            <i class="fa-solid fa-feather text-primary mr-2"></i> Thông tin bài viết cẩm nang
        </h5>

        <form method="POST" action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}">
            @csrf
            @if($article->exists)
                @method('PUT')
            @endif

            <div class="form-group mb-3">
                <label class="form-label font-weight-bold small text-muted text-uppercase">
                    Tiêu đề bài viết <span class="text-danger">*</span>
                </label>
                <input class="form-control" name="title" maxlength="200" value="{{ old('title', $article->title) }}" placeholder="VD: Bí quyết lưu hương nước hoa suốt ngày dài..." required>
            </div>

            <div class="form-group mb-3">
                <label class="form-label font-weight-bold small text-muted text-uppercase">
                    Tóm tắt ngắn (Excerpt) <span class="text-danger">*</span>
                </label>
                <textarea class="form-control" name="excerpt" rows="3" maxlength="500" placeholder="Đoạn văn ngắn giới thiệu nội dung hiển thị ở danh sách bài..." required>{{ old('excerpt', $article->excerpt) }}</textarea>
            </div>

            <div class="form-group mb-3">
                <label class="form-label font-weight-bold small text-muted text-uppercase">
                    Nội dung chi tiết <span class="text-danger">*</span>
                </label>
                <textarea class="form-control" name="body" rows="12" placeholder="Nội dung bài viết. Mỗi đoạn cách nhau bằng một dòng trống." required>{{ old('body', $article->body) }}</textarea>
                <small class="text-muted mt-1 d-block">Mẹo: Mỗi đoạn văn cách nhau bằng một dòng trống để hiển thị đẹp trên trang web.</small>
            </div>

            <div class="form-group mb-3">
                <label class="form-label font-weight-bold small text-muted text-uppercase">
                    Đường dẫn ảnh đại diện
                </label>
                <input class="form-control" name="image_url" value="{{ old('image_url', $article->image_url) }}" placeholder="VD: images/products/ten-anh.jpg">
                <small class="text-muted mt-1 d-block">Nhập đường dẫn tương đối trong thư mục public của dự án.</small>
            </div>

            <div class="form-group mb-4">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="isPublishedCheck" name="is_published" value="1" @checked(old('is_published', $article->is_published))>
                    <label class="custom-control-label font-weight-bold text-dark" for="isPublishedCheck" style="cursor: pointer;">
                        Xuất bản ngay cho khách hàng xem
                    </label>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                <a href="{{ route('admin.articles.index') }}" class="btn btn-light px-3">Hủy bỏ</a>
                <button class="btn btn-primary px-4 py-2 font-weight-bold" type="submit" style="border-radius: 8px;">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> {{ $article->exists ? 'Lưu thay đổi' : 'Tạo bài viết' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
