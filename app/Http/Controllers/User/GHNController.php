<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\GHNService;
use Illuminate\Http\Request;

class GHNController extends Controller
{
    public function getProvinces(GHNService $ghn)
    {
        return response()->json($ghn->getProvinces());
    }

    public function getDistricts(int $provinceId, GHNService $ghn)
    {
        return response()->json($ghn->getDistricts($provinceId));
    }

    public function getWards(int $districtId, GHNService $ghn)
    {
        return response()->json($ghn->getWards($districtId));
    }

    public function getShippingFee(Request $request, GHNService $ghn)
    {
        $request->validate([
            'to_district_id' => 'required|integer',
            'to_ward_code' => 'required|string',
        ]);

        $cart = session('cart', []);
        
        $perfumeIds = collect($cart)->map(function ($item, $key) {
            return is_array($item) ? ($item['perfume_id'] ?? $item['id'] ?? null) : (int) $key;
        })->filter()->unique()->values();

        $products = \App\Models\Perfume::whereIn('id', $perfumeIds)->get()->keyBy('id');

        $weight = 0;
        foreach ($cart as $key => $item) {
            if (is_array($item)) {
                $perfumeId = (int) ($item['perfume_id'] ?? $item['id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 1);
                $volumeMl = isset($item['volume_ml']) ? (int) $item['volume_ml'] : null;
            } else {
                $perfumeId = (int) $key;
                $quantity = (int) $item;
                $volumeMl = null;
            }

            $product = $products->get($perfumeId);
            if ($product) {
                $weight += $product->getWeightForVolume($volumeMl) * $quantity;
            } else {
                $weight += (int) config('services.ghn.default_weight', 200) * $quantity;
            }
        }

        if ($weight <= 0) {
            $weight = (int) config('services.ghn.default_weight', 200);
        }

        $fromDistrictId = (int) (config('services.ghn.from_district_id') ?: 1493);

        return response()->json($ghn->calculateFee(array_merge([
            'from_district_id' => $fromDistrictId,
            'to_district_id' => (int) $request->to_district_id,
            'to_ward_code' => (string) $request->to_ward_code,
        ], $ghn->packageParameters($weight))));
    }
}
