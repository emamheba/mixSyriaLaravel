@extends('layouts/layoutMaster')

@section('title', 'eCommerce Product Add - Apps')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
  'resources/assets/vendor/libs/tagify/tagify.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/typeahead-js/typeahead.js',
  'resources/assets/vendor/libs/tagify/tagify.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('page-script')
@vite([
  'resources/assets/js/app-ecommerce-product-add.js'
])
@endsection

@section('content')
<div class="app-ecommerce">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">

    <div class="d-flex flex-column justify-content-center">
      <h4 class="mb-1">Add a new SubCategory</h4>
      <p class="mb-0">Orders placed across your store</p>
    </div>
  </div>

  <div class="row">

    <div class="col-xl mb-6">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Create Category</h5> <small class="text-muted float-end">New</small>
        </div>
        <div class="card-body">
          <form class="browser-default-validation" action="{{route('sub-categories.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-6">
              <label class="form-label" for="basic-default-fullname">Name</label>
              <input type="text" class="form-control" id="basic-default-fullname" name="name" placeholder="sub category name">
            </div>
            <div class="mb-6">
              <label class="form-label" for="basic-default-country">Category</label>
              <select class="form-select" id="basic-default-country" name='category_id' required>
                @foreach ($categories as $category)
                  <option value="{{$category->id}}">{{$category->name}}</option>
                @endforeach              
              </select>
            </div>
            <div class="mb-6">
              <label class="form-label" for="basic-default-upload-file">Image</label>
              <input type="file" class="form-control" name="image" id="basic-default-upload-file" accept="image/*" required/>
            </div>
            <div class="mb-6">
              <label class="form-label" for="basic-default-message">Description</label>
              <textarea id="basic-default-message" class="form-control" name="description" placeholder="Description"></textarea>
            </div>
            <button type="submit" class="btn btn-primary waves-effect waves-light">Create</button>
          </form>
        </div>
      </div>
    </div>

  </div>  
    
</div>

@endsection
