<?php

namespace App\Http\Controllers\v1\App;

use App\Filters\ProductFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\App\Category\CategoryCollection;
use App\Http\Resources\v1\App\Category\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request, ProductFilter $filter)
    {
        $categories = Category::root()
            ->active()
            ->where(function ($query) use ($filter) {
                $query->whereHas('products', function ($subquery) use ($filter) {
                    $subquery->active()->filter($filter);
                })->orWhere(function ($subquery) use ($filter) {
                    $subquery->whereHas('childrenRecursive', function ($subquery2) use ($filter) {
                        $subquery2->active()->whereHas('products', function ($subquery3) use ($filter) {
                            $subquery3->active()->filter($filter);
                        });
                    });
                });
            })->paginate($request->per_page ?? self::PER_PAGE);

        return new CategoryCollection($categories);
    }

    public function show(Category $category, ProductFilter $filter)
    {
        $categories = Category::whereParentId($category->id)
            ->active()
            ->where(function ($query) use ($filter) {
                $query->whereHas('products', function ($subquery) use ($filter) {
                    $subquery->active()->filter($filter);
                })->orWhere(function ($subquery) use ($filter) {
                    $subquery->whereHas('childrenRecursive', function ($subquery2) use ($filter) {
                        $subquery2->active()->whereHas('products', function ($subquery3) use ($filter) {
                            $subquery3->active()->filter($filter);
                        });
                    });
                });
            })->paginate($request->per_page ?? self::PER_PAGE);

        return new CategoryCollection($categories);
    }

    public function showDetail(Category $category)
    {
        return new CategoryResource($category);
    }
}
