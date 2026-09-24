<?php

namespace App\Services;

use App\Models\Order;

class LoyaltyService
{
    public static function balance(int $userId): int
    {
        $earned = (int) floor(Order::where('user_id', $userId)->where('status', '!=', 'cancelled')->where(function ($query) {
            $query->where('status', 'completed')->orWhere('shipping_status', 'delivered');
        })->sum('total_price') / 100000);
        $spent = (int) Order::where('user_id', $userId)->where('status', '!=', 'cancelled')->sum('points_used');
        return max(0, $earned - $spent);
    }

    public static function totalSpent(int $userId): float
    {
        return (float) Order::where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');
    }

    public static function tier(int $userId): array
    {
        $spent = self::totalSpent($userId);

        if ($spent >= 5000000) {
            return [
                'code' => 'premium',
                'name' => 'Hoàng Gia (Premium VIP)',
                'badge' => '👑',
                'color' => '#d4af37',
                'gradient' => 'linear-gradient(135deg, #1f1b24 0%, #3e3221 50%, #d4af37 100%)',
                'text_color' => '#fef3c7',
                'point_rate' => '10% (100.000đ tích 10 điểm)',
                'discount_percent' => 10,
                'progress_percent' => 100,
                'next_tier' => null,
                'needed_amount' => 0,
                'spent' => $spent,
                'perks' => [
                    'Giảm trực tiếp 10% mọi đơn hàng',
                    'Tích lũy 10% điểm thưởng cho mỗi hóa đơn',
                    'Miễn phí 100% Gói quà cao cấp & Thiệp chúc mừng',
                    'Miễn phí vận chuyển Hỏa Tốc toàn quốc',
                    'Tặng 02 Sample độc quyền khi ra mắt bộ sưu tập mới',
                    'Hỗ trợ Fragrance Concierge 1-1 chuyên biệt',
                ],
            ];
        }

        if ($spent >= 1500000) {
            $nextNeeded = 5000000 - $spent;
            $progress = round((($spent - 1500000) / 3500000) * 100);

            return [
                'code' => 'rose',
                'name' => 'Hoa Hồng (Rose Member)',
                'badge' => '🌹',
                'color' => '#c2476a',
                'gradient' => 'linear-gradient(135deg, #4a1525 0%, #872341 50%, #c2476a 100%)',
                'text_color' => '#ffe4e6',
                'point_rate' => '5% (100.000đ tích 5 điểm)',
                'discount_percent' => 5,
                'progress_percent' => max(5, min(95, $progress)),
                'next_tier' => 'Hoàng Gia (Premium VIP)',
                'needed_amount' => $nextNeeded,
                'spent' => $spent,
                'perks' => [
                    'Giảm trực tiếp 5% mọi đơn hàng',
                    'Tích lũy 5% điểm thưởng mua hàng',
                    'Tặng 01 Sample chiết cao cấp mỗi đơn hàng',
                    'Ưu đãi đặc biệt giảm 15% trong tháng sinh nhật',
                    'Đổi quà điểm thưởng không giới hạn',
                ],
            ];
        }

        $nextNeeded = 1500000 - $spent;
        $progress = round(($spent / 1500000) * 100);

        return [
            'code' => 'silver',
            'name' => 'Bạc (Silver Member)',
            'badge' => '🥈',
            'color' => '#718096',
            'gradient' => 'linear-gradient(135deg, #2d3748 0%, #4a5568 50%, #a0aec0 100%)',
            'text_color' => '#f7fafc',
            'point_rate' => '2% (100.000đ tích 2 điểm)',
            'discount_percent' => 0,
            'progress_percent' => max(5, min(95, $progress)),
            'next_tier' => 'Hoa Hồng (Rose Member)',
            'needed_amount' => $nextNeeded,
            'spent' => $spent,
            'perks' => [
                'Tích lũy 2% điểm thưởng quy đổi tiền mặt',
                'Voucher sinh nhật 50.000đ gửi tặng tự động',
                'Quyền tham gia Vòng quay may mắn mỗi tuần',
                'Nhận thông báo sớm các đợt Flash Sale giới hạn',
            ],
        ];
    }
}
