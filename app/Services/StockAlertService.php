<?php

namespace App\Services;

use App\Mail\BackInStockMail;
use App\Models\Perfume;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class StockAlertService
{
    public static function notifyIfRestocked(Perfume|Product $perfume, int $previousStock): void
    {
        if ($previousStock > 0 || $perfume->stock <= 0 || ! $perfume->is_active) return;

        $alerts = DB::table('stock_alerts')->join('users', 'users.id', '=', 'stock_alerts.user_id')
            ->where('stock_alerts.perfume_id', $perfume->id)->whereNull('stock_alerts.notified_at')
            ->select('stock_alerts.id', 'users.email')->get();

        foreach ($alerts as $alert) {
            try {
                Mail::to($alert->email)->send(new BackInStockMail($perfume));
                DB::table('stock_alerts')->where('id', $alert->id)->update(['notified_at' => now()]);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }
    }
}
