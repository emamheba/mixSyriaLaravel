@extends('layouts/layoutMaster')

@section('title', 'Add Membership - Pages')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
    @include('membership::backend.membership.membership-js')
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Membership /</span> Add Membership
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Membership Details</h5>
                <form action="{{ route('admin.membership.add') }}" method="POST" enctype="multipart/form-data" class="card-body">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type" class="form-label">Membership Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="select2 form-select" required>
                                    <option value="">Select Type</option>
                                    @foreach($all_types as $type)
                                        <option value="{{ $type->id }}">{{ $type->type }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title" class="form-label">Membership Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Enter price" value="{{ old('price') }}" required>
                                @error('price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="listing_limit" class="form-label">Listings Limit <span class="text-danger">*</span></label>
                                <input type="number" min="1" class="form-control" id="listing_limit" name="listing_limit" placeholder="Enter listings limit" value="{{ old('listing_limit') }}" required>
                                @error('listing_limit')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gallery_images" class="form-label">Images Limit <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" id="gallery_images" name="gallery_images" placeholder="Enter gallery images limit" value="{{ old('gallery_images') }}" required>
                                @error('gallery_images')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="featured_listing" class="form-label">Featured Listing Limit <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" id="featured_listing" name="featured_listing" placeholder="Enter featured listing limit" value="{{ old('featured_listing', 0) }}" required>
                                @error('featured_listing')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="image" class="form-label">Membership Image</label>
                                <input type="file" class="form-control" id="image" name="image">
                                <small class="text-muted">Recommended size: 200x200px</small>
                                @error('image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="divider">
                                <div class="divider-text">Additional Permissions</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="enquiry_form" name="enquiry_form" {{ old('enquiry_form') ? 'checked' : '' }}>
                                <label class="form-check-label" for="enquiry_form">Enable Enquiry Form</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="business_hour" name="business_hour" {{ old('business_hour') ? 'checked' : '' }}>
                                <label class="form-check-label" for="business_hour">Enable Business Hours</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="membership_badge" name="membership_badge" {{ old('membership_badge') ? 'checked' : '' }}>
                                <label class="form-check-label" for="membership_badge">Enable Membership Badge</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="divider">
                                <div class="divider-text">Membership Features</div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div id="features">
                                <div class="feature-item mb-3 row align-items-center">
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="feature[]" placeholder="Enter feature">
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="status[]">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-icon remove-feature">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" id="add-feature" class="btn btn-primary mt-2">
                                <i class="ti ti-plus me-1"></i>Add Feature
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">Save Membership</button>
                        <a href="{{ route('admin.membership.all') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2
            if (document.querySelector('.select2')) {
                $('.select2').select2();
            }

            // Feature addition and removal
            const featureContainer = document.getElementById('features');
            const addFeatureBtn = document.getElementById('add-feature');

            // Get the first feature item as a template
            const featureTemplate = featureContainer.querySelector('.feature-item').cloneNode(true);

            // Add new feature
            addFeatureBtn.addEventListener('click', function() {
                const newFeature = featureTemplate.cloneNode(true);
                newFeature.querySelector('input[name="feature[]"]').value = '';
                newFeature.querySelector('input[name="status[]"]').checked = false;
                
                // Add remove event listener
                newFeature.querySelector('.remove-feature').addEventListener('click', function() {
                    if (featureContainer.querySelectorAll('.feature-item').length > 1) {
                        this.closest('.feature-item').remove();
                    } else {
                        alert('You need at least one feature');
                    }
                });
                
                featureContainer.appendChild(newFeature);
            });

            // Add remove event listeners to initial feature items
            document.querySelectorAll('.remove-feature').forEach(button => {
                button.addEventListener('click', function() {
                    if (featureContainer.querySelectorAll('.feature-item').length > 1) {
                        this.closest('.feature-item').remove();
                    } else {
                        alert('You need at least one feature');
                    }
                });
            });
        });
    </script>
@endsection