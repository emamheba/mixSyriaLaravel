<?php

namespace Modules\Brand\app\Http\Controllers;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Brand\app\Models\Brand;
use App\Models\Backend\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Actions\Media\v1\MediaHelper;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
  public function __construct(private MediaHelper $mediaHelper)
  {
  }
  const BASE_URL = 'brand::brand.';

  public function index()
  {
    $brands = Brand::latest()->get();

    return view(self::BASE_URL . 'index', compact('brands'));
  }

  public function create(Request $request)
  {
    $categories = Category::all();
    return view(self::BASE_URL . 'create', compact('categories'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'url' => 'nullable|string',
      'image' => 'required|image|mimes:jpg,png,jpeg,gif',
      'category_id' => 'required|exists:categories,id',
    ]);

    $data = $request->only(['title', 'url', 'category_id']);
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

      $brand = Brand::create($data);

      return redirect()->route('brands.index');

    } catch (\Exception $e) {
      if ($imageId) {
        $this->mediaHelper->deleteMediaImage($imageId, 'web');
      }
      Log::error('Brand creation error: ' . $e->getMessage());
      return redirect()->back()->with('error', 'Failed to create brand');
    }
  }
  public function edit(Request $request, Brand $brand)
  {
    $categories = Category::all();
    return view(self::BASE_URL . 'edit', compact('brand', 'categories'));
  }

  public function update(Request $request, Brand $brand)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'url' => 'nullable|string',
      'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
      'category_id' => 'required|exists:categories,id',
    ]);

    $data = $request->only(['title', 'url', 'category_id']);
    $oldImageId = null;

    try {
      if ($request->hasFile('image')) {
        $oldImageId = $brand->image;

        $image = $this->mediaHelper->uploadMedia(
          $request->file('image'),
          'web'
        );
        $data['image'] = $image->id;
      }

      $brand->update($data);

      if ($oldImageId) {
        $this->mediaHelper->deleteMediaImage($oldImageId, 'web');
      }

      return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');

    } catch (\Exception $e) {
      Log::error('Brand update error: ' . $e->getMessage());
      return redirect()->back()->with('error', 'Failed to update brand');
    }
  }
  public function destroy($id)
  {
    try {
      $brand = Brand::find($id);

      if ($brand && $brand->image) {
        $this->mediaHelper->deleteMediaImage($brand->image, 'web');
      }

      $brand->delete();
      return redirect()->back()->with(FlashMsg::item_new('Brand Deleted Success'));

    } catch (\Exception $e) {
      Log::error('Brand deletion error: ' . $e->getMessage());
      return redirect()->back()->with('error', 'Failed to delete brand');
    }
  }
  public function changeStatus($id)
  {
    $brand = Brand::select('status')->where('id', $id)->first();
    if ($brand->status == 1) {
      $status = 0;
    } else {
      $status = 1;
    }
    Brand::where('id', $id)->update(['status' => $status]);
    return redirect()->back()->with(FlashMsg::item_new('Status Change Success'));
  }
}
