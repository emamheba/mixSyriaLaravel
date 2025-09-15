<?php

namespace App\Http\Controllers\Backend\Categories;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\Backend\Category;
use App\Models\Backend\SubCategory;
use App\Traits\CategoryTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\Brand\app\Models\Brand;
use App\Actions\Media\v1\MediaHelper;
use Illuminate\Support\Facades\Log;

class SubCategoryController extends Controller
{
  public function __construct(private MediaHelper $mediaHelper)
  {
  }

  use CategoryTrait;

  public function index(Request $request)
  {
    $subcategories = SubCategory::with('category', 'brand')->latest()->get();

    if (!empty($request->input('search_title'))) {
      $search = $request->input('search_title');
      $sub_categories = SubCategory::with('category', 'brand')
        ->where('name', 'LIKE', '%' . $search . '%')
        ->latest()
      ->get();
    }

    $categories = Category::all();
    $brands = Brand::all();

    return view('backend.categories.subcategory.index', compact('subcategories', 'categories', 'brands'));
  }

  public function create(Request $request)
  {
    $categories = Category::all();

    return view('backend.categories.subcategory.create', compact('categories'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'category_id' => 'required',
      'description' => 'nullable|string',
      'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
      'brand_id' => 'nullable|exists:brands,id',
    ]);

    $data = $request->only(['name', 'category_id', 'description']);
    $imageId = null;

    try {
      if ($request->hasFile('image')) {
        $image = $this->mediaHelper->uploadMedia(
          $request->file('image'),
          'web'
        );
        $data['image'] = $image->id;
        $imageId = $image->id;
      }

      SubCategory::create(array_merge($data, ['slug' => Str::slug($data['name'])]));

      return redirect()->route('subcategories.index')->with('success', 'Sub Category created successfully.');

    } catch (\Exception $e) {
      if ($imageId) {
        $this->mediaHelper->deleteMediaImage($imageId, 'web');
      }
      Log::error('SubCategory creation error: ' . $e->getMessage());
      return redirect()->back()->with('error', 'Failed to create subcategory');
    }
  }
  public function edit(SubCategory $subcategory)
  {
    $categories = Category::all();
    $brands = Brand::all();
    return view('backend.categories.subcategory.edit', compact('subcategory', 'categories', 'brands'));
  }

