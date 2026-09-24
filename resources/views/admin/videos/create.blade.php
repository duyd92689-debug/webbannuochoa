@extends('layouts.admin')

@section('title', 'Thêm Video Mới')
@section('page_title', 'Thêm Video Review & Trải Nghiệm')

@section('content')
<div class="admin-video-form-page">
    <div class="admin-card" style="max-width: 900px; margin: 0 auto;">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h5 class="font-weight-bold mb-1" style="color: #0f172a;">Tạo Video Mới</h5>
                <p class="text-muted mb-0 small">Thêm video review từ YouTube, YouTube Shorts, TikTok hoặc file video MP4</p>
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

        <form method="POST" action="{{ route('admin.videos.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group mb-3">
                <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                    Tiêu đề Video <span class="text-danger">*</span>
                </label>
                <input type="text" name="title" class="form-control" placeholder="Ví dụ: Miss Dior: Hương hoa hồng ngọt dịu đầu mùa" value="{{ old('title') }}" required>
            </div>

            <div class="form-group mb-3">
                <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                    Đường dẫn Video (YouTube / Shorts / TikTok / MP4) <span class="text-danger">*</span>
                </label>
                <input type="text" name="video_url" id="input_video_url" class="form-control" placeholder="Dán link YouTube (vd: https://www.youtube.com/watch?v=... hoặc Shorts https://www.youtube.com/shorts/...)" value="{{ old('video_url') }}" required>
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
                            <option value="{{ $p->id }}" {{ old('perfume_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->brand }} - {{ number_format($p->price) }}đ)
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Khi gắn vào sản phẩm, video sẽ tự động xuất hiện ở trang chi tiết sản phẩm đó.</small>
                </div>

                <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                        Vị trí hiển thị <span class="text-danger">*</span>
                    </label>
                    <select name="placement" class="form-control" required>
                        <option value="all" {{ old('placement', 'all') === 'all' ? 'selected' : '' }}>Toàn sàn (Trang chủ & Trang chi tiết SP)</option>
                        <option value="home" {{ old('placement') === 'home' ? 'selected' : '' }}>Chỉ hiển thị tại Fragrance Shorts (Trang chủ)</option>
                        <option value="product" {{ old('placement') === 'product' ? 'selected' : '' }}>Chỉ hiển thị tại Trang chi tiết sản phẩm</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-4 form-group mb-3">
                    <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                        Thời lượng video
                    </label>
                    <input type="text" name="duration" class="form-control" placeholder="0:45" value="{{ old('duration', '0:45') }}">
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                        Lượt xem hiển thị
                    </label>
                    <input type="number" name="views_count" class="form-control" placeholder="12400" value="{{ old('views_count', 1200) }}">
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                        Thứ tự ưu tiên
                    </label>
                    <input type="number" name="sort_order" class="form-control" placeholder="0" value="{{ old('sort_order', 0) }}">
                    <small class="form-text text-muted">Số nhỏ hơn sẽ hiển thị trước.</small>
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                        Tải lên Ảnh bìa Thumbnail
                    </label>
                    <input type="file" name="thumbnail_file" class="form-control-file border p-2 rounded w-100 bg-light" accept="image/*">
                    <small class="form-text text-muted">Hỗ trợ JPG, PNG, WEBP (tối đa 5MB).</small>
                </div>

                <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                        Hoặc đường dẫn ảnh bìa (URL)
                    </label>
                    <input type="text" name="thumbnail_url" class="form-control" placeholder="images/products/... hoặc https://..." value="{{ old('thumbnail_url') }}">
                    <small class="form-text text-muted">Nếu để trống, hệ thống sẽ lấy ảnh của chai nước hoa được chọn.</small>
                </div>
            </div>

            <div class="form-group mb-3">
                <label class="font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                    Mô tả ngắn / Đánh giá mùi hương
                </label>
                <textarea name="description" rows="3" class="form-control" placeholder="Ví dụ: Cảm nhận nốt hương hoa mẫu đơn dịu dàng và hoa hồng Grasse kiêu sa sau 4 giờ...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group mb-4">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="custom-control-label font-weight-bold text-dark" for="is_active">
                        Kích hoạt hiển thị ngay trên website
                    </label>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="{{ route('admin.videos.index') }}" class="btn btn-outline-secondary px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary px-4 font-weight-bold">
                    <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Lưu Video
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
