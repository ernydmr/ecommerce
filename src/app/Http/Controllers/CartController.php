<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartAddRequest;
use App\Http\Requests\CartUpdateRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Support\ApiResponse;
use Illuminate\Http\Request;


class CartController extends Controller
{
    public function getCart(Request $r)
    {
        $cart = Cart::firstOrCreate(['user_id => $r->user()->id']);
        $cart->load(['items.product.category']);
        return ApiResponse::success(New CartResource($cart));
    }

    public function add(CartAddRequest $req, Request $r)
    {
        $cart =  Cart::firstOrCreate(['user_id' => $r->user()->id]);
        $data =  $req->validated();

        $item = CartItem::firstOrNew([
            'cart_id'    => $cart->id,
            'product_id' => $data['product_id'],
        ]);
        $item->quantity = ($item->exists ? $item->quantity : 0) + $data['quantity'];
        $item->save();

        $cart->load(['items.product.category']);
        return ApiResponse::success(new CartResource($cart), 'Sepete Eklendi', 201);
    }

    public function updateQty(CartUpdateRequest $req, Request $r)
    {
        $cart = Cart::firstOrCreate(['user_id' => $r->user()->id]);
        $data = $req->validated();

        $item = CartItem::where('cart_id', $cart->id)
        ->where('product_id', $data['product_id'])
        ->first();


        if(!$item) {
            return ApiResponse::error('Ürün sepette yok', [], 404);
        }
        $item->quantity = $data['quantity'];
        $item->save();

        $cart->load(['items.product.category']);
        return ApiResponse::success(new CartResource($cart), 'Sepet güncellendi');

    }

    public function remove(Request $r, int $productId)
    {
        $cart = Cart::firstOrCreate(['user_id' => $r->user()->id]);
        CartItem::where('cart_id', $cart->id)->where('product_id', $productId)->delete();
        $cart->load(['items.product.category']);
        return ApiResponse::success(new CartResource($cart), 'Ürün çıkarıldı');
    }


    public function clear (Request $r)
    {
        $cart = Cart::firstOrCreate(['user_id' => $r->user()->id]);
        $cart->items()->delete();
        return ApiResponse::success(null, 'Sepet temizlendi');
    }


}
