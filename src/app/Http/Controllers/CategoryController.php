<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Support\ApiResponse;

class CategoryController extends Controller
{
    public function index()
    {
        $cats = Category::query()->orderBy('name')->get();
        return ApiResponse::success(CategoryResource::collection($cats));
    }

    public function store(CategoryStoreRequest $req)
    {
        $cat = Category::create($req->validated());
        return ApiResponse::success(new CategoryResource($cat),
         'Kategori Oluşturuldu',201);
    }

    public function update(CategoryUpdateRequest $req, Category $category)
    {
        $category->update($req->validated());
        return ApiResponse::success(new CategoryResource($category),
         'Kategori Güncellendi', 201);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return ApiResponse::successs(null, 'Kategori Silindi',200);
    }
}
