<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'minimum_order', 'usage_limit', 'starts_at', 'expires_at', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'starts_at' => 'datetime', 'expires_at' => 'datetime'];
    }

    public function discountFor(int $subtotal): int
    {
        return min($subtotal, $this->type === 'percent'
            ? (int) floor($subtotal * min(100, $this->value) / 100)
            : (int) $this->value);
    }

    public function isAvailableFor(int $subtotal): bool
    {
        return $this->is_active
            && $subtotal >= $this->minimum_order
            && (! $this->starts_at || $this->starts_at->isPast())
            && (! $this->expires_at || $this->expires_at->isFuture())
            && ($this->usage_limit === null || Order::where('coupon_code', $this->code)->where('status', '!=', 'cancelled')->count() < $this->usage_limit);
    }
}
