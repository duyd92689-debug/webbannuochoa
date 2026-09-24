@extends('layouts.admin')

@section('title', 'Chỉnh sửa Video')
@section('page_title', 'Chỉnh sửa Video Review & Trải Nghiệm')

@section('content')
<div class="admin-video-form-page">
    <div class="admin-card" style="max-width: 900px; margin: 0 auto;">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h5 class="font-weight-bold mb-1" style="color: #0f172a;">Chỉnh sửa Video: {{ $video->title }}</h5>
                <p class="text-muted mb-0 small">Cập nhật thông tin, liên kết nước hoa hoặc thay đổi đường dẫn video</p>
            </div>
            <a href="{{ route('admin.videos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong class="d-block mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Vui lòng kiểm tra các thông tin sau:</strong>
                <ul class="mb-0 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- Video Player Preview --}}
        @if($video->is_youtube)
            <div class="mb-4 p-3 bg-light rounded border text-center">
                <div class="small font-weight-bold text-muted mb-2"><i class="fa-solid fa-eye mr-1"></i> Xem trước Video YouTube:</div>
                <div style="max-width: 480px; margin: 0 auto; aspect-ratio: 16/9; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 14px rgba(0,0,0,0.15);">
                    <iframe src="{{ $video->embed_url }}" style="width:100%; height:100%; border:0;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.videos.update', $video) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group mb-3">
                <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                    Tiêu đề Video <span class="text-danger">*</span>
                </label>
                <input type="text" name="title" class="form-control" placeholder="Ví dụ: Miss Dior: Hương hoa hồng ngọt dịu đầu mùa" value="{{ old('title', $video->title) }}" required>
            </div>

            <div class="form-group mb-3">
                <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                    Đường dẫn Video (YouTube / Shorts / TikTok / MP4) <span class="text-danger">*</span>
                </label>
                <input type="text" name="video_url" class="form-control" placeholder="Dán link YouTube (vd: https://www.youtube.com/watch?v=... hoặc Shorts https://www.youtube.com/shorts/...)" value="{{ old('video_url', $video->video_url) }}" required>
                <small class="form-text text-muted">
                    Hỗ trợ: Link video YouTube chuẩn, link YouTube Shorts (video dọc 9:16), link chia sẻ <code>youtu.be</code> hoặc link file video <code>.mp4</code>.
                </small>
            </div>

            <div class="form-row">
                <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                        Gắn với Nước hoa (Tùy chọn)
                    </label>
                    <select name="perfume_id" class="form-control">
                        <option value="">-- Không gắn (Video thương hiệu chung) --</option>
                        @foreach($perfumes as $p)
                            <option value="{{ $p->id }}" {{ old('perfume_id', $video->perfume_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->brand }} - {{ number_format($p->price) }}đ)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                        Vị trí hiển thị <span class="text-danger">*</span>
                    </label>
                    <select name="placement" class="form-control" required>
                        <option value="all" {{ old('placement', $video->placement) === 'all' ? 'selected' : '' }}>Toàn sàn (Trang chủ & Trang chi tiết SP)</option>
                        <option value="home" {{ old('placement', $video->placement) === 'home' ? 'selected' : '' }}>Chỉ hiển thị tại Fragrance Shorts (Trang chủ)</option>
                        <option value="product" {{ old('placement', $video->placement) === 'product' ? 'selected' : '' }}>Chỉ hiển thị tại Trang chi tiết sản phẩm</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-4 form-group mb-3">
                    <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                        Thời lượng video
                    </label>
                    <input type="text" name="duration" class="form-control" placeholder="0:45" value="{{ old('duration', $video->duration) }}">
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                        Lượt xem hiển thị
                    </label>
                    <input type="number" name="views_count" class="form-control" placeholder="12400" value="{{ old('views_count', $video->views_count) }}">
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                        Thứ tự ưu tiên
                    </label>
                    <input type="number" name="sort_order" class="form-control" placeholder="0" value="{{ old('sort_order', $video->sort_order) }}">
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                        Đổi Ảnh bìa Thumbnail (Tải tệp)
                    </label>
                    <input type="file" name="thumbnail_file" class="form-control-file border p-2 rounded w-100 bg-light" accept="image/*">
                    @if($video->thumbnail_url)
                        <div class="mt-2 d-flex align-items-center gap-2">
                            <img src="{{ $video->thumbnail_src }}" alt="Thumb" style="width: 45px; height: 45px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
                            <span class="small text-muted font-italic">Ảnh bìa hiện tại</span>
                        </div>
                    @endif
                </div>

                <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                        Hoặc đường dẫn ảnh bìa (URL)
                    </label>
                    <input type="text" name="thumbnail_url" class="form-control" placeholder="images/products/... hoặc https://..." value="{{ old('thumbnail_url', $video->thumbnail_url) }}">
                </div>
            </div>

            <div class="form-group mb-3">
                <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                    Mô tả ngắn / Đánh giá mùi hương
                </label>
                <textarea name="description" rows="3" class="form-control" placeholder="Ví dụ: Cảm nhận nốt hương hoa mẫu đơn dịu dàng...">{{ old('description', $video->description) }}</textarea>
            </div>

            <div class="form-group mb-4">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $video->is_active) ? 'checked' : '' }}>
                    <label class="custom-control-label font-weight-bold text-dark" for="is_active">
                        Kích hoạt hiển thị ngay trên website
                    </label>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="{{ route('admin.videos.index') }}" class="btn btn-outline-secondary px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary px-4 font-weight-bold">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Lưu Thay Đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
