@extends('layouts/layoutMaster')

@section('title', __('Add a new SubCategory'))

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
  <script>
    document.getElementById('category_id').addEventListener('change', function() {
        const categoryId = this.value;

        // تحديث البراندات
        fetch(`/admin/get-brands/${categoryId}`)
            .then(response => response.json())
            .then(data => {
                const brandSelect = document.getElementById('brand_id');
                brandSelect.innerHTML =
                    '<option value="" disabled selected>{{ __('Select a Brand') }}</option>';
                data.forEach(brand => {
                    const option = document.createElement('option');
                    option.value = brand.id;
                    option.text = brand.title;
                    brandSelect.appendChild(option);
                });
            });


    });
</script>
@endsection

@section('content')
<div class="app-ecommerce">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
    <div class="d-flex flex-column justify-content-center">
      <h4 class="mb-1">{{ __('Add a new SubCategory') }}</h4>
      <p class="mb-0">{{ __('Orders placed across your store') }}</p>
    </div>
  </div>
  <div class="row">
    <div class="col-xl mb-6">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">{{ __('Create SubCategory') }}</h5> <small class="text-muted float-end">{{ __('New') }}</small>
        </div>
        <div class="card-body">
          <form class="browser-default-validation" action="{{ route('subcategories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-6">
              <label class="form-label" for="basic-default-fullname">{{ __('Name') }}</label>
              <input type="text" class="form-control" id="basic-default-fullname" name="name" placeholder="{{ __('Sub category name') }}">
            </div>

          <!-- Parent Category -->
          <div class="mb-6">
            <label class="form-label" for="category_id">{{ __('Parent Category') }}</label>
            <select class="form-select" name="category_id" id="category_id" required>
                <option value="" disabled selected>{{ __('Select a Category') }}</option>
                <!-- هذا الخيار سيظهر أولاً ولن يتم تحديده -->
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}</option>
                    <!-- تحديد الفئة الأم المحددة إذا كانت موجودة -->
                @endforeach
            </select>
        </div>

            <!-- إضافة حقل لاختيار العلامة التجارية -->
            {{-- <div class="mb-6">
              <label class="form-label" for="brand_id">{{ __('Brand') }}</label>
              <select class="form-select" name="brand_id" id="brand_id" required>
                  <option value="" disabled selected>{{ __('Select a Brand') }}</option>
              </select>
          </div> --}}


            {{-- <div class="mb-6">
              <label class="form-label" for="basic-default-upload-file">{{ __('Image') }}</label>
              <input type="file" class="form-control" name="image" id="basic-default-upload-file" accept="image/*"/>
            </div> --}}

            <div class="mb-6">
              <label class="form-label" for="basic-default-message">{{ __('Description') }}</label>
              <textarea id="basic-default-message" class="form-control" name="description" placeholder="{{ __('Description') }}"></textarea>
            </div>

            <button type="submit" class="btn btn-primary waves-effect waves-light">{{ __('Create') }}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