  public function update(Request $request, SubCategory $subcategory)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'category_id' => 'required',
      'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
      'brand_id' => 'nullable|exists:brands,id',
    ]);

    $data = $request->only(['name', 'description', 'category_id']);
    $oldImageId = null;

    try {
      if ($request->hasFile('image')) {
        $oldImageId = $subcategory->image;

        $image = $this->mediaHelper->uploadMedia(
          $request->file('image'),
          'web'
        );
        $data['image'] = $image->id;
      }

      $subcategory->update(array_merge($data, ['slug' => Str::slug($data['name'])]));

      if ($oldImageId) {
        $this->mediaHelper->deleteMediaImage($oldImageId, 'web');
      }

      return redirect()->route('subcategories.index')->with('success', 'Sub Category updated successfully.');

    } catch (\Exception $e) {
      Log::error('SubCategory update error: ' . $e->getMessage());
      return redirect()->back()->with('error', 'Failed to update subcategory');
    }
  }
  public function getSubCategories()
  {
    $subCategories = SubCategory::with('category', 'brand')->get();

    return response()->json($subCategories);
  }

  public function addNewSubcategory(Request $request)
  {
    if ($request->isMethod('post')) {
      $request->validate([
        'name' => 'required|max:191|unique:sub_categories',
        'slug' => 'max:191|unique:sub_categories',
        'category_id' => 'required',
        'brand_id' => 'nullable|exists:brands,id',
        'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
      ]);

      $slug = $request->slug == '' ? Str::slug($request->name) : $request->slug;
      $imageId = null;

      try {
        if ($request->hasFile('image')) {
          $image = $this->mediaHelper->uploadMedia(
            $request->file('image'),
            'web'
          );
          $imageId = $image->id;
        }

        $sub_category = SubCategory::create([
          'name' => $request->name,
          'description' => $request->description,
          'slug' => $slug,
          'category_id' => $request->category_id,
          'brand_id' => $request->brand_id ?? null,
          'image' => $imageId,
        ]);

        $Metas = [
          'meta_title' => purify_html($request->meta_title),
          'meta_tags' => purify_html($request->meta_tags),
          'meta_description' => purify_html($request->meta_description),
          'facebook_meta_tags' => purify_html($request->facebook_meta_tags),
          'facebook_meta_description' => purify_html($request->facebook_meta_description),
          'facebook_meta_image' => $request->facebook_meta_image,
          'twitter_meta_tags' => purify_html($request->twitter_meta_tags),
          'twitter_meta_description' => purify_html($request->twitter_meta_description),
          'twitter_meta_image' => $request->twitter_meta_image,
        ];
        $sub_category->metaData()->create($Metas);

        return redirect()->back()->with(FlashMsg::item_new('Sub Category Added'));

      } catch (\Exception $e) {
        if ($imageId) {
          $this->mediaHelper->deleteMediaImage($imageId, 'web');
        }
        Log::error('SubCategory creation error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to create subcategory');
      }
    }

    $categories = Category::all();
    $brands = Brand::all();
    return view('backend.pages.subcategory.add_subcategory', compact('categories', 'brands'));
  }
  public function changeStatus($id)
  {
    $category = SubCategory::select('status')->where('id', $id)->first();
    $status = $category->status == 1 ? 0 : 1;
    SubCategory::where('id', $id)->update(['status' => $status]);

    return redirect()->back()->with(FlashMsg::item_new('Status Change Success'));
  }

  public function editSubcategory(Request $request, $id = null)
  {
    if ($request->isMethod('post')) {
      $request->validate([
        'name' => 'required|max:191|unique:sub_categories,name,' . $request->id,
        'category_id' => 'required',
        'slug' => 'max:191|unique:sub_categories,slug,' . $request->id,
        'brand_id' => 'nullable|exists:brands,id',
        'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
      ], [
        'name.unique' => __('Sub Category Already Exists.'),
        'slug.unique' => __('Slug Already Exists.'),
      ]);

      $subcategory = SubCategory::findOrFail($request->id);
      $old_slug = $subcategory->slug;
      $oldImageId = $subcategory->image;
      $newImageId = $oldImageId;

      try {
        if ($request->hasFile('image')) {
          $image = $this->mediaHelper->uploadMedia(
            $request->file('image'),
            'web'
          );
          $newImageId = $image->id;
        }

        SubCategory::where('id', $request->id)->update([
          'name' => $request->name,
          'description' => $request->description,
          'category_id' => $request->category_id,
          'slug' => $request->slug ?? $old_slug,
          'brand_id' => $request->brand_id,
          'image' => $newImageId,
        ]);

        // Delete old image after successful update
        if ($request->hasFile('image') && $oldImageId) {
          $this->mediaHelper->deleteMediaImage($oldImageId, 'web');
        }

        // باقي الكود للـ metadata...
        $subcategory_meta_update = SubCategory::findOrFail($id);
        $Metas = [
          'meta_title' => purify_html($request->meta_title),
          'meta_tags' => purify_html($request->meta_tags),
          'meta_description' => purify_html($request->meta_description),
          'facebook_meta_tags' => purify_html($request->facebook_meta_tags),
          'facebook_meta_description' => purify_html($request->facebook_meta_description),
          'facebook_meta_image' => $request->facebook_meta_image,
          'twitter_meta_tags' => purify_html($request->twitter_meta_tags),
          'twitter_meta_description' => purify_html($request->twitter_meta_description),
          'twitter_meta_image' => $request->twitter_meta_image,
        ];

        if (is_null($subcategory_meta_update->metaData()->first())) {
          $subcategory_meta_update->metaData()->create($Metas);
        } else {
          $subcategory_meta_update->metaData()->update($Metas);
        }

        return redirect()->back()->with(FlashMsg::item_new('Sub Category Update Success'));

      } catch (\Exception $e) {
        Log::error('SubCategory update error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to update subcategory');
      }
    }

    $subcategory = SubCategory::find($id);
    $categories = Category::where('status', 1)->get();
    $brands = Brand::all();
    return view('backend.pages.subcategory.edit_subcategory', compact('subcategory', 'categories', 'brands'));
  }
  public function destroy($id)
  {
    try {
      $subcategory = SubCategory::find($id);

      if ($subcategory && $subcategory->image) {
        $this->mediaHelper->deleteMediaImage($subcategory->image, 'web');
      }

      $subcategory->delete();
      return redirect()->back()->with(FlashMsg::item_new('Sub Category Deleted Success'));

    } catch (\Exception $e) {
      Log::error('SubCategory deletion error: ' . $e->getMessage());
      return redirect()->back()->with('error', 'Failed to delete subcategory');
    }
  }
  public function bulkAction(Request $request)
  {
    try {
      $subcategories = SubCategory::whereIn('id', $request->ids)->get();

      foreach ($subcategories as $subcategory) {
        if ($subcategory->image) {
          $this->mediaHelper->deleteMediaImage($subcategory->image, 'web');
        }
      }

      SubCategory::whereIn('id', $request->ids)->delete();
      return response()->json(['status' => 'ok']);

    } catch (\Exception $e) {
      Log::error('SubCategory bulk delete error: ' . $e->getMessage());
      return response()->json(['status' => 'error']);
    }
  }
  public function searchSubCategory(Request $request)
  {
    $sub_categories = SubCategory::where('name', 'LIKE', "%" . strip_tags($request->string_search) . "%")->paginate(10);
    return $sub_categories->total() >= 1 ? view('backend.pages.subcategory.search-subcategory', compact('sub_categories'))->render() : response()->json(['status' => __('nothing')]);
  }

  public function paginate(Request $request)
  {
    if ($request->ajax()) {
      $sub_categories = $request->string_search == ''
        ? SubCategory::latest()->paginate(10)
        : SubCategory::where('name', 'LIKE', "%" . strip_tags($request->string_search) . "%")->paginate(10);

      return view('backend.pages.subcategory.search-subcategory', compact('sub_categories'))->render();
    }
  }

  public function getBrands($category_id)
  {
    $brands = $this->getBrandsByCategory($category_id);
    return response()->json($brands);
  }
}
