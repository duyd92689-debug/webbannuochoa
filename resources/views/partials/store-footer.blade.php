<footer class="ht-footer">
    <div class="store-container ht-footer-grid">
        <div class="ht-footer-about">
            <a class="ht-brand" href="{{ route('home') }}"><span class="ht-brand-name">Ha Thu<span>PERFUME STUDIO</span></span></a>
            <p>Hương thơm là cách dịu dàng nhất để kể câu chuyện của riêng bạn.</p>
            <span class="ht-footer-signature">Chọn hương. Chọn chính mình.</span>
        </div>
        <div><h2>Khám phá</h2><a href="{{ route('home', ['gender' => 'nu']) }}#san-pham">Nước hoa nữ</a><a href="{{ route('home', ['gender' => 'nam']) }}#san-pham">Nước hoa nam</a><a href="{{ route('home', ['gender' => 'unisex']) }}#san-pham">Nước hoa unisex</a><a href="{{ route('home', ['sort' => 'sale']) }}#san-pham">Ưu đãi hiện có</a></div>
        <div><h2>Dành cho bạn</h2><a href="{{ route('orders.tracking') }}">Theo dõi đơn hàng</a><a href="{{ route('orders.index') }}">Lịch sử mua hàng</a><a href="{{ route('cart.index') }}">Giỏ hàng của bạn</a><a href="{{ route('register') }}">Trở thành thành viên</a></div>
        <div class="ht-footer-help"><span class="ht-eyebrow">MỘT CHÚT THẤU HIỂU</span><h2>Tìm hương thơm<br>hợp với bạn.</h2><p>Bắt đầu từ thương hiệu bạn yêu, hay một mùi hương bạn muốn khám phá.</p><a class="ht-text-link" href="{{ route('home') }}#bo-suu-tap">Khám phá bộ sưu tập @include('partials.icon', ['name' => 'arrow', 'size' => 18])</a></div>
    </div>
    <div class="store-container ht-footer-bottom"><span>© {{ date('Y') }} Ha Thu Perfume Studio.</span><span>Được chăm chút, từ hương thơm đến trải nghiệm.</span><a href="#main-content">Về đầu trang ↑</a></div>
</footer>