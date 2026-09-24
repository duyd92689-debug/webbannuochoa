<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GHNWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        Log::info('GHN Webhook received', $request->all());

        $orderCode = $request->input('OrderCode');
        $status = $request->input('Status');

        if ($orderCode) {
            $order = Order::where('ghn_order_code', $orderCode)->first();
            if ($order) {
                // Ánh xạ trạng thái GHN sang shipping_status
                $shippingStatusMap = [
                    'ready_to_pick' => 'ready_to_pick',
                    'picking' => 'picking',
                    'picked' => 'picked',
                    'delivering' => 'delivering',
                    'delivered' => 'delivered',
                    'cancel' => 'cancelled',
                    'return' => 'returned',
                ];

                if (isset($shippingStatusMap[$status])) {
                    $order->update([
                        'shipping_status' => $shippingStatusMap[$status],
                    ]);
                }
            }
        }

        return response()->json(['message' => 'GHN Webhook processed']);
    }
}
