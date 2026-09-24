@extends('layouts.store')

@section('title', 'Ha Thu Perfume · Hương thơm của riêng bạn')

@section('content')
@php
    $isFiltered = request()->hasAny(['search', 'gender', 'category', 'sort']);
    $selectedCategory = request('category') ? $categories->firstWhere('id', (int) request('category')) : null;
    $collectionTitle = request()->filled('search') ? 'Mùi hương bạn đang tìm'
        : ($selectedCategory?->name ?? match (request('gender')) {
            'nam' => 'Nước hoa dành cho chàng',
            'nu' => 'Nước hoa dành cho nàng',
            'unisex' => 'Hương thơm không giới hạn',
            default => 'Nước hoa nổi bật',
        });
@endphp

@if(!$isFiltered)
<section class="ht-hero">
    <div class="store-container ht-hero-grid">
        <div class="ht-hero-copy">
            <span class="ht-eyebrow"><span class="ht-small-line"></span> THẾ GIỚI HƯƠNG THƠM CỦA HA THU</span>
            <h1>Một chút hương,<br>một chút <em>thương.</em></h1>
            <p>Để mỗi ngày đều có một dấu ấn thật riêng.<br>Khám phá những mùi hương được yêu, dành cho phiên bản đẹp nhất của bạn.</p>
            <div class="ht-hero-actions">
                <a class="ht-button" href="#san-pham">Tìm hương của bạn @include('partials.icon', ['name' => 'arrow', 'size' => 18])</a>
                <a class="ht-text-link" href="#bo-suu-tap">Xem bộ sưu tập</a>
            </div>
            <div class="ht-hero-note">@include('partials.icon', ['name' => 'flower', 'size' => 27])<span>Không chỉ là nước hoa.<br><strong>Là cảm xúc bạn mang theo.</strong></span></div>
        </div>
        <div class="ht-hero-visual">
            <div class="ht-hero-photo">
                <img src="{{ asset('images/products/miss-dior-blooming.jpg') }}" alt="Chai Miss Dior hồng dịu giữa hoa hồng và lụa mềm" width="720" height="720" fetchpriority="high">
                <a class="ht-photo-label" href="{{ route('home', ['search' => 'Dior']) }}#san-pham"><span>HƯƠNG HOA ĐƯỢC YÊU<STRONG>Miss Dior Blooming Bouquet</STRONG></span>@include('partials.icon', ['name' => 'arrow'])</a>
            </div>
            <span class="ht-hero-side-note">A LITTLE SCENT, A LITTLE LOVE.</span>
            <div class="ht-photo-caption"><span>THE HA THU EDIT</span><span>Nhẹ nhàng. Tinh tế. Rất riêng.</span></div>
        </div>
    </div>
</section>
<div class="store-container ht-benefits" aria-label="Cam kết của cửa hàng">
    <div>@include('partials.icon', ['name' => 'shield', 'size' => 25])<span><strong>Nước hoa chính hãng</strong><small>An tâm với từng lựa chọn</small></span></div>
    <div>@include('partials.icon', ['name' => 'gift', 'size' => 25])<span><strong>Gói trọn yêu thương</strong><small>Chăm chút từng món quà</small></span></div>
    <div>@include('partials.icon', ['name' => 'truck', 'size' => 25])<span><strong>Giao hàng tận nơi</strong><small>Đóng gói an toàn, cẩn thận</small></span></div>
    <div>@include('partials.icon', ['name' => 'heart', 'size' => 25])<span><strong>Tư vấn tận tâm</strong><small>Cùng bạn chọn hương phù hợp</small></span></div>
</div>
@endif

<section class="store-container ht-products-section" id="san-pham">
    <div class="ht-section-heading">
        <div><span class="ht-eyebrow">{{ $isFiltered ? 'KHÁM PHÁ CÙNG HA THU' : 'NHỮNG MÙI HƯƠNG ĐÁNG THỬ' }}</span><h2>{{ $collectionTitle }}</h2>
            @if(request()->filled('search'))<p>Kết quả cho “{{ request('search') }}” · {{ $perfumes->count() }} sản phẩm hiển thị</p>
            @else<p>Mỗi mùi hương, một cách để thể hiện chính mình.</p>@endif
        </div>
        @if($isFiltered)<a class="ht-text-link" href="{{ route('home') }}#san-pham">Xem tất cả @include('partials.icon', ['name' => 'arrow', 'size' => 18])</a>
        @else<span class="ht-section-index">01 / THE COLLECTION</span>@endif
    </div>
    <nav class="ht-product-tabs" aria-label="Sắp xếp sản phẩm">
        <a @class(['active' => !request('sort')]) href="{{ route('home', request()->except('sort')) }}#san-pham">Mới nhất</a>
        <a @class(['active' => request('sort') === 'sale']) href="{{ route('home', array_merge(request()->except('sort'), ['sort' => 'sale'])) }}#san-pham">Đang ưu đãi</a>
        <a @class(['active' => request('sort') === 'price_asc']) href="{{ route('home', array_merge(request()->except('sort'), ['sort' => 'price_asc'])) }}#san-pham">Giá tăng dần</a>
        <a @class(['active' => request('sort') === 'price_desc']) href="{{ route('home', array_merge(request()->except('sort'), ['sort' => 'price_desc'])) }}#san-pham">Giá giảm dần</a>
    </nav>
    <div class="store-products">
        @forelse($perfumes as $perfume)
        <article class="store-product-card">
            <a class="store-product-image" href="{{ route('perfumes.show', $perfume) }}" aria-label="Xem {{ $perfume->name }}">
                @if($perfume->image_src)
                    <img src="{{ $perfume->image_src }}" alt="{{ $perfume->name }}" width="600" height="600" loading="lazy" decoding="async">
                @else
                    <div class="ht-product-placeholder">@include('partials.icon', ['name' => 'flower', 'size' => 56])<span>{{ $perfume->brand }}</span></div>
                @endif
                @if($perfume->sale_price !== null && $perfume->sale_price < $perfume->price && $perfume->price > 0)
                    <small>−{{ round((1 - $perfume->sale_price / $perfume->price) * 100) }}%</small>
                @endif
                <span class="ht-product-open" aria-hidden="true">@include('partials.icon', ['name' => 'arrow', 'size' => 18])</span>
            </a>
            <div class="store-product-info">
                <span>{{ $perfume->brand }}</span>
                <h3><a href="{{ route('perfumes.show', $perfume) }}">{{ $perfume->name }}</a></h3>
                <p>{{ $perfume->volume_ml }} ml <span>·</span> {{ $perfume->concentration ?: 'Nước hoa' }}</p>
                <div class="ht-product-bottom">
                    <div class="ht-product-price"><strong>{{ number_format((float) ($perfume->sale_price ?? $perfume->price), 0, ',', '.') }}₫</strong>@if($perfume->sale_price !== null && $perfume->sale_price < $perfume->price)<del>{{ number_format((float) $perfume->price, 0, ',', '.') }}₫</del>@endif</div>
                    <a class="ht-product-detail" href="{{ route('perfumes.show', $perfume) }}" aria-label="Chọn dung tích {{ $perfume->name }}">@include('partials.icon', ['name' => 'bag', 'size' => 18])</a>
                </div>
            </div>
        </article>
        @empty
        <div class="store-empty">@include('partials.icon', ['name' => 'search', 'size' => 42])<h3>Chưa tìm thấy mùi hương phù hợp</h3><p>Thử một tên nước hoa, thương hiệu hoặc danh mục khác nhé.</p><a class="ht-button" href="{{ route('home') }}#san-pham">Khám phá tất cả nước hoa</a></div>
        @endforelse
    </div>
