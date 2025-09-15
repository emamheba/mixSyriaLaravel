@extends('layouts/layoutMaster')

@section('title', 'تعديل العلامة التجارية - التطبيقات')

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
      <h4 class="mb-1">تعديل العلامة التجارية</h4>
      <p class="mb-0">تحديث تفاصيل العلامة التجارية</p>
    </div>
  </div>

  <div class="row">
    <div class="col-xl mb-6">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">تعديل العلامة التجارية</h5> <small class="text-muted float-end">تحديث</small>
        </div>
        <div class="card-body">
          <form action="{{ route('brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-6">
              <label class="form-label" for="basic-default-fullname">الاسم</label>
              <input type="text" class="form-control" id="basic-default-fullname" name="title" value="{{ $brand->title }}" placeholder="اسم العلامة التجارية">
            </div>

            <!-- إضافة حقل لاختيار الفئة -->
            <div class="mb-6">
              <label class="form-label" for="category_id">الفئة</label>
              <select class="form-select" name="category_id" id="category_id" required>
                <option value="" disabled>اختر فئة</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" 
                          @if($category->id == $brand->category_id) selected @endif>
                          {{ $category->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="mb-6">
              <label class="form-label" for="basic-default-upload-file">الصورة</label>
              <input type="file" class="form-control" name="image" id="basic-default-upload-file" accept="image/*"/>
              @if ($brand->image)
                <div class="mt-2">
                  <img src="{{ $brand->image }}" alt="صورة العلامة التجارية" style="max-width: 200px; height: auto;">
                </div>
              @endif
            </div>

            <button type="submit" class="btn btn-primary waves-effect waves-light">تحديث</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection