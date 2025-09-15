@extends('layouts/layoutMaster')

@section('title', __('Edit Category'))

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
@section('content')
<div class="app-ecommerce">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
    <div class="d-flex flex-column justify-content-center">
      <h4 class="mb-1">{{ __('Edit Category') }}</h4>
      <p class="mb-0">{{ __('Update the category details') }}</p>
    </div>
  </div>
  <div class="row">
    <div class="col-xl mb-6">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">{{ __('Edit Category') }}</h5> <small class="text-muted float-end">{{ __('Update') }}</small>
        </div>
        <div class="card-body">
          <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-6">
              <label class="form-label" for="basic-default-fullname">{{ __('Name') }}</label>
              <input type="text" class="form-control" id="basic-default-fullname" name="name" value="{{ $category->name }}" placeholder="{{ __('Category name') }}">
            </div>

               <div class="mb-6">
        <label class="form-label" for="category-type">{{__('نوع الفئة')}}</label>
        <select id="category-type" class="form-select" name="category_type">
            <option value="">{{ __('اختر النوع') }}</option>
            <option value="sell" @if($category->category_type == 'sell') selected @endif>{{ __('بيع') }}</option>
            <option value="rent" @if($category->category_type == 'rent') selected @endif>{{ __('إيجار') }}</option>
            <option value="job" @if($category->category_type == 'job') selected @endif>{{ __('وظيفة') }}</option>
            <option value="service" @if($category->category_type == 'service') selected @endif>{{ __('خدمة') }}</option>
        </select>
    </div>
            <div class="mb-6">
              <label class="form-label" for="basic-default-upload-file">{{ __('Image') }}</label>
              <input type="file" class="form-control" name="image" id="basic-default-upload-file" accept="image/*"/>
              @if ($category->image)
                <div class="mt-2">
                  <img src="{{ $category->image }}" alt="{{ __('Category Image') }}" style="max-width: 200px; height: auto;">
                </div>
              @endif
            </div>
            <div class="mb-6">
              <label class="form-label" for="basic-default-message">{{ __('Description') }}</label>
              <textarea id="basic-default-message" class="form-control" name="description" placeholder="{{ __('Description') }}">{{ $category->description }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary waves-effect waves-light">{{ __('Update') }}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
