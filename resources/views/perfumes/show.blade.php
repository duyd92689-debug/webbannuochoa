@extends('layouts.store')

@section('title', $perfume->name.' · Ha Thu Perfume Studio')

@section('content')
    <section class="store-container luxury-product-page">
        {{-- Breadcrumb --}}
        <nav class="luxury-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('home') }}#san-pham">{{ $perfume->category?->name ?? 'Nước hoa' }}</a>
            <span>/</span>
            <span class="current">{{ $perfume->name }}</span>
        </nav>

        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger purchase-alert" role="alert" style="margin-bottom: 20px;">
                @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        <div class="luxury-product-grid">
            {{-- Visual Gallery --}}
            <div class="luxury-gallery-column">
                <div class="luxury-main-visual">
                    @if ($perfume->image_src)
                        <img id="mainProductImage" src="{{ $perfume->image_src }}" alt="{{ $perfume->name }}">
                    @else
                        <div class="luxury-bottle-placeholder">
                            <span>{{ mb_substr($perfume->brand, 0, 1) }}</span>
                            <small>{{ $perfume->brand }}</small>
                        </div>
                    @endif

                    @if ($perfume->sale_price !== null)
                        <span class="luxury-badge-sale">ƯU ĐÃI -{{ round((($perfume->price - $perfume->sale_price) / $perfume->price) * 100) }}%</span>
                    @endif

                    <div class="luxury-authenticity-tag">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>
                        Cam kết nước hoa chính hãng
                    </div>
                </div>

                {{-- Live Viewers & Social Proof --}}
                <p class="ht-product-advice">Một mùi hương riêng, một dấu ấn khó quên.</p>

                {{-- Highlights Box --}}
                <div class="luxury-guarantees-grid">
                    <div class="guarantee-item">
                        <div class="guarantee-icon">🚚</div>
                        <div>
                            <strong>Freeship toàn quốc</strong>
                            <small>Đóng gói 3 lớp chống vỡ</small>
                        </div>
                    </div>
                    <div class="guarantee-item">
                        <div class="guarantee-icon">✨</div>
                        <div>
                            <strong>Đổi trả 14 ngày</strong>
                            <small>Hỗ trợ đổi mùi linh hoạt</small>
                        </div>
                    </div>
                    <div class="guarantee-item">
                        <div class="guarantee-icon">🛡️</div>
                        <div>
                            <strong>Cam kết chính hãng</strong>
                            <small>Đền 200% nếu phát hiện giả</small>
                        </div>
                    </div>
                    <div class="guarantee-item">
                        <div class="guarantee-icon">🎁</div>
                        <div>
                            <strong>Tặng mẫu thử Mini</strong>
                            <small>Kèm trong mọi đơn hàng</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Information & Purchase Form --}}
            <div class="luxury-info-column">
                <div class="luxury-brand-kicker">{{ $perfume->brand }}</div>
                <h1 class="luxury-product-title">{{ $perfume->name }}</h1>

                <div class="luxury-category-tags">
                    <span class="tag-pill">{{ $perfume->category?->name ?? 'Nước hoa' }}</span>
                    <span class="tag-pill">{{ ['nam' => 'Dành cho Nam', 'nu' => 'Dành cho Nữ', 'unisex' => 'Unisex - Mọi giới tính'][$perfume->gender] }}</span>
                    <span class="tag-pill">{{ $perfume->concentration ?: 'Eau de Parfum (EDP)' }}</span>
                </div>

                {{-- Price Display --}}
                @php
                    $baseFullPrice = (float) ($perfume->sale_price ?? $perfume->price);
                    $originalFullPrice = (float) $perfume->price;
                    $volume100 = (int) ($perfume->volume_ml ?: 100);
                    
                    // Tính giá theo dung tích đẹp mắt
                    $price10ml = round(($baseFullPrice * 0.22) / 10000) * 10000;
                    $price50ml = round(($baseFullPrice * 0.65) / 10000) * 10000;
                    $priceFull = $baseFullPrice;

                    // Số lượng tồn kho theo từng dung tích riêng biệt
                    $stock10ml = (int) $perfume->stock_10ml;
                    $stock50ml = (int) $perfume->stock_50ml;
                    $stockFull = (int) $perfume->stock_100ml;
                @endphp

                <div class="luxury-price-box">
                    <div class="current-price" id="displayPrice">{{ number_format($priceFull, 0, ',', '.') }}₫</div>
                    @if ($perfume->sale_price !== null)
                        <del class="old-price" id="displayOldPrice">{{ number_format($originalFullPrice, 0, ',', '.') }}₫</del>
                        <span class="save-tag" id="displaySaveTag">Tiết kiệm {{ number_format($originalFullPrice - $priceFull, 0, ',', '.') }}₫</span>
                    @endif
                </div>

                <div class="luxury-stock-status {{ $stockFull <= 5 && $stockFull > 0 ? 'low' : ($stockFull <= 0 ? 'out-of-stock' : '') }}" id="luxuryStockStatus">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
                    <span id="stockStatusText">{{ $stockFull > 0 ? 'Còn hàng trong kho ('.$stockFull.' chai '.$volume100.'ml Fullbox sẵn sàng giao)' : 'Tạm thời hết hàng dung tích '.$volume100.'ml' }}</span>
                </div>

                <form class="luxury-purchase-form" method="POST" action="{{ route('cart.add', $perfume) }}" id="purchaseForm">
                    @csrf
                    
                    {{-- Dung tích (Volume Options) --}}
                    <div class="form-option-section">
                        <div class="option-header">
                            <label class="option-title">Chọn Dung Tích:</label>
                            <span class="option-sub" id="volumeSelectedLabel">{{ $volume100 }}ml (Fullbox Nguyên Seal) · Còn {{ $stockFull }} chai</span>
                        </div>
                        <div class="volume-options-grid">
                            {{-- Option 10ml Chiết --}}
                            <label class="volume-card-option" data-volume="10" data-stock="{{ $stock10ml }}" data-desc="Chiết Travel Spray" data-price="{{ $price10ml }}" data-oldprice="{{ round($price10ml * 1.25 / 10000) * 10000 }}">
                                <input type="radio" name="volume_ml" value="10">
                                <div class="volume-card-badge">Dùng thử</div>
                                <div class="volume-card-size">10ml</div>
                                <div class="volume-card-desc">Chiết Travel Spray</div>
                                <div class="volume-card-price">{{ number_format($price10ml, 0, ',', '.') }}₫</div>
                                <div class="volume-card-stock {{ $stock10ml <= 5 && $stock10ml > 0 ? 'stock-low' : ($stock10ml <= 0 ? 'stock-out' : '') }}">
                                    @if($stock10ml > 0)
                                        <span class="stock-dot"></span> Còn {{ $stock10ml }} chai
                                    @else
                                        <span class="stock-dot" style="background:#ef4444;"></span> Tạm hết
                                    @endif
                                </div>
                            </label>

                            {{-- Option 50ml --}}
                            <label class="volume-card-option" data-volume="50" data-stock="{{ $stock50ml }}" data-desc="Chai Vừa Phải" data-price="{{ $price50ml }}" data-oldprice="{{ round($price50ml * 1.2 / 10000) * 10000 }}">
                                <input type="radio" name="volume_ml" value="50">
                                <div class="volume-card-badge">Phổ biến</div>
                                <div class="volume-card-size">50ml</div>
                                <div class="volume-card-desc">Chai Vừa Phải</div>
                                <div class="volume-card-price">{{ number_format($price50ml, 0, ',', '.') }}₫</div>
                                <div class="volume-card-stock {{ $stock50ml <= 5 && $stock50ml > 0 ? 'stock-low' : ($stock50ml <= 0 ? 'stock-out' : '') }}">
                                    @if($stock50ml > 0)
                                        <span class="stock-dot"></span> Còn {{ $stock50ml }} chai
                                    @else
                                        <span class="stock-dot" style="background:#ef4444;"></span> Tạm hết
                                    @endif
                                </div>
                            </label>

                            {{-- Option Fullsize (Default) --}}
                            <label class="volume-card-option active" data-volume="{{ $volume100 }}" data-stock="{{ $stockFull }}" data-desc="Fullbox Nguyên Seal" data-price="{{ $priceFull }}" data-oldprice="{{ $originalFullPrice }}">
                                <input type="radio" name="volume_ml" value="{{ $volume100 }}" checked>
                                <div class="volume-card-badge best-seller">Bán chạy nhất ★</div>
                                <div class="volume-card-size">{{ $volume100 }}ml</div>
                                <div class="volume-card-desc">Fullbox Nguyên Seal</div>
                                <div class="volume-card-price">{{ number_format($priceFull, 0, ',', '.') }}₫</div>
                                <div class="volume-card-stock {{ $stockFull <= 5 && $stockFull > 0 ? 'stock-low' : ($stockFull <= 0 ? 'stock-out' : '') }}">
                                    @if($stockFull > 0)
                                        <span class="stock-dot"></span> Còn {{ $stockFull }} chai
                                    @else
                                        <span class="stock-dot" style="background:#ef4444;"></span> Tạm hết
                                    @endif
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Dịch Vụ Đi Kèm & Quà Tặng (Add-ons / Options) --}}
                    <div class="form-option-section">
                        <label class="option-title">Dịch Vụ & Tuỳ Chọn Cao Cấp:</label>
                        <div class="addon-options-list">
                            <label class="addon-card-option">
                                <input type="checkbox" name="addon_gift" id="addonGift" value="1" onchange="calculateTotalPrice()">
                                <div class="addon-icon">🎁</div>
                                <div class="addon-text">
                                    <strong>Gói quà Luxury & Thiệp chúc mừng</strong>
                                    <small>Hộp quà cao cấp thắt nơ lụa thủ công + thiệp viết tay theo yêu cầu (+50.000₫)</small>
                                </div>
                            </label>

                            <label class="addon-card-option">
                                <input type="checkbox" name="addon_engrave" id="addonEngrave" value="1" onchange="toggleEngraveField()">
                                <div class="addon-icon">✨</div>
                                <div class="addon-text">
                                    <strong>Khắc tên / Lời chúc Laser lên thân chai</strong>
                                    <small>Cá nhân hóa dấu ấn riêng (Miễn phí quà tặng)</small>
                                </div>
                            </label>
                            
                            <div class="engrave-input-box" id="engraveInputBox" style="display: none;">
                                <input type="text" name="engrave_text" maxlength="30" placeholder="Nhập tên hoặc lời chúc muốn khắc (tối đa 30 ký tự)...">
                            </div>
                        </div>
                    </div>

                    {{-- Số lượng & Action Buttons --}}
                    <div class="form-option-section">
                        <label class="option-title">Số Lượng:</label>
                        <div class="quantity-and-action-row">
                            <div class="luxury-quantity-picker" data-quantity-picker>
                                <button type="button" data-quantity-minus aria-label="Giảm số lượng">−</button>
                                <input type="number" name="quantity" id="productQuantity" value="1" min="1" max="{{ max(1, $perfume->stock) }}" aria-label="Số lượng sản phẩm">
                                <button type="button" data-quantity-plus aria-label="Tăng số lượng">+</button>
                            </div>

                            <div class="subtotal-indicator">
                                <span>Tạm tính: </span>
                                <strong id="dynamicSubtotal">{{ number_format($priceFull, 0, ',', '.') }}₫</strong>
                            </div>
                        </div>
                    </div>

                    <div class="luxury-purchase-actions">
                        <button class="luxury-add-cart-btn" type="submit" @disabled($perfume->stock < 1)>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                            Thêm vào giỏ hàng
                        </button>
                        <button class="luxury-buy-now-btn" type="submit" name="buy_now" value="1" @disabled($perfume->stock < 1)>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                            Mua ngay
                        </button>
                    </div>
                </form>

                {{-- Thông số kỹ thuật nhanh --}}
                <div class="luxury-specs-card">
                    <h3>Thông Tin Chi Tiết</h3>
                    <div class="specs-grid">
                        <div class="spec-row">
                            <span class="spec-label">Thương hiệu</span>
                            <span class="spec-value">{{ $perfume->brand }}</span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label">Nồng độ</span>
                            <span class="spec-value">{{ $perfume->concentration ?: 'Eau de Parfum (EDP)' }}</span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label">Dung tích chọn</span>
                            <span class="spec-value" id="specVolumeValue">{{ $volume100 }}ml</span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label">Giới tính</span>
                            <span class="spec-value">{{ ['nam' => 'Nam giới', 'nu' => 'Nữ giới', 'unisex' => 'Unisex'][$perfume->gender] }}</span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label">Xuất xứ</span>
                            <span class="spec-value">Pháp / Ý (Chính hãng)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $scent = $perfume->scent_profile;
        @endphp

        {{-- Kim Tự Tháp Tầng Hương (Olfactory Fragrance Notes) --}}
        <div class="luxury-olfactory-section">
            <div class="section-title-center">
                <span class="section-kicker">Trải nghiệm mùi hương độc bản</span>
                <h2>Kim Tự Tháp Tầng Hương Đặc Trưng</h2>
                <p>Nghệ thuật hòa quyện của các nốt hương tinh tế lưu giữ suốt cả ngày</p>
                <div class="mt-3">
                    <span class="badge badge-pill px-3 py-2" style="background: {{ $scent['badge_color'] }}15; color: {{ $scent['badge_color'] }}; font-size: 0.85rem; font-weight: 700; border: 1.5px solid {{ $scent['badge_color'] }}40; letter-spacing: 0.5px; display: inline-block;">
                        {{ $scent['family_badge'] }} · {{ $scent['family'] }}
                    </span>
                </div>
            </div>

            <div class="pyramid-cards-grid">
                <div class="pyramid-card">
                    <div class="pyramid-phase-badge">{{ $scent['top']['time'] }}</div>
                    <div class="pyramid-icon">{{ $scent['top']['icon'] }}</div>
                    <h4>{{ $scent['top']['title'] }}</h4>
                    <p class="notes-desc">{{ $scent['top']['notes'] }}</p>
                    <small>{{ $scent['top']['desc'] }}</small>
                    @if(!empty($scent['top']['tags']))
                        <div class="mt-3 d-flex flex-wrap justify-content-center" style="gap: 5px;">
                            @foreach($scent['top']['tags'] as $tag)
                                <span style="font-size: 11px; background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 2px 10px; color: #475569; font-weight: 500;">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="pyramid-card active-pyramid">
                    <div class="pyramid-phase-badge">{{ $scent['heart']['time'] }}</div>
                    <div class="pyramid-icon">{{ $scent['heart']['icon'] }}</div>
                    <h4>{{ $scent['heart']['title'] }}</h4>
                    <p class="notes-desc">{{ $scent['heart']['notes'] }}</p>
                    <small>{{ $scent['heart']['desc'] }}</small>
                    @if(!empty($scent['heart']['tags']))
                        <div class="mt-3 d-flex flex-wrap justify-content-center" style="gap: 5px;">
                            @foreach($scent['heart']['tags'] as $tag)
                                <span style="font-size: 11px; background: #fff; border: 1px solid #fed7aa; border-radius: 20px; padding: 2px 10px; color: #9a3412; font-weight: 500;">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="pyramid-card">
                    <div class="pyramid-phase-badge">{{ $scent['base']['time'] }}</div>
                    <div class="pyramid-icon">{{ $scent['base']['icon'] }}</div>
                    <h4>{{ $scent['base']['title'] }}</h4>
                    <p class="notes-desc">{{ $scent['base']['notes'] }}</p>
                    <small>{{ $scent['base']['desc'] }}</small>
                    @if(!empty($scent['base']['tags']))
                        <div class="mt-3 d-flex flex-wrap justify-content-center" style="gap: 5px;">
                            @foreach($scent['base']['tags'] as $tag)
                                <span style="font-size: 11px; background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 2px 10px; color: #475569; font-weight: 500;">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Đánh giá chỉ số mùi hương --}}
            <div class="fragrance-meters-grid">
                <div class="meter-box">
                    <div class="meter-header">
                        <span>⏳ Độ lưu hương</span>
                        <strong>{{ $scent['longevity']['text'] }}</strong>
                    </div>
                    <div class="meter-bar"><div class="meter-fill" style="width: {{ $scent['longevity']['percent'] }}%;"></div></div>
                </div>
                <div class="meter-box">
                    <div class="meter-header">
                        <span>💨 Độ tỏa hương</span>
                        <strong>{{ $scent['sillage']['text'] }}</strong>
                    </div>
                    <div class="meter-bar"><div class="meter-fill" style="width: {{ $scent['sillage']['percent'] }}%;"></div></div>
                </div>
                <div class="meter-box">
                    <div class="meter-header">
                        <span>🌙 Thời điểm khuyên dùng</span>
                        <strong>{{ $scent['season']['text'] }}</strong>
                    </div>
                    <div class="meter-bar"><div class="meter-fill" style="width: {{ $scent['season']['percent'] }}%;"></div></div>
                </div>
                <div class="meter-box">
                    <div class="meter-header">
                        <span>👔 Phong cách phù hợp</span>
                        <strong>{{ $scent['style']['text'] }}</strong>
                    </div>
                    <div class="meter-bar"><div class="meter-fill" style="width: {{ $scent['style']['percent'] }}%;"></div></div>
                </div>
            </div>

            {{-- Dải các nốt hương chủ đạo --}}
            @if(!empty($scent['highlight_notes']))
                <div class="mt-4 pt-3 border-top text-center" style="border-top: 1px solid #f1f5f9;">
                    <span class="text-muted small text-uppercase font-weight-bold d-block mb-2" style="letter-spacing: 1.5px; font-size: 11px;">
                        ✨ Các nốt hương chủ đạo định hình phong cách
                    </span>
                    <div class="d-flex flex-wrap justify-content-center" style="gap: 8px;">
                        @foreach($scent['highlight_notes'] as $hNote)
                            <span class="badge border px-3 py-2" style="font-size: 12px; font-weight: 600; color: #334155; border-radius: 30px; background: #f8fafc; border-color: #e2e8f0;">
                                ✦ {{ $hNote }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Mô tả chi tiết sản phẩm --}}
        <div class="luxury-description-section">
            <h2>Mô Tả Sản Phẩm</h2>
            <div class="luxury-description-content">
                <p>{{ $perfume->description ?: 'Sản phẩm nước hoa cao cấp được tuyển chọn tỉ mỉ cho bộ sưu tập Ha Thu Perfume Studio. Mỗi giọt tinh dầu là một tuyên ngôn về phong cách và thần thái sang trọng.' }}</p>
                
                <p>Nước hoa <strong>{{ $perfume->name }}</strong> từ thương hiệu danh tiếng <strong>{{ $perfume->brand }}</strong> mang đến trải nghiệm hương thơm đỉnh cao, giúp bạn tự tin tỏa sáng trong mọi buổi gặp gỡ, dạ tiệc hay công việc hàng ngày.</p>
            </div>
        </div>

    </section>

    {{-- Interactive Script for Dynamic Price, Options & Calculations --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let currentBasePrice = {{ $priceFull }};
            let currentOldPrice = {{ $originalFullPrice }};
            let currentVolume = {{ $volume100 }};

            const volumeCards = document.querySelectorAll('.volume-card-option');
            const displayPrice = document.getElementById('displayPrice');
            const displayOldPrice = document.getElementById('displayOldPrice');
            const displaySaveTag = document.getElementById('displaySaveTag');
            const volumeSelectedLabel = document.getElementById('volumeSelectedLabel');
            const specVolumeValue = document.getElementById('specVolumeValue');
            const quantityInput = document.getElementById('productQuantity');
            const dynamicSubtotal = document.getElementById('dynamicSubtotal');
            const addonGift = document.getElementById('addonGift');

            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
            }

            window.calculateTotalPrice = function() {
                const qty = parseInt(quantityInput.value) || 1;
                const giftPrice = addonGift && addonGift.checked ? 50000 : 0;
                
                const unitPriceWithAddon = currentBasePrice + giftPrice;
                const totalPrice = unitPriceWithAddon * qty;

                displayPrice.textContent = formatCurrency(unitPriceWithAddon);
                if (displayOldPrice) {
                    displayOldPrice.textContent = formatCurrency(currentOldPrice + giftPrice);
                }
                if (displaySaveTag && currentOldPrice > currentBasePrice) {
                    displaySaveTag.textContent = 'Tiết kiệm ' + formatCurrency(currentOldPrice - currentBasePrice);
                }

                if (dynamicSubtotal) {
                    dynamicSubtotal.textContent = formatCurrency(totalPrice);
                }
            };

            window.toggleEngraveField = function() {
                const engraveBox = document.getElementById('engraveInputBox');
                const isChecked = document.getElementById('addonEngrave').checked;
                if (engraveBox) {
                    engraveBox.style.display = isChecked ? 'block' : 'none';
                    if (isChecked) {
                        engraveBox.querySelector('input')?.focus();
                    }
                }
            };

            const stockStatusContainer = document.getElementById('luxuryStockStatus');
            const stockStatusText = document.getElementById('stockStatusText');
            const btnAddCart = document.querySelector('.luxury-add-cart-btn');
            const btnBuyNow = document.querySelector('.luxury-buy-now-btn');

            volumeCards.forEach(card => {
                card.addEventListener('click', function () {
                    volumeCards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');

                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) radio.checked = true;

                    currentVolume = this.getAttribute('data-volume');
                    currentBasePrice = parseFloat(this.getAttribute('data-price')) || {{ $priceFull }};
                    currentOldPrice = parseFloat(this.getAttribute('data-oldprice')) || currentBasePrice;
                    const stockNum = parseInt(this.getAttribute('data-stock')) || 0;
                    const desc = this.getAttribute('data-desc') || this.querySelector('.volume-card-desc')?.textContent || '';

                    if (volumeSelectedLabel) {
                        volumeSelectedLabel.textContent = `${currentVolume}ml (${desc}) · Còn ${stockNum} chai`;
                    }
                    if (specVolumeValue) {
                        specVolumeValue.textContent = `${currentVolume}ml`;
                    }

                    // Cập nhật trạng thái và số lượng còn lại cho dung tích đang bấm chọn
                    if (stockStatusText) {
                        if (stockNum > 0) {
                            stockStatusText.textContent = `Còn hàng trong kho (${stockNum} chai ${currentVolume}ml ${desc} sẵn sàng giao)`;
                        } else {
                            stockStatusText.textContent = `Tạm thời hết hàng dung tích ${currentVolume}ml`;
                        }
                    }

                    if (stockStatusContainer) {
                        if (stockNum <= 0) {
                            stockStatusContainer.className = 'luxury-stock-status out-of-stock';
                        } else if (stockNum <= 5) {
                            stockStatusContainer.className = 'luxury-stock-status low';
                        } else {
                            stockStatusContainer.className = 'luxury-stock-status';
                        }
                    }

                    // Giới hạn số lượng mua theo tồn kho của dung tích đó
                    if (quantityInput) {
                        quantityInput.max = Math.max(1, stockNum);
                        if (parseInt(quantityInput.value) > stockNum && stockNum > 0) {
                            quantityInput.value = stockNum;
                        }
                    }

                    if (btnAddCart) {
                        btnAddCart.disabled = stockNum < 1;
                    }
                    if (btnBuyNow) {
                        btnBuyNow.disabled = stockNum < 1;
                    }

                    calculateTotalPrice();
                });
            });

            // Lắng nghe khi bấm tăng giảm số lượng
            document.addEventListener('click', function(e) {
                if (e.target.closest('[data-quantity-minus], [data-quantity-plus]')) {
                    setTimeout(calculateTotalPrice, 50);
                }
            });

            if (quantityInput) {
                quantityInput.addEventListener('input', calculateTotalPrice);
            }


            calculateTotalPrice();
        });
    </script>

    <style>
        .volume-card-stock {
            font-size: 11.5px;
            font-weight: 600;
            color: #059669;
            margin-top: 6px;
            padding-top: 5px;
            border-top: 1px dashed #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: all 0.2s ease;
        }
        .volume-card-option.active .volume-card-stock {
            border-top-color: #cbd5e1;
            font-weight: 700;
        }
        .volume-card-stock.stock-low {
            color: #d97706;
        }
        .volume-card-stock.stock-out {
            color: #dc2626;
        }
        .stock-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: #10b981;
            display: inline-block;
            flex-shrink: 0;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }
        .volume-card-stock.stock-low .stock-dot {
            background-color: #f59e0b;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
        }
        .luxury-stock-status.out-of-stock {
            color: #dc2626;
        }
        .luxury-stock-status svg {
            flex-shrink: 0;
        }
    </style>
@endsection
