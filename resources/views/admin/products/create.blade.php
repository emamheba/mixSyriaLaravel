@extends('layouts/layoutMaster')

@section('title', 'eCommerce Product Add - Apps')

@section('vendor-style')
  @vite([
  'resources/assets/vendor/libs/quill/typography.scss',
  'resources/assets/vendor/libs/quill/katex.scss',
  'resources/assets/vendor/libs/quill/editor.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/dropzone/dropzone.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/tagify/tagify.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
  'resources/assets/vendor/libs/quill/katex.js',
  'resources/assets/vendor/libs/quill/quill.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/dropzone/dropzone.js',
  'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/tagify/tagify.js'
  ])
@endsection

@section('page-script')
  @vite([
    'resources/assets/js/app-ecommerce-product-add.js'
  ])
@endsection

@section('page-style')
<style>
  
</style>
@endsection
@section('content')
<div class="app-ecommerce">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
    <div class="d-flex flex-column justify-content-center">
      <h4 class="mb-1">Add a new Product</h4>
      <p class="mb-0">Orders placed across your store</p>
    </div>
    <div class="d-flex align-content-center flex-wrap gap-4">
      <div class="d-flex gap-4">
        <button class="btn btn-label-secondary">Discard</button>
        <button class="btn btn-label-primary">Save draft</button>
      </div>
      <button type="submit" form="product-form" class="btn btn-primary">Publish product</button>
    </div>
  </div>

  <form id="product-form" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="image" id="image-path"> 
    <input type="file" name="gallery[]" id="hidden-images" multiple style="display: none;">

    <div class="row">
      <div class="col-12 col-lg-8">
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-tile mb-0">Product information</h5>
          </div>
          <div class="card-body">
            <div class="mb-6">
              <label class="form-label" for="ecommerce-product-name">Name</label>
              <input type="text" class="form-control" id="ecommerce-product-name" placeholder="Product title" name="name" aria-label="Product title" required>
            </div>
            <div class="row mb-6">
              <div class="col">
                <label class="form-label" for="ecommerce-product-price">Price</label>
                <input type="number" class="form-control" id="ecommerce-product-price" placeholder="Price" name="price" aria-label="Product price" required>
              </div>
              <div class="col">
                <label class="form-label" for="ecommerce-product-sale-price">Sale Price</label>
                <input type="number" class="form-control" id="ecommerce-product-sale-price" placeholder="Sale Price" name="sale_price" aria-label="Product sale price">
              </div>
            </div>
            <div class="mb-6">
              <label class="form-label" for="basic-default-upload-file">Image</label>
              <input type="file" class="form-control" name="image" id="basic-default-upload-file" accept="image/*" required/>
            </div>
            <div class="mb-6">
              <label class="form-label" for="ecommerce-product-summary">Summary</label>
              <textarea class="form-control" id="ecommerce-product-summary" placeholder="Product summary" name="summary" aria-label="Product summary"></textarea>
            </div>
            <div class="mb-6">
              <label class="form-label" for="ecommerce-product-description">Description</label>
              <textarea class="form-control" id="ecommerce-product-description" placeholder="Product description" name="description" aria-label="Product description"></textarea>
            </div>
            <div class="row mb-6">
              <div class="col">
                <label class="form-label" for="ecommerce-product-quantity">Quantity</label>
                <input type="number" class="form-control" id="ecommerce-product-quantity" placeholder="Quantity" name="quantity" aria-label="Product quantity" required>
              </div>
              <div class="col">
                <label class="form-label" for="ecommerce-product-stock">Stock</label>
                <select class="form-select" id="ecommerce-product-stock" name="stock" required>
                  <option value="1">In Stock</option>
                  <option value="0">Out of Stock</option>
                </select>
              </div>
            </div>
            <div class="mb-6">
              <label class="form-label" for="ecommerce-product-status">Status</label>
              <select class="form-select" id="ecommerce-product-status" name="status" required>
                <option value="1">Published</option>
                <option value="0">Inactive</option>
              </select>
            </div>
          </div>
        </div>

          <div class="card mb-6">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0 card-title">Product Image</h5>
            </div>
            <div class="card-body">
              <div class="dropzone needsclick" id="dropzone-basic">
                <div class="dz-message needsclick">
                  <p class="h4 needsclick pt-3 mb-2">Drag and drop your image here</p>
                  <p class="h6 text-muted d-block fw-normal mb-2">or</p>
                  <span class="note needsclick btn btn-sm btn-label-primary">Browse image</span>
                </div>
              </div>
            </div>
          </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-title mb-0">Organize</h5>
          </div>
          <div class="card-body">
            <div class="mb-6">
              <label class="form-label" for="ecommerce-product-category">Category</label>
              <select class="form-select" id="ecommerce-product-category" name="category_id" required>
                <option value="">Select a category</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-6">
              <label class="form-label" for="ecommerce-product-sub-category">Sub Category</label>
              <select class="form-select" id="ecommerce-product-sub-category" name="sub_category_id" required>
                @foreach($subCategories as $subCategory)
                  <option value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-6">
              <label class="form-label" for="ecommerce-product-unit">Unit</label>
              <select class="form-select" id="ecommerce-product-unit" name="unit_id" required>
                @foreach($units as $id => $name)
                  <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="text-end mt-4">
      <button type="submit" class="btn btn-primary">Save Product</button>
    </div>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('ecommerce-product-category');
    const subCategorySelect = document.getElementById('ecommerce-product-sub-category');

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

@endsection