</section>

@if(!$isFiltered)
<section class="ht-collections-section" id="bo-suu-tap">
    <div class="store-container">
        <div class="ht-section-heading"><div><span class="ht-eyebrow">MÙI HƯƠNG NÓI LÊN BẠN</span><h2>Một thế giới. Nhiều sắc hương.</h2></div><span class="ht-section-index">02 / FIND YOUR SCENT</span></div>
        <div class="ht-collections">
            <a class="ht-collection-card" href="{{ route('home', ['gender' => 'nu']) }}#san-pham">
                <img src="{{ asset('images/products/delina-exclusif.jpg') }}" alt="Nước hoa Delina sắc hồng bên những cánh hoa" loading="lazy" width="600" height="600">
                <div><span>DỊU DÀNG & CUỐN HÚT</span><h3>Dành cho nàng</h3><p>Để mỗi khoảnh khắc thêm rạng rỡ.</p><span class="ht-collection-arrow">@include('partials.icon', ['name' => 'arrow'])</span></div>
            </a>
            <a class="ht-collection-card" href="{{ route('home', ['gender' => 'nam']) }}#san-pham">
                <img src="{{ asset('images/products/black-gold.jpg') }}" alt="Nước hoa sắc đen với những chi tiết vàng ấm" loading="lazy" width="600" height="600">
                <div><span>LỊCH LÃM & BẢN LĨNH</span><h3>Dành cho chàng</h3><p>Một dấu ấn khó có thể nhầm lẫn.</p><span class="ht-collection-arrow">@include('partials.icon', ['name' => 'arrow'])</span></div>
            </a>
            <a class="ht-collection-card" href="{{ route('home', ['gender' => 'unisex']) }}#san-pham">
                <img src="{{ asset('images/products/tom-ford-rose-prick.jpg') }}" alt="Chai Tom Ford Rose Prick hồng thanh lịch" loading="lazy" width="600" height="600">
                <div><span>TỰ DO & KHÁC BIỆT</span><h3>Không giới hạn</h3><p>Hương thơm dành cho mọi cá tính.</p><span class="ht-collection-arrow">@include('partials.icon', ['name' => 'arrow'])</span></div>
            </a>
        </div>
    </div>
</section>
<section class="store-container ht-brands-section" aria-label="Khám phá thương hiệu">
    <span class="ht-eyebrow">NHỮNG TÊN TUỔI LÀM NÊN CẢM XÚC</span>
    <div class="ht-brands">
        @foreach(['DIOR', 'CHANEL', 'TOM FORD', 'NARCISO', 'ARMANI'] as $brand)
            <a href="{{ route('home', ['search' => $brand]) }}#san-pham">{{ $brand }}</a>
        @endforeach
    </div>
</section>
<section class="store-container ht-story">
    <div class="ht-story-image"><img src="{{ asset('images/perfume-hero.jpg') }}" alt="Bộ sưu tập nước hoa được đặt bên những đóa hoa trắng" loading="lazy" width="900" height="600"></div>
    <div class="ht-story-copy"><span class="ht-eyebrow">TỪ HA THU, VỚI YÊU THƯƠNG</span><h2>Một món quà nhỏ.<br>Một cảm xúc <em>thật lâu.</em></h2><p>Tặng người thương, hay dành tặng chính mình. Một chai nước hoa là lời nhắn dịu dàng, gói ghém những điều đôi khi khó nói thành lời.</p><a class="ht-button ht-button-light" href="{{ route('home', ['sort' => 'sale']) }}#san-pham">Chọn một món quà @include('partials.icon', ['name' => 'arrow', 'size' => 18])</a><span class="ht-story-signature">with love, Ha Thu</span></div>
</section>
@endif
@endsection
