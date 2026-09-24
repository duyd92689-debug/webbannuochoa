@extends('layouts.store')

@section('title', 'Lịch sử đơn hàng · Ha Thu Perfume')

@section('content')
<div class="orders-history-page">
    <div class="store-container">
        {{-- Breadcrumb --}}
        <nav class="orders-breadcrumb">
            <a href="{{ route('home') }}">Trang chủ</a>
            <span>/</span>
            <span class="active">Đơn hàng của tôi</span>
        </nav>

        <div class="orders-page-header">
            <div>
                <span class="badge-tag">NHỮNG MÙI HƯƠNG BẠN ĐÃ CHỌN</span>
                <h1>Đơn hàng của bạn</h1>
                <p>Theo dõi tiến trình vận chuyển đơn hàng qua Giao Hàng Nhanh (GHN)</p>
            </div>
            <a href="{{ route('home') }}#san-pham" class="btn-continue-shop">+ Tiếp tục mua sắm</a>
        </div>

        @if($orders->isEmpty())
            <div class="empty-orders-card">
                <div class="empty-icon">📦</div>
                <h3>Bạn chưa có đơn hàng nào</h3>
                <p>Hãy khám phá bộ sưu tập nước hoa cao cấp tại Ha Thu Perfume và đặt hàng ngay hôm nay!</p>
                <a href="{{ route('home') }}#san-pham" class="btn-shop-now">Khám phá sản phẩm</a>
            </div>
        @else
            <div class="orders-list-wrapper">
                @foreach ($orders as $order)
                    @php
                        $statusLabels = [
                            'pending' => ['text' => 'Chờ xử lý', 'class' => 'status-pending'],
                            'ready_to_pick' => ['text' => 'GHN đã nhận đơn', 'class' => 'status-ready'],
                            'delivering' => ['text' => 'Đang giao hàng', 'class' => 'status-shipping'],
                            'delivered' => ['text' => 'Giao thành công', 'class' => 'status-delivered'],
                            'cancelled' => ['text' => 'Đã hủy', 'class' => 'status-cancelled'],
                            'not_shipped' => ['text' => 'Chưa giao', 'class' => 'status-pending'],
                        ];
                        $st = $statusLabels[$order->shipping_status] ?? ['text' => $order->shipping_status, 'class' => 'status-pending'];
                    @endphp

                    <div class="order-card-box">
                        <div class="order-card-top">
                            <div class="order-info-group">
                                <span class="order-id-badge">Đơn hàng #{{ $order->id }}</span>
                                <span class="order-time">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                @if($order->ghn_order_code)
                                    <span class="ghn-code-pill">
                                        🚚 Mã GHN: <strong>{{ $order->ghn_order_code }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="order-status-group">
                                <span class="shipping-status-tag {{ $st['class'] }}">{{ $st['text'] }}</span>
                            </div>
                        </div>

                        <div class="order-card-content">
                            <div class="order-items-preview">
                                @foreach ($order->items as $item)
                                    @php $prod = $item->product ?? $item->perfume; @endphp
                                    <div class="item-mini-row">
                                        <div class="item-mini-thumb">
                                            @if($prod && $prod->image_src)
                                                <img src="{{ $prod->image_src }}" alt="{{ $prod->name }}">
                                            @else
                                                <span>🌸</span>
                                            @endif
                                        </div>
                                        <div class="item-mini-info">
                                            <span class="item-mini-name">{{ $prod->name ?? 'Nước hoa' }}</span>
                                            <span class="item-mini-meta">Số lượng: x{{ $item->quantity }} · Giá: {{ number_format($item->price, 0, ',', '.') }}₫</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="order-finance-summary">
                                <div class="finance-row">
                                    <span>Cước vận chuyển (GHN):</span>
                                    <strong>{{ number_format($order->ghn_total_fee, 0, ',', '.') }} VNĐ</strong>
                                </div>
                                <div class="finance-row total-row">
                                    <span>Tổng thanh toán:</span>
                                    <strong class="total-price-highlight">{{ number_format($order->total_price, 0, ',', '.') }} VNĐ</strong>
                                </div>
                            </div>
                        </div>

                        <div class="order-card-footer">
                            <div class="order-receiver-info">
                                <span>📍 Người nhận: <strong>{{ $order->name }}</strong> ({{ $order->phone }}) - {{ $order->address }}</span>
                                <div style="margin-top: 4px; display: flex; gap: 8px; align-items: center;">
                                    @php
                                        $lastTx = $order->paymentTransactions?->first();
                                        $gw = $lastTx?->gateway;
                                    @endphp
                                    @if($order->status === 'paid')
                                        @if($gw === 'atm_domestic')
                                            <span class="badge badge-success" style="background:#10b981; color:#fff; font-size:0.75rem; padding:3px 8px; border-radius:6px; font-weight:600;">✓ Đã thanh toán qua Thẻ ATM Nội Địa</span>
                                        @elseif($gw === 'atm_international')
                                            <span class="badge badge-success" style="background:#10b981; color:#fff; font-size:0.75rem; padding:3px 8px; border-radius:6px; font-weight:600;">✓ Đã thanh toán qua Thẻ Quốc Tế (Visa/Master)</span>
                                        @else
                                            <span class="badge badge-success" style="background:#10b981; color:#fff; font-size:0.75rem; padding:3px 8px; border-radius:6px; font-weight:600;">✓ Đã thanh toán qua MoMo</span>
                                        @endif
                                    @elseif($order->status === 'cod_ordered')
                                        <span class="badge badge-info" style="background:#0284c7; color:#fff; font-size:0.75rem; padding:3px 8px; border-radius:6px; font-weight:600;">💵 Thanh toán COD khi nhận hàng</span>
                                    @elseif($order->status === 'pending')
                                        <span class="badge badge-warning" style="background:#f59e0b; color:#fff; font-size:0.75rem; padding:3px 8px; border-radius:6px; font-weight:600;">⏳ Chờ thanh toán</span>
                                    @endif
                                </div>
                            </div>
                            <div class="order-actions" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                                @if($order->status === 'pending')
                                    <a href="{{ route('user.orders.momo.pay', $order->id) }}" class="btn-pay-again" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#a50064,#db2777);color:#fff;font-weight:600;font-size:0.85rem;padding:9px 18px;border-radius:8px;text-decoration:none;transition:all 0.2s;box-shadow:0 3px 10px rgba(165,0,100,0.25);">
                                        <img src="{{ asset('images/payments/momo.svg') }}" alt="MoMo" style="height:18px;width:18px;border-radius:4px;object-fit:contain;">
                                        <span>Thanh toán lại qua MoMo</span>
                                    </a>
                                @endif
                                <a href="{{ route('orders.show', $order->id) }}" class="btn-detail">Xem chi tiết đơn →</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="orders-pagination">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>

<style>
.orders-history-page {
    padding: 32px 0 80px;
    background: #faf8f9;
    min-height: 80vh;
}
.orders-breadcrumb {
    display: flex;
    gap: 8px;
    font-size: 0.85rem;
    color: #6b7280;
    margin-bottom: 24px;
}
.orders-breadcrumb a {
    color: #4b5563;
    text-decoration: none;
}
.orders-breadcrumb a:hover {
    color: #db2777;
}
.orders-breadcrumb .active {
    color: #db2777;
    font-weight: 600;
}

.orders-page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 30px;
}
.orders-page-header .badge-tag {
    display: inline-block;
    background: #fce7f3;
    color: #be185d;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    padding: 4px 10px;
    border-radius: 9999px;
    margin-bottom: 8px;
    text-transform: uppercase;
}
.orders-page-header h1 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 2.2rem;
    font-weight: 600;
    color: #111827;
    margin: 0 0 6px 0;
}
.orders-page-header p {
    color: #6b7280;
    font-size: 0.95rem;
    margin: 0;
}
.btn-continue-shop {
    padding: 10px 18px;
    background: #ffffff;
    border: 1.5px solid #fbcfe8;
    color: #db2777;
    font-size: 0.88rem;
    font-weight: 600;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-continue-shop:hover {
    background: #fdf2f8;
}

/* Empty Card */
.empty-orders-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 60px 20px;
    text-align: center;
    border: 1px solid #f3e8ee;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    max-width: 520px;
    margin: 40px auto;
}
.empty-icon {
    font-size: 3.5rem;
    margin-bottom: 16px;
}
.empty-orders-card h3 {
    font-size: 1.35rem;
    color: #1f2937;
    margin-bottom: 8px;
}
.empty-orders-card p {
    color: #6b7280;
    font-size: 0.92rem;
    line-height: 1.5;
    margin-bottom: 24px;
}
.btn-shop-now {
    display: inline-block;
    padding: 12px 28px;
    background: linear-gradient(135deg, #f472b6 0%, #db2777 100%);
    color: #ffffff;
    font-weight: 600;
    border-radius: 10px;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(219, 39, 119, 0.25);
    transition: all 0.2s;
}
.btn-shop-now:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(219, 39, 119, 0.35);
}

