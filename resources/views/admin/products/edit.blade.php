@extends('layouts/layoutMaster')

@section('title', 'Update Product - Apps')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/quill/typography.scss',
  'resources/assets/vendor/libs/quill/katex.scss',
  'resources/assets/vendor/libs/quill/editor.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/dropzone/dropzone.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/quill/katex.js',
  'resources/assets/vendor/libs/quill/quill.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/dropzone/dropzone.js'
])
@endsection

@section('content')
<div class="app-ecommerce">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6">
    <div class="d-flex flex-column justify-content-center">
      <h4 class="mb-1">Update Product</h4>
    </div>
    <div class="d-flex gap-4">
      <button type="submit" form="product-form" class="btn btn-primary">Update Product</button>
    </div>
  </div>

  <form id="product-form" action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
      <div class="col-12 col-lg-8">
        <div class="card mb-4">
          <div class="card-body">
            <div class="mb-6">
              <label class="form-label" for="ecommerce-product-name">Name</label>
              <input type="text" class="form-control" id="ecommerce-product-name" placeholder="Product title" name="name" value="{{ $product->name }}" aria-label="Product title" required>
            </div>
            <div class="row mb-6">
              <div class="col">
                <label class="form-label" for="ecommerce-product-price">Price</label>
                <input type="number" class="form-control" id="ecommerce-product-price" placeholder="Price" name="price" value="{{ $product->price }}" aria-label="Product price" required>
              </div>
              <div class="col">
                <label class="form-label" for="ecommerce-product-sale-price">Sale Price</label>
                <input type="number" class="form-control" id="ecommerce-product-sale-price" placeholder="Sale Price" name="sale_price" value="{{ $product->sale_price }}" aria-label="Product sale price">
              </div>
            </div>
            <div class="mb-6">
              <label class="form-label" for="basic-default-upload-file">Image</label>
              <input type="file" class="form-control" name="image" id="basic-default-upload-file" accept="image/*"/>
              @if ($product->image)
                <div class="mt-2">
                  <img src="{{ $product->image }}" alt="Product Image" style="max-width: 200px; height: auto;">
                </div>
              @endif
            </div>
            <div class="mb-6">
              <label class="form-label" for="ecommerce-product-summary">Summary</label>
              <textarea class="form-control" id="ecommerce-product-summary" placeholder="Product summary" name="summary" aria-label="Product summary">{{ $product->summary }}</textarea>
            </div>
            <div class="mb-6">
              <label class="form-label" for="ecommerce-product-description">Description</label>
              <textarea class="form-control" id="ecommerce-product-description" placeholder="Product description" name="description" aria-label="Product description">{{ $product->description }}</textarea>
            </div>
            <div class="row mb-6">
              <div class="col">
                <label class="form-label" for="ecommerce-product-quantity">Quantity</label>
                <input type="number" class="form-control" id="ecommerce-product-quantity" placeholder="Quantity" name="quantity" value="{{ $product->quantity }}" aria-label="Product quantity" required>
              </div>
              <div class="col">
                <label class="form-label" for="ecommerce-product-stock">Stock</label>
                <select class="form-select" id="ecommerce-product-stock" name="stock" required>
                  <option value="1" {{ $product->stock == 1 ? 'selected' : '' }}>In Stock</option>
                  <option value="0" {{ $product->stock == 0 ? 'selected' : '' }}>Out of Stock</option>
                </select>
              </div>
            </div>
            <div class="mb-6">
              <label class="form-label" for="ecommerce-product-status">Status</label>
              <select class="form-select" id="ecommerce-product-status" name="status" required>
                <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Published</option>
                <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>
            
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title mb-3">Product Gallery</h5>
            
            <div class="row mb-3" id="gallery-container">
              @foreach($product->gallery as $image)
              <div class="col-3 mb-3 position-relative gallery-item" data-id="{{ $image->id }}">
                <img src="{{ $image->image }}" class="img-thumbnail">
                <button type="button" class="btn btn-danger btn-sm delete-gallery-image">
                  <i class="fas fa-times"></i>
                </button>
              </div>
              @endforeach
            </div>

            <div class="dropzone" id="gallery-dropzone">
              <div class="dz-message">
                <i class="fas fa-images fa-3x text-muted"></i>
                <p class="my-2">Drag & drop or click to upload multiple images</p>
              </div>
            </div>
            <input type="file" name="gallery[]" id="hidden-gallery" multiple style="display: none;">
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="card">
          <div class="card-body">
            <div class="mb-6">
              <label class="form-label" for="ecommerce-product-category">Category</label>
              <select class="form-select" id="ecommerce-product-category" name="category_id" required>
                <option value="">Select a category</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                  </option>
                @endforeach
              </select>
            </div>
            
            <div class="mb-6">
              <label class="form-label" for="ecommerce-product-sub-category">Sub Category</label>
              <select class="form-select" id="ecommerce-product-sub-category" name="sub_category_id" required>
                <option value="">Select a subcategory</option>
                @if(isset($subCategories))
                  @foreach($subCategories as $subCategory)
                    <option value="{{ $subCategory->id }}" {{ $product->sub_category_id == $subCategory->id ? 'selected' : '' }}>
                      {{ $subCategory->name }}
                    </option>
                  @endforeach
                @endif
              </select>
            </div>

            <div class="mb-4">
              <label class="form-label">Unit</label>
              <select class="form-select" name="unit_id" required>
                @foreach($units as $unit)
                <option value="{{ $unit->id }}" {{ $unit->id == $product->unit_id ? 'selected' : '' }}>
                  {{ $unit->name }}
                </option>
                @endforeach
              </select>
            </div>

            <div class="mb-4">
              <label class="form-label">Status</label>
              <select class="form-select" name="status" required>
                <option value="1" {{ $product->status ? 'selected' : '' }}>Published</option>
                <option value="0" {{ !$product->status ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  

  const galleryDropzone = new Dropzone('#gallery-dropzone', {
    url: '#',
    maxFiles: 5,
    acceptedFiles: 'image/*',
    autoProcessQueue: false,
    previewTemplate: `<div class="dz-preview dz-file-preview">
      <div class="dz-details">
        <div class="dz-thumbnail">
          <img data-dz-thumbnail>
          <span class="dz-nopreview">No preview</span>
          <div class="dz-success-mark"></div>
          <div class="dz-error-mark"></div>
          <div class="dz-error-message"><span data-dz-errormessage></span></div>
          <div class="progress">
            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
          </div>
        </div>
        <div class="dz-filename" data-dz-name></div>
        <div class="dz-size" data-dz-size></div>
      </div>
    </div>`,
    init: function() {
      this.on("addedfile", file => {
        const input = document.querySelector('#hidden-gallery');
        const dt = new DataTransfer();
        Array.from(input.files).forEach(f => dt.items.add(f));
        dt.items.add(file);
        input.files = dt.files;
      });

      this.on("removedfile", file => {
        const input = document.querySelector('#hidden-gallery');
        const dt = new DataTransfer();
        Array.from(input.files).filter(f => f.name !== file.name).forEach(f => dt.items.add(f));
        input.files = dt.files;
      });
    }
  });

  document.querySelectorAll('.delete-gallery-image').forEach(btn => {
    btn.addEventListener('click', function() {
      const item = this.closest('.gallery-item');
      if (confirm('Delete this image?')) {
        fetch(`/gallery/${item.dataset.id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          }
        }).then(res => {
          if (res.ok) item.remove();
        });
      }
    });
  });
});



document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('ecommerce-product-category');
    const subCategorySelect = document.getElementById('ecommerce-product-sub-category');

    const selectedCategoryId = categorySelect.value;

    if (selectedCategoryId) {
        fetch(`/get-subcategories/${selectedCategoryId}`)
            .then(response => response.json())
            .then(data => {
                subCategorySelect.innerHTML = '<option value="">Select a subcategory</option>';

                data.forEach(subCategory => {
                    const option = document.createElement('option');
                    option.value = subCategory.id;
                    option.textContent = subCategory.name;

                    if (subCategory.id == '{{ $product->sub_category_id }}') {
                        option.selected = true;
                    }

                    subCategorySelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error fetching subcategories:', error);
            });
    }
    categorySelect.addEventListener('change', function () {
        const categoryId = this.value;

        subCategorySelect.innerHTML = '<option value="">Select a subcategory</option>';

        if (categoryId) {
            fetch(`/get-subcategories/${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(subCategory => {
                        const option = document.createElement('option');
                        option.value = subCategory.id;
                        option.textContent = subCategory.name;
                        subCategorySelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching subcategories:', error);
                });
        }
    });
});
</script>

<style>
.dropzone {
  border: 2px dashed #dee2e6;
  border-radius: 0.5rem;
  padding: 1.5rem;
  background: #f8f9fa;
}

.dz-preview {
  margin: 0.5rem;
  border-radius: 0.5rem;
  overflow: hidden;
  position: relative;
}

.dz-remove {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  color: #dc3545;
  background: white;
  border-radius: 50%;
  padding: 0.25rem 0.5rem;
  cursor: pointer;
}

.img-thumbnail {
  width: 100%;
  height: 150px;
  object-fit: cover;
}

.gallery-item {
  position: relative;
  transition: transform 0.2s;
}

.gallery-item:hover {
  transform: scale(1.03);
}

.dz-progress {
  height: 5px;
  background: #e9ecef;
}

.progress-bar-primary {
  background-color: #7367f0;
}
</style>
@endsection