<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Perfume;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const TABS = [
        'all' => ['label' => 'Tất cả', 'color' => 'blue', 'statuses' => []],
        'pending' => ['label' => 'Chờ xử lý', 'color' => 'slate', 'statuses' => ['pending', 'not_shipped', 'processing']],
        'ready' => ['label' => 'Chờ lấy hàng', 'color' => 'cyan', 'statuses' => ['ready_to_pick']],
        'picking' => ['label' => 'Đang lấy hàng', 'color' => 'cyan', 'statuses' => ['picking']],
        'delivering' => ['label' => 'Đang giao', 'color' => 'amber', 'statuses' => ['delivering', 'picked', 'storing', 'transporting', 'sorting']],
        'delivered' => ['label' => 'Thành công', 'color' => 'green', 'statuses' => ['delivered']],
        'return' => ['label' => 'Hoàn hàng', 'color' => 'orange', 'statuses' => ['return', 'returning', 'returned', 'return_transporting', 'return_sorting']],
        'cancelled' => ['label' => 'Đã hủy', 'color' => 'red', 'statuses' => ['cancelled']],
    ];

    public function index(Request $request): View
    {
        $paymentLabels = [
            'pending' => 'Chờ thanh toán',
            'initiated' => 'Đang chờ MoMo',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            'cancelled' => 'Đã hủy',
            'refund_pending' => 'Chờ hoàn tiền',
            'refunded' => 'Đã hoàn tiền',
        ];

        $shippingLabels = [
            'pending' => 'Chờ tạo vận đơn',
            'not_shipped' => 'Chưa giao hàng',
            'processing' => 'Đang tạo vận đơn',
            'ready_to_pick' => 'Chờ lấy hàng',
            'picking' => 'Đang lấy hàng',
            'picked' => 'Đã lấy hàng',
            'storing' => 'Đang lưu kho',
            'transporting' => 'Đang trung chuyển',
            'sorting' => 'Đang phân loại',
            'delivering' => 'Đang giao hàng',
            'delivered' => 'Giao hàng thành công',
            'return' => 'Chờ hoàn hàng',
            'returning' => 'Đang hoàn hàng',
            'returned' => 'Đã hoàn hàng',
            'return_transporting' => 'Đang chuyển hoàn',
            'return_sorting' => 'Đang phân loại hoàn',
            'cancelled' => 'Đã hủy',
        ];

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['pending', 'paid', 'paid_momo', 'cod_ordered', 'cod_paid', 'cancelled', 'confirmed', 'completed'])],
            'payment_status' => ['nullable', Rule::in(array_keys($paymentLabels))],
            'shipping_status' => ['nullable', Rule::in(array_keys($shippingLabels))],
            'gateway' => ['nullable', Rule::in(['cod', 'momo', 'unknown'])],
            'tab' => ['nullable', Rule::in(array_keys(self::TABS))],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('date_from') ? ['after_or_equal:date_from'] : [])],
            'per_page' => ['nullable', 'integer', Rule::in([25, 50, 100])],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'amount_desc', 'amount_asc'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ], [
            'date_to.after_or_equal' => 'Ngày kết thúc phải từ ngày bắt đầu trở đi.',
            '*.date_format' => 'Ngày lọc không hợp lệ.',
            '*.in' => 'Giá trị bộ lọc không hợp lệ.',
        ]);

        $paymentId = DB::table('payment_transactions')->select('id')->whereColumn('order_id', 'orders.id')
            ->orderByRaw("CASE WHEN status IN ('paid', 'refund_pending', 'refunded') THEN 0 ELSE 1 END")
            ->orderByDesc('id')->limit(1);

        $source = DB::table('orders')->leftJoin('payment_transactions as payment', function ($join) use ($paymentId) {
            $join->on('payment.order_id', '=', 'orders.id')->where('payment.id', '=', $paymentId);
        })->select('orders.*')
        ->selectRaw("COALESCE(payment.gateway, CASE WHEN orders.status IN ('cod_ordered', 'cod_paid') THEN 'cod' WHEN orders.status IN ('paid', 'paid_momo') THEN 'momo' ELSE 'unknown' END) as gateway")
        ->selectRaw("COALESCE(payment.status, CASE WHEN orders.status = 'cod_ordered' THEN 'pending' WHEN orders.status IN ('cod_paid', 'paid_momo') THEN 'paid' ELSE orders.status END) as payment_status");

        $query = Order::query()->fromSub($source, 'orders');

        foreach (['status', 'payment_status', 'gateway'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $filters[$field]);
            }
        }

        if ($request->filled('search')) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                  ->orWhere('customer_name', 'like', '%'.$search.'%')
                  ->orWhere('phone', 'like', '%'.$search.'%')
                  ->orWhere('ghn_order_code', 'like', '%'.$search.'%')
                  ->orWhereHas('items.perfume', fn ($perfumes) => $perfumes->where('name', 'like', '%'.$search.'%'));

                if (preg_match('/^(?:#|DH)?0*(\d+)$/i', $search, $matches)) {
                    $q->orWhere('orders.id', $matches[1]);
                }
            });
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<', Carbon::parse($filters['date_to'])->addDay()->startOfDay());
        }

        // Số trên tab theo bộ lọc chung, không bị giới hạn bởi trang hiện tại.
        $shippingCounts = (clone $query)->select('shipping_status')->selectRaw('COUNT(*) as total')
            ->groupBy('shipping_status')->pluck('total', 'shipping_status');

        $tabs = collect(self::TABS)->map(function ($tab, $key) use ($shippingCounts) {
            $tab['count'] = $key === 'all'
                ? $shippingCounts->sum()
                : collect($tab['statuses'])->sum(fn ($status) => $shippingCounts->get($status, 0));
            return $tab;
        });

        $activeTab = $filters['tab'] ?? 'all';
        if ($activeTab !== 'all') {
            $query->whereIn('shipping_status', self::TABS[$activeTab]['statuses']);
        }

        if ($request->filled('shipping_status')) {
            $query->where('shipping_status', $filters['shipping_status']);
        }

        [$column, $direction] = match ($filters['sort'] ?? 'newest') {
            'oldest' => ['created_at', 'asc'],
            'amount_desc' => ['total_price', 'desc'],
            'amount_asc' => ['total_price', 'asc'],
            default => ['created_at', 'desc'],
        };

        $orders = $query->with(['items.perfume', 'user'])
            ->orderBy($column, $direction)
            ->orderBy('id', $direction)
            ->paginate((int) ($filters['per_page'] ?? 25))
            ->withQueryString();

        $viewName = view()->exists('admin.orders.index') ? 'admin.orders.index' : 'admin.order.index';

        return view($viewName, compact(
            'orders',
            'filters',
            'tabs',
            'activeTab',
            'paymentLabels',
            'shippingLabels'
        ));
    }

    public function show($id): View
    {
        $order = Order::with(['user', 'items.perfume', 'paymentTransactions' => function ($query) {
            $query->latest();
        }])->findOrFail($id);

        $viewName = view()->exists('admin.orders.show') ? 'admin.orders.show' : 'admin.order.show';

        return view($viewName, compact('order'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => ['nullable', 'string'],
            'shipping_status' => ['nullable', 'string'],
        ]);

        $newStatus = $request->input('status');
        $newShippingStatus = $request->input('shipping_status');

        // Logic Lab 8: "Nếu đơn hàng ở trạng thái đang giao -> KHÔNG cho Hủy"
        $deliveringStatuses = ['delivering', 'picked', 'storing', 'transporting', 'sorting'];
        if (($newStatus === 'cancelled' || $newShippingStatus === 'cancelled') && in_array($order->shipping_status, $deliveringStatuses)) {
            return back()->with('error', 'Đơn hàng đang giao, không thể hủy!');
        }

        $oldStatus = $order->status;

        try {
            DB::transaction(function () use ($order, $oldStatus, $newStatus, $newShippingStatus) {
                if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                    foreach ($order->items as $item) {
                        if ($item->perfume) {
                            $item->perfume->increment('stock', $item->quantity);
                        }
                    }
                } elseif ($oldStatus === 'cancelled' && $newStatus && $newStatus !== 'cancelled') {
                    foreach ($order->items as $item) {
                        if ($item->perfume) {
                            $perfume = Perfume::query()->lockForUpdate()->find($item->perfume_id);
                            if (!$perfume || $perfume->stock < $item->quantity) {
                                throw new \Exception("Sản phẩm '{$item->perfume->name}' không đủ tồn kho để khôi phục.");
                            }
                            $perfume->decrement('stock', $item->quantity);
                        }
                    }
                }

                $updates = [];
                if ($newStatus) $updates['status'] = $newStatus;
                if ($newShippingStatus) $updates['shipping_status'] = $newShippingStatus;

                if (!empty($updates)) {
                    $order->update($updates);
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }

    /**
     * Yêu cầu đặc biệt của người dùng: Cập nhật trạng thái hàng loạt theo các checkbox đã tích
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'order_ids' => ['required', 'array', 'min:1'],
            'order_ids.*' => ['integer', 'exists:orders,id'],
            'bulk_status' => ['nullable', 'string'],
            'bulk_shipping_status' => ['nullable', 'string'],
        ]);

        $orderIds = $request->input('order_ids', []);
        $bulkStatus = $request->input('bulk_status');
        $bulkShippingStatus = $request->input('bulk_shipping_status');

        if (!$bulkStatus && !$bulkShippingStatus) {
            return back()->with('error', 'Vui lòng chọn trạng thái cần cập nhật hàng loạt.');
        }

        $deliveringStatuses = ['delivering', 'picked', 'storing', 'transporting', 'sorting'];
        $updatedCount = 0;
        $skippedDeliveringCount = 0;

        $orders = Order::whereIn('id', $orderIds)->get();

        foreach ($orders as $order) {
            // Kiểm tra ràng buộc Lab 8: Không cho hủy nếu đang giao
            if (($bulkStatus === 'cancelled' || $bulkShippingStatus === 'cancelled') && in_array($order->shipping_status, $deliveringStatuses)) {
                $skippedDeliveringCount++;
                continue;
            }

            $oldStatus = $order->status;

            DB::transaction(function () use ($order, $oldStatus, $bulkStatus, $bulkShippingStatus) {
                if ($bulkStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                    foreach ($order->items as $item) {
                        if ($item->perfume) {
                            $item->perfume->increment('stock', $item->quantity);
                        }
                    }
                }

                $data = [];
                if ($bulkStatus) $data['status'] = $bulkStatus;
                if ($bulkShippingStatus) $data['shipping_status'] = $bulkShippingStatus;

                $order->update($data);
            });

            $updatedCount++;
        }

        $message = "Đã cập nhật trạng thái thành công cho {$updatedCount} đơn hàng.";
        if ($skippedDeliveringCount > 0) {
            $message .= " (Bỏ qua {$skippedDeliveringCount} đơn hàng đang giao vì không thể hủy theo quy định).";
        }

        return back()->with('success', $message);
    }
}
