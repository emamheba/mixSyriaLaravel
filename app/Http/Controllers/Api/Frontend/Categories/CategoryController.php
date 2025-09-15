<?php

namespace App\Http\Controllers\Api\Frontend\Categories;

use App\Http\Controllers\Controller;
use App\Http\Resources\Categories\BrandResource;
use App\Http\Resources\Categories\CategoryResource;
use App\Http\Resources\Categories\SubCategoryResource;
use App\Http\Responses\ApiResponse;
use App\Models\Backend\Category;
use Illuminate\Http\Request;
use Modules\Brand\app\Models\Brand;


class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 1)
            ->with([
                'subcategories' => function ($query) {
                    $query->where('status', 1)
                        ->with([
                            'childcategories' => function ($query) {
                                $query->where('status', 1)
                                    ->with([
                                        'brand' => function ($query) {
                                            $query->where('status', 1);
                                        }
                                    ]);
                            },
                            'brand' => function ($query) {
                                $query->where('status', 1);
                            }
                        ]);
                },
                'brands' => function ($query) {
                    $query->where('status', 1);
                }
            ])
            ->get();

        return ApiResponse::success('Categories retrieved', CategoryResource::collection($categories));
    }


    public function getBrands(Request $request)
    {
        $brands = Brand::where('status', 1)->get();
        return ApiResponse::success('Brands retrieved', BrandResource::collection($brands));
    }

    public function getBrandsByCategory(Category $category)
    {
        $brands = $category->brands()->where('status', 1)->get();
        return ApiResponse::success('Brands retrieved', BrandResource::collection($brands));
    }

    public function getSubcategories(Category $category)
    {
        $subcategories = $category->subcategories()
            ->where('status', 1)
            ->with([
                'childcategories' => function ($query) {
                    $query->where('status', 1);
                }
            ])
            ->get();

        return ApiResponse::success('Subcategories retrieved', SubCategoryResource::collection($subcategories));
    }

    public function getCategory(Category $category)
    {
        if ($category->status != 1) {
            return ApiResponse::error('Category not found', 404);
        }

        return ApiResponse::success('Category retrieved', new CategoryResource($category));
    }
}
