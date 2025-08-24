<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Services\Contracts\OrderServiceInterface;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderServiceInterface $orders) {}

    public function store(Request $r)
    {
        [$order, $err] = $this->orders->createForUser($r->user()->id);
        if ($err) { return $err; }

        $order->load('items.product.category');
        return ApiResponse::success(new OrderResource($order), 'Sipariş oluşturuldu', 201);
    }

    public function index(Request $r)
    {
        $orders = $r->user()->orders()
            ->with('items.product.category')
            ->orderBy('id','desc')
            ->paginate(20);

        return ApiResponse::success(OrderResource::collection($orders));
    }

    public function updateStatus(\Illuminate\Http\Request $request, \App\Models\Order $order)
{
    $data = $request->validate([
        'status' => 'required|in:PENDING,PAID,SHIPPED,CANCELLED',
    ]);

    $order->status = $data['status'];
    $order->save();

    $order->load(['user','items.product']);

    return ApiResponse::success($order, 'Durum güncellendi');
}


    public function show(Request $r, \App\Models\Order $order)
    {
        if ($order->user_id !== $r->user()->id) {
            return ApiResponse::error('Yetkisiz', [], 403);
        }
        $order->load('items.product.category');
        return ApiResponse::success(new OrderResource($order));
    }
}