@extends('layouts/layoutMaster')

@section('title', __('Add New Child Category'))

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
    @vite(['resources/assets/js/app-ecommerce-product-add.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categorySelect = document.getElementById('category_id');
            const subcategorySelect = document.getElementById('sub_category_id');

            categorySelect.addEventListener('change', function () {
                const categoryId = this.value;

                subcategorySelect.innerHTML = '<option value="" disabled selected>{{ __("Select a Sub Category") }}</option>';
                subcategorySelect.disabled = true;

                if (!categoryId) return;

                fetch(`/admin/get-subcategories-by-category/${categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(subcategory => {
                            const option = document.createElement('option');
                            option.value = subcategory.id;
                            option.text = subcategory.name;
                            subcategorySelect.appendChild(option);
                        });
                        subcategorySelect.disabled = false;
                    })
                    .catch(error => console.error('Error fetching subcategories:', error));
            });
        });
    </script>
@endsection

@section('content')
<div class="app-ecommerce">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
        <div class="d-flex flex-column justify-content-center">
            <h4 class="mb-1">{{ __('Add a new Child Category') }}</h4>
            <p class="mb-0">{{ __('Add a new child category with details') }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-xl mb-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Create Child Category') }}</h5>
                    <small class="text-muted float-end">{{ __('New') }}</small>
                </div>
                <div class="card-body">
                    <form class="browser-default-validation" action="{{ route('childcategories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-6">
                            <label class="form-label" for="name">{{ __('Name') }}</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Child category name') }}" required>
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="category_id">{{ __('Parent Category') }}</label>
                            <select class="form-select" name="category_id" id="category_id" required>
                                <option value="" disabled selected>{{ __('Select a Category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="sub_category_id">{{ __('Sub Category') }}</label>
                            <select class="form-select" name="sub_category_id" id="sub_category_id" required>
                                <option value="" disabled selected>{{ __('Select a Sub Category') }}</option>
                            </select>
                        </div>



                        <div class="mb-6">
                            <label class="form-label" for="description">{{ __('Description') }}</label>
                            <textarea class="form-control" name="description" placeholder="{{ __('Enter description here') }}"></textarea>
                        </div>
{{-- 
                        <div class="mb-6">
                            <label class="form-label" for="image">{{ __('Image') }}</label>
                            <input type="file" class="form-control" name="image" id="image" accept="image/*" />
                        </div> --}}

                        <button type="submit" class="btn btn-primary waves-effect waves-light">{{ __('Create') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
