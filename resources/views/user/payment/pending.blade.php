@extends('layouts.store')

@section('title', 'Thanh toán bằng thẻ · Ha Thu Perfume')

@section('content')
<div class="card-pay-page">
    <div class="store-container">

        <nav class="card-breadcrumb">
            <a href="{{ route('home') }}">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('orders.index') }}">Đơn hàng của tôi</a>
            <span>/</span>
            <span class="active">Thanh toán bằng thẻ</span>
        </nav>

        <div class="card-pay-header">
            <div class="card-success-icon">✅</div>
            <div>
                <span class="card-badge-tag">Đặt hàng thành công</span>
                <h1>Nhập Thông Tin Thẻ Thanh Toán</h1>
                <p>Đơn hàng <strong>#{{ $order->id }}</strong> · Số tiền: <strong class="amount-highlight">{{ number_format($order->total_price, 0, ',', '.') }} VNĐ</strong></p>
            </div>
        </div>

        <div class="card-pay-grid">

            {{-- Cột trái: Form nhập thẻ --}}
            <div class="card-pay-main">

                @if($payMethod === 'atm_domestic')
                {{-- ===== ATM NỘI ĐỊA ===== --}}
                <div class="card-form-box domestic">
                    <div class="card-form-header">
                        <span class="card-type-icon">🏧</span>
                        <div>
                            <h2>Thẻ ATM Nội Địa</h2>
                            <small>Vietcombank · BIDV · Agribank · Techcombank · MB Bank · và hơn 40 ngân hàng khác</small>
                        </div>
                        <div class="domestic-bank-badges">
                            <span class="b-chip vcb">VCB</span>
                            <span class="b-chip bidv">BIDV</span>
                            <span class="b-chip tcb">TCB</span>
                            <span class="b-chip mb">MB</span>
                        </div>
                    </div>

                    {{-- Hình minh hoạ thẻ ATM --}}
                    <div class="card-visual-wrap">
                        <div class="card-3d domestic-card">
                            <div class="card-top-row">
                                <div class="card-bank-name">🏦 NGÂN HÀNG NỘI ĐỊA</div>
                                <div class="card-chip-icon">▣</div>
                            </div>
                            <div class="card-number-display" id="previewCardNum">•••• •••• •••• ••••</div>
                            <div class="card-bottom-row">
                                <div>
                                    <div class="card-field-label">Chủ thẻ</div>
                                    <div class="card-field-val" id="previewName">TÊN CHỦ THẺ</div>
                                </div>
                                <div>
                                    <div class="card-field-label">Hết hạn</div>
                                    <div class="card-field-val" id="previewExpiry">MM/YY</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-input-section">
                        <div class="input-group full-w">
                            <label for="dom_card_num">Số thẻ ATM <span class="req">*</span></label>
                            <input type="text" id="dom_card_num" placeholder="0000 0000 0000 0000" maxlength="19" class="card-field-input" oninput="formatCardNumber(this); updatePreview()">
                        </div>
                        <div class="input-row-2">
                            <div class="input-group">
                                <label for="dom_expiry">Ngày hết hạn <span class="req">*</span></label>
                                <input type="text" id="dom_expiry" placeholder="MM/YY" maxlength="5" class="card-field-input" oninput="formatExpiry(this); updatePreview()">
                            </div>
                            <div class="input-group">
                                <label for="dom_cvv">Mã CVV / CVC <span class="req">*</span></label>
                                <input type="password" id="dom_cvv" placeholder="•••" maxlength="4" class="card-field-input">
                            </div>
                        </div>
                        <div class="input-group full-w">
                            <label for="dom_name">Tên chủ thẻ (in hoa) <span class="req">*</span></label>
                            <input type="text" id="dom_name" placeholder="VD: NGUYEN THI THU HA" class="card-field-input" style="text-transform:uppercase;" oninput="updatePreview()">
                        </div>
                        <div class="input-group full-w">
                            <label for="dom_otp">Mã OTP (gửi về SĐT đăng ký thẻ) <span class="req">*</span></label>
                            <div class="otp-row">
                                <input type="text" id="dom_otp" placeholder="Nhập mã OTP 6 số" maxlength="6" class="card-field-input">
                                <button type="button" class="btn-send-otp" id="btnSendOtp" onclick="sendOtp(this)">Gửi OTP</button>
                            </div>
                        </div>

                        <button class="btn-pay-card domestic-btn" onclick="handleCardPay(event, 'domestic')">
                            🔒 Thanh toán {{ number_format($order->total_price, 0, ',', '.') }} VNĐ
                        </button>
                        <p class="secure-hint">🔒 Bảo mật theo tiêu chuẩn PCI DSS · Mã hóa SSL 256-bit</p>
                    </div>
                </div>

                @elseif($payMethod === 'atm_international')
                {{-- ===== ATM QUỐC TẾ ===== --}}
                <div class="card-form-box international">
                    <div class="card-form-header">
                        <span class="card-type-icon">💳</span>
                        <div>
                            <h2>Thẻ ATM Quốc Tế</h2>
                            <small>Visa · Mastercard · JCB · American Express · UnionPay</small>
                        </div>
                        <div class="intl-card-badges">
                            <span class="i-chip visa">VISA</span>
                            <span class="i-chip master">MC</span>
                            <span class="i-chip jcb">JCB</span>
                            <span class="i-chip amex">Amex</span>
                        </div>
                    </div>

                    {{-- Hình minh hoạ thẻ quốc tế --}}
                    <div class="card-visual-wrap">
                        <div class="card-3d international-card">
                            <div class="card-top-row">
                                <div class="card-bank-name" style="color:rgba(255,255,255,0.7);">INTERNATIONAL CARD</div>
                                <div class="card-chip-icon" style="color:#ffd700;">▣</div>
                            </div>
                            <div class="card-number-display" id="previewIntlNum">•••• •••• •••• ••••</div>
                            <div class="card-bottom-row">
                                <div>
                                    <div class="card-field-label">Cardholder</div>
                                    <div class="card-field-val" id="previewIntlName">YOUR NAME</div>
                                </div>
                                <div>
                                    <div class="card-field-label">Expires</div>
                                    <div class="card-field-val" id="previewIntlExpiry">MM/YY</div>
                                </div>
                                <div style="margin-left:auto;">
                                    <span class="i-chip visa" style="font-size:0.85rem;padding:4px 10px;">VISA</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-input-section">
                        <div class="input-group full-w">
                            <label for="intl_card_num">Số thẻ <span class="req">*</span></label>
                            <input type="text" id="intl_card_num" placeholder="0000 0000 0000 0000" maxlength="19" class="card-field-input" oninput="formatCardNumber(this); updateIntlPreview()">
                        </div>
                        <div class="input-row-2">
                            <div class="input-group">
                                <label for="intl_expiry">Ngày hết hạn (MM/YY) <span class="req">*</span></label>
                                <input type="text" id="intl_expiry" placeholder="MM/YY" maxlength="5" class="card-field-input" oninput="formatExpiry(this); updateIntlPreview()">
                            </div>
                            <div class="input-group">
                                <label for="intl_cvv">CVV / CVC <span class="req">*</span>
                                    <span class="cvv-hint" title="3 số mặt sau thẻ (Amex: 4 số mặt trước)">?</span>
                                </label>
                                <input type="password" id="intl_cvv" placeholder="•••" maxlength="4" class="card-field-input">
                            </div>
                        </div>
                        <div class="input-group full-w">
                            <label for="intl_name">Tên chủ thẻ (in hoa, như in trên thẻ) <span class="req">*</span></label>
                            <input type="text" id="intl_name" placeholder="VD: NGUYEN THI THU HA" class="card-field-input" style="text-transform:uppercase;" oninput="updateIntlPreview()">
                        </div>

                        <div class="secure-3d-banner">
                            <span>🔐</span>
                            <div>
                                <strong>Xác thực 3D Secure</strong>
                                <p>Sau khi xác nhận, bạn sẽ được chuyển sang trang xác thực OTP của ngân hàng phát hành thẻ.</p>
                            </div>
                        </div>

                        <button class="btn-pay-card international-btn" onclick="handleCardPay(event, 'international')">
                            🔒 Thanh toán {{ number_format($order->total_price, 0, ',', '.') }} VNĐ
                        </button>
                        <p class="secure-hint">🔒 Bảo mật 3D Secure · PCI DSS Level 1 · SSL 256-bit · Powered by Stripe</p>
                    </div>
                </div>
                @endif

            </div>

            {{-- Cột phải: Tóm tắt đơn hàng --}}
            <div class="card-pay-side">
                <div class="card-summary-box">
                    <h3 class="sum-title">📦 Đơn hàng #{{ $order->id }}</h3>

                    <div class="sum-items">
                        @foreach($order->items as $item)
                            @php $prod = $item->product ?? $item->perfume; @endphp
                            <div class="sum-row">
                                <div class="sum-thumb">
                                    @if($prod && $prod->image_src)
                                        <img src="{{ $prod->image_src }}" alt="{{ $prod->name }}">
                                    @else
                                        <span>🌸</span>
                                    @endif
                                    <span class="sum-qty">{{ $item->quantity }}</span>
                                </div>
                                <div class="sum-info">
                                    <span class="sum-name">{{ $prod->name ?? 'Nước hoa' }}</span>
                                    <span class="sum-meta">{{ $item->volume_ml ?? 100 }}ml</span>
                                </div>
                                <span class="sum-price">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</span>
                            </div>
                        @endforeach
                    </div>

                    @php $itemsTotal = $order->items->sum(fn($i) => $i->price * $i->quantity); @endphp
                    <div class="sum-costs">
                        <div class="sum-cost-row">
                            <span>Tiền hàng</span>
                            <span>{{ number_format($itemsTotal, 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div class="sum-cost-row">
                            <span>Phí vận chuyển (GHN)</span>
                            <span>{{ number_format($order->ghn_total_fee ?? 0, 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div class="sum-cost-row sum-total">
                            <span>Tổng thanh toán</span>
                            <strong class="sum-total-amt">{{ number_format($order->total_price, 0, ',', '.') }} VNĐ</strong>
                        </div>
                    </div>

                    <div class="sum-ship-info">
                        <h4>📬 Giao đến</h4>
                        <p><strong>{{ $order->name }}</strong></p>
                        <p>📞 {{ $order->phone }}</p>
                        <p>📍 {{ $order->address }}</p>
                    </div>

                    <div class="sum-method-tag">
                        @if($payMethod === 'atm_domestic')
                            🏧 <strong>Thẻ ATM Nội Địa</strong>
                        @else
                            💳 <strong>Thẻ ATM Quốc Tế (Visa/Mastercard/JCB)</strong>
                        @endif
                    </div>

                    <a href="{{ route('orders.index') }}" class="btn-view-orders-link">Xem lịch sử đơn hàng</a>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.card-pay-page { padding: 32px 0 80px; background: #faf8f9; min-height: 80vh; }
.card-breadcrumb { display: flex; gap: 8px; font-size: 0.85rem; color: #6b7280; margin-bottom: 24px; }
.card-breadcrumb a { color: #4b5563; text-decoration: none; }
.card-breadcrumb a:hover { color: #db2777; }
.card-breadcrumb .active { color: #db2777; font-weight: 600; }
.card-pay-header { display: flex; align-items: center; gap: 18px; margin-bottom: 30px; }
.card-success-icon { font-size: 3rem; }
.card-badge-tag { display: inline-block; background: #d1fae5; color: #065f46; font-size: 0.75rem; font-weight: 700; padding: 3px 10px; border-radius: 9999px; margin-bottom: 5px; }
.card-pay-header h1 { font-family: 'Playfair Display', Georgia, serif; font-size: 1.9rem; font-weight: 600; color: #111827; margin: 0 0 3px; }
.card-pay-header p { color: #6b7280; margin: 0; font-size: 0.93rem; }
.amount-highlight { color: #db2777; font-size: 1.05rem; }
.card-pay-grid { display: grid; grid-template-columns: 1fr 330px; gap: 28px; align-items: flex-start; }

/* Card Form Box */
.card-form-box { background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 1px solid #f0e8f3; }
.card-form-header { display: flex; align-items: center; gap: 14px; padding: 20px 24px; border-bottom: 1px solid #f5f5f5; }
.card-form-box.domestic .card-form-header { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); }
.card-form-box.international .card-form-header { background: linear-gradient(135deg, #1a1f71 0%, #2e77bc 100%); }
.card-form-box.international .card-form-header h2 { color: #fff; }
.card-form-box.international .card-form-header small { color: #c7d2fe; }
.card-type-icon { font-size: 2rem; }
.card-form-header h2 { font-size: 1.05rem; font-weight: 700; color: #1f2937; margin: 0; }
.card-form-header small { color: #6b7280; font-size: 0.78rem; }
.domestic-bank-badges, .intl-card-badges { margin-left: auto; display: flex; gap: 4px; flex-wrap: wrap; }
.b-chip { font-size: 0.65rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; }
.b-chip.vcb  { background: #dcfce7; color: #166534; }
.b-chip.bidv { background: #fee2e2; color: #991b1b; }
.b-chip.tcb  { background: #fce7f3; color: #9d174d; }
.b-chip.mb   { background: #ede9fe; color: #4c1d95; }
.i-chip { font-size: 0.65rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; }
.i-chip.visa   { background: #1a1f71; color: #fff; }
.i-chip.master { background: #eb001b; color: #fff; }
.i-chip.jcb    { background: #003087; color: #fff; }
.i-chip.amex   { background: #2e77bc; color: #fff; }

/* Card visual preview */
.card-visual-wrap { padding: 24px 24px 0; display: flex; justify-content: center; }
.card-3d {
    width: 100%; max-width: 360px; border-radius: 18px; padding: 22px 24px;
    color: #fff; box-shadow: 0 12px 32px rgba(0,0,0,0.2);
    background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
    transition: transform 0.3s;
}
.card-3d:hover { transform: perspective(600px) rotateY(-5deg) scale(1.02); }
.domestic-card { background: linear-gradient(135deg, #1d4ed8 0%, #60a5fa 100%); }
.international-card { background: linear-gradient(135deg, #1a1f71 0%, #2e77bc 80%, #1a1f71 100%); }
.card-top-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; }
.card-bank-name { font-size: 0.72rem; font-weight: 600; opacity: 0.8; letter-spacing: 0.05em; }
.card-chip-icon { font-size: 1.4rem; }
.card-number-display { font-family: 'Courier New', monospace; font-size: 1.35rem; font-weight: 600; letter-spacing: 0.12em; margin-bottom: 20px; text-shadow: 0 1px 3px rgba(0,0,0,0.3); }
.card-bottom-row { display: flex; gap: 24px; align-items: flex-end; }
.card-field-label { font-size: 0.62rem; opacity: 0.65; text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 3px; }
.card-field-val { font-size: 0.88rem; font-weight: 600; font-family: 'Courier New', monospace; }

/* Input section */
.card-input-section { padding: 24px; display: flex; flex-direction: column; gap: 16px; }
.input-group { display: flex; flex-direction: column; gap: 5px; }
.input-group.full-w { width: 100%; }
.input-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.input-group label { font-size: 0.82rem; font-weight: 600; color: #374151; display: flex; align-items: center; gap: 4px; }
.req { color: #e11d48; }
.cvv-hint { width: 16px; height: 16px; background: #e5e7eb; border-radius: 50%; font-size: 0.68rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; cursor: help; color: #6b7280; }
.card-field-input {
    padding: 11px 14px; border: 1.5px solid #e5e7eb; border-radius: 10px;
    font-size: 0.92rem; font-family: 'Courier New', monospace; outline: none;
    transition: all 0.2s; background: #fafafa;
}
.card-field-input:focus { border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }
.card-form-box.international .card-field-input:focus { border-color: #1a1f71; box-shadow: 0 0 0 3px rgba(26,31,113,0.12); }

.otp-row { display: flex; gap: 10px; }
.otp-row .card-field-input { flex: 1; }
.btn-send-otp { padding: 11px 16px; background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 10px; color: #1d4ed8; font-weight: 700; font-size: 0.82rem; cursor: pointer; white-space: nowrap; transition: all 0.2s; }
.btn-send-otp:hover { background: #dbeafe; }
.btn-send-otp:disabled { background: #f3f4f6; color: #9ca3af; border-color: #e5e7eb; cursor: not-allowed; }

.secure-3d-banner { display: flex; align-items: flex-start; gap: 12px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 14px 16px; }
.secure-3d-banner span { font-size: 1.5rem; flex-shrink: 0; }
.secure-3d-banner strong { font-size: 0.88rem; color: #1e3a8a; }
.secure-3d-banner p { margin: 4px 0 0; font-size: 0.78rem; color: #3b82f6; }

.btn-pay-card {
    width: 100%; padding: 15px; border: none; border-radius: 12px;
    font-weight: 700; font-size: 0.95rem; cursor: pointer;
    transition: all 0.25s; letter-spacing: 0.02em;
}
.domestic-btn { background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%); color: #fff; box-shadow: 0 4px 16px rgba(29,78,216,0.3); }
.domestic-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(29,78,216,0.4); }
.international-btn { background: linear-gradient(135deg, #1a1f71 0%, #2e77bc 100%); color: #fff; box-shadow: 0 4px 16px rgba(26,31,113,0.3); }
.international-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(26,31,113,0.4); }
.secure-hint { text-align: center; font-size: 0.74rem; color: #9ca3af; margin: 4px 0 0; }

/* Summary box */
.card-summary-box { background: #fff; border-radius: 16px; padding: 22px; border: 1px solid #f0e8f3; box-shadow: 0 4px 20px rgba(219,39,119,0.05); position: sticky; top: 90px; }
.sum-title { font-size: 1rem; font-weight: 700; color: #111827; margin: 0 0 14px; }
.sum-items { max-height: 200px; overflow-y: auto; margin-bottom: 14px; }
.sum-row { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px dashed #f3f4f6; }
.sum-thumb { width: 40px; height: 40px; border-radius: 8px; background: #fafafa; border: 1px solid #f3e8ee; flex-shrink: 0; position: relative; display: flex; align-items: center; justify-content: center; }
.sum-thumb img { width: 100%; height: 100%; object-fit: cover; border-radius: 7px; }
.sum-qty { position: absolute; top: -5px; right: -5px; background: #db2777; color: #fff; font-size: 0.62rem; font-weight: 700; width: 15px; height: 15px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.sum-info { flex: 1; min-width: 0; }
.sum-name { display: block; font-size: 0.82rem; font-weight: 600; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sum-meta { font-size: 0.72rem; color: #9ca3af; }
.sum-price { font-size: 0.82rem; font-weight: 600; color: #374151; white-space: nowrap; }
.sum-costs { border-top: 1px solid #f3f4f6; padding-top: 12px; margin-bottom: 14px; display: flex; flex-direction: column; gap: 8px; }
.sum-cost-row { display: flex; justify-content: space-between; font-size: 0.84rem; color: #4b5563; }
.sum-total { border-top: 1px dashed #e5e7eb; padding-top: 8px; font-weight: 600; }
.sum-total-amt { color: #db2777; font-size: 1.05rem; }
.sum-ship-info { background: #f9fafb; border-radius: 10px; padding: 12px 14px; margin-bottom: 14px; font-size: 0.82rem; }
.sum-ship-info h4 { font-size: 0.84rem; font-weight: 700; color: #374151; margin: 0 0 7px; }
.sum-ship-info p { margin: 2px 0; color: #4b5563; }
.sum-method-tag { background: #fdf2f8; border: 1px solid #fce7f3; border-radius: 8px; padding: 10px 14px; font-size: 0.83rem; color: #7e1450; margin-bottom: 14px; }
.btn-view-orders-link { display: block; text-align: center; padding: 11px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; color: #374151; font-weight: 600; font-size: 0.87rem; text-decoration: none; transition: background 0.2s; }
.btn-view-orders-link:hover { background: #f3f4f6; }

@media (max-width: 900px) {
    .card-pay-grid { grid-template-columns: 1fr; }
    .card-summary-box { position: static; }
    .input-row-2 { grid-template-columns: 1fr; }
}
</style>

<script>
// Cập nhật preview thẻ nội địa
function updatePreview() {
    const num = document.getElementById('dom_card_num');
    const name = document.getElementById('dom_name');
    const exp = document.getElementById('dom_expiry');
    if (num) document.getElementById('previewCardNum').textContent = (num.value || '•••• •••• •••• ••••').padEnd(19, '•').substring(0, 19);
    if (name) document.getElementById('previewName').textContent = (name.value.toUpperCase() || 'TÊN CHỦ THẺ').substring(0, 22);
    if (exp) document.getElementById('previewExpiry').textContent = exp.value || 'MM/YY';
}
// Cập nhật preview thẻ quốc tế
function updateIntlPreview() {
    const num = document.getElementById('intl_card_num');
    const name = document.getElementById('intl_name');
    const exp = document.getElementById('intl_expiry');
    if (num) document.getElementById('previewIntlNum').textContent = (num.value || '•••• •••• •••• ••••');
    if (name) document.getElementById('previewIntlName').textContent = (name.value.toUpperCase() || 'YOUR NAME').substring(0, 22);
    if (exp) document.getElementById('previewIntlExpiry').textContent = exp.value || 'MM/YY';
}
// Format số thẻ thành nhóm 4 số
function formatCardNumber(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 16);
    input.value = v.replace(/(.{4})/g, '$1 ').trim();
}
// Format MM/YY
function formatExpiry(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 4);
    if (v.length > 2) v = v.substring(0, 2) + '/' + v.substring(2);
    input.value = v;
}
// Gửi OTP (demo)
function sendOtp(btn) {
    btn.disabled = true;
    btn.textContent = '60s';
    let s = 60;
    const t = setInterval(() => {
        s--;
        btn.textContent = s + 's';
        if (s <= 0) {
            clearInterval(t);
            btn.disabled = false;
            btn.textContent = 'Gửi lại';
        }
    }, 1000);
    // Demo: chỉ alert thông báo
    setTimeout(() => alert('Demo: Mã OTP 123456 đã được gửi về số điện thoại đăng ký thẻ của bạn.'), 500);
}
// Xử lý thanh toán
function handleCardPay(e, type) {
    e.preventDefault();
    const btn = e.target;
    btn.textContent = '⏳ Đang xác thực thẻ & ngân hàng...';
    btn.disabled = true;

    const gw = type === 'domestic' ? 'atm_domestic' : 'atm_international';
    document.getElementById('formGateway').value = gw;

    const cardNum = type === 'domestic' 
        ? (document.getElementById('dom_card_num')?.value || '') 
        : (document.getElementById('intl_card_num')?.value || '');
    document.getElementById('formCardNumber').value = cardNum;

    setTimeout(() => {
        btn.textContent = '✅ Đang xử lý kết quả giao dịch...';
        document.getElementById('confirmPayForm').submit();
    }, 1000);
}
</script>

{{-- Form ẩn gửi xác nhận thanh toán lên server --}}
<form id="confirmPayForm" method="POST" action="{{ route('user.orders.confirm.payment', $order) }}" style="display:none;">
    @csrf
    <input type="hidden" name="gateway" id="formGateway" value="{{ $payMethod }}">
    <input type="hidden" name="card_number" id="formCardNumber" value="">
</form>
@endsection
