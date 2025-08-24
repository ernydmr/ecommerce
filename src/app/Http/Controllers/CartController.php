<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartAddRequest;
use App\Http\Requests\CartUpdateRequest;
use App\Http\Resources\CartResource;
use App\Models\{Cart, CartItem, Product};
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // GET /api/cart
    public function getCart(Request $r)
    {
        $cart = Cart::firstOrCreate(['user_id' => $r->user()->id]);
        $cart->load(['items.product.category']);
        return ApiResponse::success(new CartResource($cart));
    }

    // POST /api/cart/add
    public function add(CartAddRequest $req)
    {
        $userId = $req->user()->id;
        $data   = $req->validated();

        $cart    = Cart::firstOrCreate(['user_id' => $userId]);
        $product = Product::findOrFail($data['product_id']);

        // aynı ürün sepette varsa miktarı artır
        $item = CartItem::firstOrNew([
            'cart_id'    => $cart->id,
            'product_id' => $data['product_id'],
        ]);

        $newQty = ($item->exists ? $item->quantity : 0) + $data['quantity'];

        // stok kontrolü
        if ($product->stock_quantity < $newQty) {
            return ApiResponse::error('Stok yetersiz', [
                'quantity' => ['Mevcut stok: '.$product->stock_quantity]
            ], 422);
        }

        $item->quantity = $newQty;
        $item->save();

        $cart->load(['items.product.category']);
        return ApiResponse::success(new CartResource($cart), 'Sepete eklendi', 201);
    }

    // PUT /api/cart/update
    public function updateQty(CartUpdateRequest $req)
    {
        $userId = $req->user()->id;
        $data   = $req->validated();

        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        $item = $cart->items()->where('product_id', $data['product_id'])->first();
        if (!$item) {
            return ApiResponse::error('Ürün sepette yok', [], 404);
        }

        // 0 gelirse ürünü sepetten kaldır
        if ((int)$data['quantity'] === 0) {
            $item->delete();
        } else {
            // güncellemeden önce stok kontrolü
            $product = Product::find($data['product_id']);
            if ($product && $product->stock_quantity < (int)$data['quantity']) {
                return ApiResponse::error('Stok yetersiz', [
                    'quantity' => ['Mevcut stok: '.$product->stock_quantity]
                ], 422);
            }
            $item->update(['quantity' => (int)$data['quantity']]);
        }

        $cart->load(['items.product.category']);
        return ApiResponse::success(new CartResource($cart), 'Sepet güncellendi');
    }

    // DELETE /api/cart/remove/{productId}
    public function remove(Request $r, int $productId)
    {
        $cart = Cart::firstOrCreate(['user_id' => $r->user()->id]);
        $deleted = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->delete();

        if (!$deleted) {
            return ApiResponse::error('Ürün sepette yok', [], 404);
        }

        $cart->load(['items.product.category']);
        return ApiResponse::success(new CartResource($cart), 'Ürün çıkarıldı');
    }

    // DELETE /api/cart/clear
    public function clear(Request $r)
    {
        $cart = Cart::firstOrCreate(['user_id' => $r->user()->id]);
        $cart->items()->delete();

        return ApiResponse::success(null, 'Sepet temizlendi');
    }
}