/* Orders List */
.orders-list-wrapper {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.order-card-box {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #f3e8ee;
    box-shadow: 0 4px 20px rgba(219, 39, 119, 0.03);
    overflow: hidden;
}
.order-card-top {
    padding: 16px 24px;
    background: #fdfafc;
    border-bottom: 1px solid #f5ecf0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}
.order-info-group {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.order-id-badge {
    font-weight: 700;
    color: #1f2937;
    font-size: 0.95rem;
}
.order-time {
    color: #9ca3af;
    font-size: 0.82rem;
}
.ghn-code-pill {
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
    font-size: 0.8rem;
    padding: 3px 10px;
    border-radius: 9999px;
}
.ghn-code-pill strong {
    color: #1d4ed8;
}

.shipping-status-tag {
    font-size: 0.8rem;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 9999px;
    display: inline-block;
}
.status-pending {
    background: #fef3c7;
    color: #b45309;
}
.status-ready {
    background: #e0e7ff;
    color: #4338ca;
}
.status-shipping {
    background: #dbeafe;
    color: #1d4ed8;
}
.status-delivered {
    background: #dcfce7;
    color: #15803d;
}
.status-cancelled {
    background: #fee2e2;
    color: #b91c1c;
}

.order-card-content {
    padding: 20px 24px;
    display: grid;
    grid-template-columns: 1fr 280px;
    gap: 24px;
    align-items: center;
}
.order-items-preview {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.item-mini-row {
    display: flex;
    align-items: center;
    gap: 12px;
}
.item-mini-thumb {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    border: 1px solid #f3e8ee;
    background: #fafafa;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}
.item-mini-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.item-mini-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.item-mini-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1f2937;
}
.item-mini-meta {
    font-size: 0.78rem;
    color: #6b7280;
}

.order-finance-summary {
    border-left: 1px solid #f3f4f6;
    padding-left: 20px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.finance-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    color: #4b5563;
}
.total-row {
    margin-top: 6px;
    padding-top: 8px;
    border-top: 1px dashed #e5e7eb;
}
.total-row span {
    font-weight: 600;
    color: #111827;
}
.total-price-highlight {
    color: #db2777;
    font-size: 1.15rem;
}

.order-card-footer {
    padding: 14px 24px;
    background: #ffffff;
    border-top: 1px solid #f5ecf0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}
.order-receiver-info {
    font-size: 0.82rem;
    color: #6b7280;
}
.btn-detail {
    padding: 8px 16px;
    background: #fff;
    border: 1px solid #e5e7eb;
    color: #374151;
    font-size: 0.83rem;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-detail:hover {
    border-color: #db2777;
    color: #db2777;
    background: #fdf2f8;
}

.orders-pagination {
    margin-top: 30px;
    display: flex;
    justify-content: center;
}

@media (max-width: 768px) {
    .order-card-content {
        grid-template-columns: 1fr;
    }
    .order-finance-summary {
        border-left: none;
        padding-left: 0;
        border-top: 1px dashed #f3f4f6;
        padding-top: 14px;
    }
    .orders-page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 14px;
    }
}

/* ===== Pay Again Dropdown ===== */
.pay-again-dropdown { position: relative; display: inline-block; }
.btn-pay-again-trigger {
    display: inline-flex; align-items: center; gap: 7px;
    background: linear-gradient(135deg, #a50064 0%, #d82d8b 100%);
    color: #fff; font-weight: 700; font-size: 0.85rem;
    padding: 9px 14px; border-radius: 8px; border: none;
    cursor: pointer; box-shadow: 0 2px 10px rgba(165,0,100,0.3);
    transition: all 0.2s; white-space: nowrap;
}
.btn-pay-again-trigger:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(165,0,100,0.4); }
.pay-arrow { font-size: 0.65rem; transition: transform 0.2s; }
.pay-arrow.open { transform: rotate(180deg); }

.pay-drop-menu {
    position: absolute; bottom: calc(100% + 6px); left: 0;
    min-width: 240px; background: #fff;
    border: 1.5px solid #f9a8d4; border-radius: 14px;
    box-shadow: 0 8px 28px rgba(165,0,100,0.15);
    z-index: 9999; overflow: hidden;
    animation: payDropIn 0.18s ease;
}
@keyframes payDropIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.pay-drop-header {
    padding: 9px 14px 7px; font-size: 0.73rem; font-weight: 700;
    color: #9ca3af; text-transform: uppercase; letter-spacing: 0.06em;
    border-bottom: 1px solid #fdf2f8;
}
.pay-drop-item {
    display: flex; align-items: center; gap: 10px;
    padding: 11px 14px; text-decoration: none;
    border-bottom: 1px solid #fdf2f8; transition: background 0.14s;
}
.pay-drop-item:last-child { border-bottom: none; }
.pay-drop-item:hover { background: #fdf2f8; }
.pay-ditem-icon {
    width: 34px; height: 34px; border-radius: 9px; display: flex;
    align-items: center; justify-content: center; flex-shrink: 0;
    font-size: 1.1rem;
}
.pay-ditem-icon.momo-bg { background: #fce7f3; }
.pay-ditem-icon.domestic-bg { background: #dbeafe; }
.pay-ditem-icon.intl-bg { background: #e0e7ff; }
.pay-ditem-info { display: flex; flex-direction: column; gap: 1px; }
.pay-ditem-name { font-size: 0.85rem; font-weight: 600; color: #111827; }
.pay-ditem-desc { font-size: 0.72rem; color: #6b7280; }
</style>

<script>
function togglePayDropdown(orderId, event) {
    event.stopPropagation();
    const menu = document.getElementById('payMenu_' + orderId);
    const btn = event.currentTarget;
    const arrow = btn.querySelector('.pay-arrow');
    const isOpen = menu.style.display === 'block';
    // Đóng tất cả dropdown khác
    document.querySelectorAll('.pay-drop-menu').forEach(m => m.style.display = 'none');
    document.querySelectorAll('.pay-arrow').forEach(a => a.classList.remove('open'));
    // Toggle cái hiện tại
    if (!isOpen) {
        menu.style.display = 'block';
        arrow.classList.add('open');
    }
}
// Đóng khi click ngoài
document.addEventListener('click', function() {
    document.querySelectorAll('.pay-drop-menu').forEach(m => m.style.display = 'none');
    document.querySelectorAll('.pay-arrow').forEach(a => a.classList.remove('open'));
});
</script>
@endsection
