@extends('layouts/layoutMaster')

@section('title', 'Edit Membership')

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
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Membership - {{ $membership_details->title }}</h5>
                <a href="{{ route('admin.membership.all') }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Back to Memberships
                </a>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('admin.membership.edit', $membership_details->id) }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label">Membership Type <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select select2" required>
                            <option value="">Select Type</option>
                            @foreach($all_types as $type)
                                <option value="{{ $type->id }}" {{ $membership_details->membership_type_id == $type->id ? 'selected' : '' }}>{{ $type->type }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" value="{{ $membership_details->title }}" placeholder="Enter title" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="price" value="{{ $membership_details->price }}" step="0.01" placeholder="0.00" required>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Listings Limit <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="listing_limit" value="{{ $membership_details->listing_limit }}" min="1" placeholder="Enter listings limit" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Featured Listings Limit</label>
                        <input type="number" class="form-control" name="featured_listing" value="{{ $membership_details->featured_listing }}" min="0" placeholder="Enter featured listings limit">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Gallery Images Limit</label>
                        <input type="number" class="form-control" name="gallery_images" value="{{ $membership_details->gallery_images }}" min="0" placeholder="Enter gallery images limit">
                    </div>
                    
                    <div class="col-md-8">
                        <label class="form-label">Image URL (Optional)</label>
                        <input type="text" class="form-control" name="image" value="{{ $membership_details->image }}" placeholder="Enter image URL">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label d-block">Enquiry Form</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enquiry_form" name="enquiry_form" {{ $membership_details->enquiry_form ? 'checked' : '' }}>
                            <label class="form-check-label" for="enquiry_form">Allow enquiry form</label>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label d-block">Business Hours</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="business_hour" name="business_hour" {{ $membership_details->business_hour ? 'checked' : '' }}>
                            <label class="form-check-label" for="business_hour">Allow business hours</label>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label d-block">Membership Badge</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="membership_badge" name="membership_badge" {{ $membership_details->membership_badge ? 'checked' : '' }}>
                            <label class="form-check-label" for="membership_badge">Display membership badge</label>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Features <span class="text-danger">*</span></label>
                        <div id="features">
                            @foreach($membership_details->features as $index => $feature)
                                <div class="feature-row d-flex mb-2 align-items-center gap-2">
                                    <input type="text" name="feature[]" class="form-control" value="{{ $feature->feature }}" placeholder="Enter feature" required>
                                    <div class="form-check">
                                        <input type="checkbox" name="status[]" class="form-check-input feature-check" id="feature-status-{{ $index }}" {{ $feature->status == 'on' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature-status-{{ $index }}">Active</label>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-icon remove-feature">
                                        <i class="ti ti-x"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline-primary mt-2" id="add-feature">
                            <i class="ti ti-plus me-1"></i>Add Feature
                        </button>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary">Update Membership</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection