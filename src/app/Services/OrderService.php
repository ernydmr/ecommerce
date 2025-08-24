<?php
namespace App\Services;

use App\Models\{Order,OrderItem,Cart,Product};
use App\Services\Contracts\OrderServiceInterface;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\DB;

class OrderService implements OrderServiceInterface
{
    public function createForUser(int $userId): array
{
    $cart = Cart::with('items.product')->firstOrCreate(['user_id'=>$userId]);
    if ($cart->items->isEmpty()) {
        return [null, ApiResponse::error('Sepet boş', [], 400)];
    }

    // transaction sonuçlarını burada yakalayıp dışarıda event fırlatacağız
    [$order, $err] = DB::transaction(function () use ($cart, $userId) {
        // Stok kontrolü
        foreach ($cart->items as $ci) {
            if ($ci->product->stock_quantity < $ci->quantity) {
                return [null, ApiResponse::error("Stok yetersiz: {$ci->product->name}", [], 422)];
            }
        }

        // Sipariş ve kalemler
        $order = Order::create([
            'user_id' => $userId,
            'total_amount' => 0,
            'status' => 'PENDING',
        ]);

        $total = 0;
        foreach ($cart->items as $ci) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $ci->product_id,
                'quantity' => $ci->quantity,
                'price' => $ci->product->price,
            ]);
            $total += $ci->quantity * (float) $ci->product->price;

            // stok düş
            $ci->product->decrement('stock_quantity', $ci->quantity);
        }

        $order->update(['total_amount' => $total]);
        $cart->items()->delete(); // sepeti temizle

        return [$order, null];
    });

    // Transaction başarılı ise, ŞİMDİ event fırlat (commit sonrası)
    if ($err === null && $order) {
        event(new \App\Events\OrderCreated($order));
    }

    return [$order, $err];
}

}
