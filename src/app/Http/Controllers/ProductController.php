<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $r)
    {
            $limit = (int) $r->input('limit',20);
            $limit = max(1, min(100, $limit));

            $q = Product::query()->with('category');

            if($r->filled('category_id')) {
               $q->where('category_id', (int) $r->input('category_id'));
            }

            if($r->filled('min_price')) {
               $q->where('price', '>=', (float) $r->input('min_price'));
            }  

            if($r->filled('max_price')) {
               $q->where('price', '<=', (float) $r->input('max_price'));
            } 

            if ($r->filled('search')) {
            $search = trim((string) $r->input('search'));
            if ($search !== '') {
                $q->where(function($qq) use ($search) {
                    $qq->whereRaw('name ILIKE ?', ["%{$search}%"])
                       ->orWhereRaw('description ILIKE ?', ["%{$search}%"]);
                });
                
        }
    }

    $products = $q->orderBy('id','desc')->paginate($limit);
    return ApiResponse::success(ProductResource::collection($products));
}

  public function show(Product $product)
    {
        $product->load('category');
        return ApiResponse::success(new ProductResource($product));
    }

    public function store(ProductStoreRequest $req)
    {
        $p = Product::create($req->validated());
        return ApiResponse::success(new ProductResource($p), 'Ürün oluşturuldu', 201);
    }

    public function update(ProductUpdateRequest $req, Product $product)
    {
        $product->update($req->validated());
        return ApiResponse::success(new ProductResource($product), 'Ürün güncellendi');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return ApiResponse::success(null, 'Ürün silindi');
    }
}

