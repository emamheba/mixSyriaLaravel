<?php

namespace App\Http\Controllers\Backend\Categories;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\Backend\Category;
use App\Models\Backend\ChildCategory;
use App\Models\Backend\SubCategory;
use App\Traits\CategoryTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Brand\app\Models\Brand;
use App\Actions\Media\v1\MediaHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ChildCategoryController extends Controller
{
    public function __construct(private MediaHelper $mediaHelper)
    {
    }

    use CategoryTrait;
    const BASE_URL = 'backend.categories.childcategory.';


    public function index(Request $request)
    {
        $child_categories = ChildCategory::with('category', 'subcategory', 'brand')->latest()->get();

        if (!empty($request->input('search_title'))) {
        $search = $request->input('search_title');
        $child_categories = ChildCategory::with('category', 'subcategory', 'brand')
            ->where('name', 'LIKE', '%' . $search . '%')
            ->latest()
            ->get();
        }

        $categories = Category::all();
        $subcategories = SubCategory::all();
        $brands = Brand::all();

        return view(self::BASE_URL . 'index', compact('child_categories', 'categories', 'subcategories', 'brands'));
    }

    public function create(Request $request)
    {
        $categories = Category::all();
        return view(self::BASE_URL . 'create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'brand_id' => 'nullable|exists:brands,id',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
        ]);

        $data = $request->only(['name', 'category_id', 'sub_category_id', 'brand_id', 'description']);
        $newImageId = null;

        DB::beginTransaction();

        try {
        if ($request->hasFile('image')) {
            $image = $this->mediaHelper->uploadMedia(
            $request->file('image'),
            'web'
            );
            $data['image'] = $image->id;
            $newImageId = $image->id;
        }

        ChildCategory::create(array_merge($data, ['slug' => Str::slug($data['name'])]));

        DB::commit();

        return redirect()->route('childcategories.index')->with('success', 'Child Category created successfully.');

        } catch (\Exception $e) {
        DB::rollBack();

        if ($newImageId) {
            $this->mediaHelper->deleteMediaImage($newImageId, 'web');
        }
        Log::error('ChildCategory creation error:', ['error' => $e]);

        return redirect()->back()->with('error', 'Failed to create child category. Please check the logs.');
        }
    }

    public function edit(Request $request, $id)
    {
        $child_category = ChildCategory::findOrFail($id);
        $categories = Category::all();

        $brands_for_category = Brand::where('category_id', $child_category->category_id)->get();

        $subcategories_for_brand = SubCategory::where('brand_id', $child_category->brand_id)->get();

        return view(self::BASE_URL . 'edit', compact(
        'child_category',
        'categories',
        'brands_for_category',
        'subcategories_for_brand'
        ));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'required',
        'sub_category_id' => 'required',
        'brand_id' => 'nullable|exists:brands,id',
        'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
        ]);

        $child_category = ChildCategory::findOrFail($id);
        $data = $request->only(['name', 'category_id', 'sub_category_id', 'brand_id', 'description']);
        $newImageId = null;

        DB::beginTransaction();

        try {
        $oldImageId = $child_category->getRawOriginal('image');

        if ($request->hasFile('image')) {
            $image = $this->mediaHelper->uploadMedia(
            $request->file('image'),
            'web'
            );
            $data['image'] = $image->id;
            $newImageId = $image->id;
        }

        $child_category->update(array_merge($data, ['slug' => Str::slug($data['name'])]));

        if ($newImageId && $oldImageId) {
            $this->mediaHelper->deleteMediaImage($oldImageId, 'web');
        }

        DB::commit();

        return redirect()->route('childcategories.index')->with('success', 'Child Category updated successfully.');

        } catch (\Exception $e) {
        DB::rollBack();

        if ($newImageId) {
            $this->mediaHelper->deleteMediaImage($newImageId, 'web');
        }

        Log::error('ChildCategory update error:', ['error' => $e, 'child_category_id' => $id]);

        return redirect()->back()->with('error', 'Failed to update child category. Please check the logs.');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
        $child_category = ChildCategory::findOrFail($id);
        $imageId = $child_category->getRawOriginal('image');

        $child_category->delete();

        if ($imageId) {
            $this->mediaHelper->deleteMediaImage($imageId, 'web');
        }

        DB::commit();

        return redirect()->back()->with(FlashMsg::item_new('Child Category Deleted Success'));

        } catch (\Exception $e) {
        DB::rollBack();
        Log::error('ChildCategory deletion error:', ['error' => $e, 'child_category_id' => $id]);
        return redirect()->back()->with('error', 'Failed to delete child category');
        }
    }

    public function changeStatus($id)
    {
        $child_category = ChildCategory::find($id);
        $child_category->status = $child_category->status == 1 ? 0 : 1;
        $child_category->save();

        return redirect()->back()->with(FlashMsg::item_new('Status Change Success'));
    }

    public function bulkDelete(Request $request)
    {
        DB::beginTransaction();
        try {
        $child_categories = ChildCategory::whereIn('id', $request->ids)->get();

        foreach ($child_categories as $child_category) {
            $imageId = $child_category->getRawOriginal('image');
            if ($imageId) {
            $this->mediaHelper->deleteMediaImage($imageId, 'web');
            }
        }

        ChildCategory::whereIn('id', $request->ids)->delete();

        DB::commit();

        return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
        DB::rollBack();
        Log::error('ChildCategory bulk delete error:', ['error' => $e]);
        return response()->json(['status' => 'error']);
        }
    }


    public function getBrandsByCategory($category_id)
    {
        $brands = Brand::where('category_id', $category_id)->where('status', 1)->get(['id', 'title']);
        return response()->json($brands);
    }

    public function getSubCategoriesByBrand($brand_id)
    {
        $subcategories = SubCategory::where('brand_id', $brand_id)->where('status', 1)->get(['id', 'name']);
        return response()->json($subcategories);
    }

    public function getSubCategoriesByCategory($category_id)
    {
        $subcategories = SubCategory::where('category_id', $category_id)->where('status', 1)->get(['id', 'name']);
        return response()->json($subcategories);
    }

}
