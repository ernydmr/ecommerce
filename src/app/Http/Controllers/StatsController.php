<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Support\ApiResponse;

class StatsController extends Controller
{
    public function index()
    {
        $stats = [
            'users'         => User::count(),
            'orders'        => Order::count(),
            'revenue_paid'  => (float) Order::where('status', 'PAID')->sum('total_amount'),
            'products'      => Product::count(),
            'today_orders'  => Order::whereDate('created_at', now()->toDateString())->count(),
        ];

        return ApiResponse::success($stats, 'İstatistikler');
    }
}
