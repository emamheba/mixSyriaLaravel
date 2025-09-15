<?php

namespace App\Http\Controllers\Backend\Categories;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\Backend\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Actions\Media\v1\MediaHelper;
use Illuminate\Validation\Rule; 

class CategoryController extends Controller
{

  public function __construct(private MediaHelper $mediaHelper)
  {
  }

  public function index(Request $request)
  {
    $categories = Category::latest()->get();
    if (!empty($request->input('search_title'))) {
      $search = $request->input('search_title');
      $categories = Category::where('name', 'LIKE', '%' . $search . '%')->latest()->get();
    }
    return view('backend.categories.category.index', compact('categories'));
  }

  public function create(Request $request)
  {
    return view('backend.categories.category.create');
  }
 public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpg,png,jpeg,gif',
            'category_type' => ['nullable', Rule::in(['sell', 'rent', 'job', 'service'])],
        ]);

        $data = $request->only(['name', 'description', 'category_type']);
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

            Category::create(array_merge($data, ['slug' => Str::slug($data['name'])]));

            return redirect()->route('categories.index')->with(FlashMsg::item_new('تم إنشاء الفئة بنجاح'));

        } catch (\Exception $e) {
            if ($imageId) {
                $this->mediaHelper->deleteMediaImage($imageId, 'web');
            }
      Log::error('Category creation error: ' . $e->getMessage());
      return redirect()->back()->with('error', 'Failed to create category');
        }
    }
    
    public function getCategories(Request $request)
  {
    $categories = Category::latest()->paginate(10);

    return response()->json($categories);
  }

  public function edit(Category $category)
  {
    return view('backend.categories.category.edit', compact('category'));
  }

 public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
            'category_type' => ['nullable', Rule::in(['sell', 'rent', 'job', 'service'])],
        ]);

        $data = $request->only(['name', 'description', 'category_type']);
        $oldImageId = null;

        try {
            if ($request->hasFile('image')) {
                $oldImageId = $category->image;

                $image = $this->mediaHelper->uploadMedia(
                    $request->file('image'),
                    'web'
                );
                $data['image'] = $image->id;
            }

            $category->update(array_merge($data, ['slug' => Str::slug($data['name'])]));

            if ($oldImageId) {
                $this->mediaHelper->deleteMediaImage($oldImageId, 'web');
            }

            return redirect()->route('categories.index')->with(FlashMsg::item_new('تم تحديث الفئة بنجاح'));

        } catch (\Exception $e) {
      Log::error('Category update error: ' . $e->getMessage());
      return redirect()->back()->with('error', 'Failed to update category');
        }
    }
    
    public function changeStatus($id)
  {
    $category = Category::select('status')->where('id', $id)->first();
    if ($category->status == 1) {
      $status = 0;
    } else {
      $status = 1;
    }
    Category::where('id', $id)->update(['status' => $status]);
    return redirect()->back()->with(FlashMsg::item_new('Status Change Success'));
  }

  public function destroy($id)
  {
    try {
      $category = Category::find($id);

      if ($category && $category->image) {
        $this->mediaHelper->deleteMediaImage($category->image, 'web');
      }

      $category->delete();
      return redirect()->back()->with(FlashMsg::item_new('Category Deleted Success'));

    } catch (\Exception $e) {
      Log::error('Category deletion error: ' . $e->getMessage());
      return redirect()->back()->with('error', 'Failed to delete category');
    }
  }
  public function bulkAction(Request $request)
  {
    try {
      $categories = Category::whereIn('id', $request->ids)->get();

      foreach ($categories as $category) {
        if ($category->image) {
          $this->mediaHelper->deleteMediaImage($category->image, 'web');
        }
      }

      Category::whereIn('id', $request->ids)->delete();
      return response()->json(['status' => 'ok']);

    } catch (\Exception $e) {
      Log::error('Bulk delete error: ' . $e->getMessage());
      return response()->json(['status' => 'error']);
    }
  }
  public function searchCategory(Request $request)
  {
    $categories = Category::where('name', 'LIKE', "%" . strip_tags($request->string_search) . "%")->paginate(10);
    return $categories->total() >= 1 ? view('backend.pages.category.search-category', compact('categories'))->render() : response()->json(['status' => __('nothing')]);
  }
  function paginate(Request $request)
  {
    if ($request->ajax()) {
      if ($request->string_search == '') {
        $categories = Category::latest()->paginate(10);
      } else {
        $categories = Category::where('name', 'LIKE', "%" . strip_tags($request->string_search) . "%")->paginate(10);
      }
      return view('backend.pages.category.search-category', compact('categories'))->render();
    }
  }

}
