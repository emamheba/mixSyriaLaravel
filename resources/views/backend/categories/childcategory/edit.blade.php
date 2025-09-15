@extends('layouts/layoutMaster')

@section('title', __('تعديل الفئة الفرعية الثانية'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/dropzone/dropzone.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/tagify/tagify.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/dropzone/dropzone.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/tagify/tagify.js'
])
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('sub_category_id');

    const currentSubCategoryId = "{{ $child_category->sub_category_id }}";

    function fetchOptions(categoryId) {
      fetch(`/admin/get-subcategories-by-category/${categoryId}`)
        .then(res => res.json())
        .then(data => {
          subcategorySelect.innerHTML = `<option value="" disabled>{{ __('اختر فئة فرعية') }}</option>`;
          data.forEach(sub => {
            const selected = sub.id == currentSubCategoryId ? 'selected' : '';
            subcategorySelect.innerHTML += `<option value="${sub.id}" ${selected}>${sub.name}</option>`;
          });
          subcategorySelect.disabled = false;
        });
    }

    categorySelect.addEventListener('change', function () {
      const categoryId = this.value;
      subcategorySelect.innerHTML = `<option value="" disabled selected>{{ __('اختر فئة فرعية') }}</option>`;
      subcategorySelect.disabled = true;
      if (categoryId) fetchOptions(categoryId);
    });

    if (categorySelect.value) {
      fetchOptions(categorySelect.value);
    }
  });
</script>
@endsection

@section('content')
<div class="app-ecommerce">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
    <div class="d-flex flex-column justify-content-center">
      <h4 class="mb-1">{{ __('تعديل الفئة الفرعية الثانية') }}</h4>
      <p class="mb-0">{{ __('تحديث تفاصيل الفئة الفرعية الثانية') }}</p>
    </div>
  </div>

  <div class="row">
    <div class="col-xl mb-6">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">{{ __('تعديل الفئة الفرعية الثانية') }}</h5>
          <small class="text-muted float-end">{{ __('تحديث') }}</small>
        </div>
        <div class="card-body">
          <form action="{{ route('childcategories.update', $child_category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-6">
              <label class="form-label" for="name">{{ __('الاسم') }}</label>
              <input type="text" class="form-control" id="name" name="name" value="{{ $child_category->name }}" placeholder="اسم الفئة الفرعية الثانية" required>
            </div>

            <div class="mb-6">
              <label class="form-label" for="category_id">{{ __('الفئة') }}</label>
              <select class="form-select" id="category_id" name="category_id" required>
                <option value="" disabled>{{ __('اختر الفئة') }}</option>
                @foreach ($categories as $category)
                  <option value="{{ $category->id }}" {{ $child_category->category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="mb-6">
              <label class="form-label" for="sub_category_id">{{ __('الفئة الفرعية') }}</label>
              <select class="form-select" id="sub_category_id" name="sub_category_id" required>
                <option value="" disabled selected>{{ __('اختر فئة فرعية') }}</option>
              </select>
            </div>

            <div class="mb-6">
              <label class="form-label" for="description">{{ __('الوصف') }}</label>
              <textarea id="description" class="form-control" name="description" placeholder="الوصف">{{ $child_category->description }}</textarea>
            </div>

            {{-- <div class="mb-6">
              <label class="form-label" for="image">{{ __('الصورة') }}</label>
              <input type="file" class="form-control" name="image" id="image" accept="image/*"/>
              @if ($child_category->image)
                <div class="mt-2">
                  <img src="{{ $child_category->image }}" alt="صورة الفئة الفرعية الثانية" style="max-width: 200px; height: auto;">
                </div>
              @endif
            </div> --}}

            <button type="submit" class="btn btn-primary waves-effect waves-light">{{ __('حفظ التغييرات') }}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
