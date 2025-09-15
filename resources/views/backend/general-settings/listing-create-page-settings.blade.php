@extends('layouts/layoutMaster')

@section('title', __('Listing Create Page Settings'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/toastr/toastr.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/toastr/toastr.js'
])
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: "{{ __('Select an option') }}",
        allowClear: false
    });

    // Form submission handler
    $('#listingSettingsForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#updateBtn');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="ti ti-loader ti-xs me-2"></i>{{ __("Updating...") }}');
        
        // Submit form
        setTimeout(() => {
            this.submit();
        }, 500);
    });
});
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row">
    <div class="col-12">
      <!-- Page Header -->
      <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div>
            <h4 class="card-title mb-1">{{ __('Listing Create Page Settings') }}</h4>
            <p class="card-subtitle mb-0 text-muted">{{ __('Configure who can create listings and their default status') }}</p>
          </div>
          <div class="card-action">
            <i class="ti ti-settings ti-lg text-primary"></i>
          </div>
        </div>
      </div>

      <!-- Settings Form -->
      <div class="row">
        <div class="col-xl-8 col-lg-10">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">{{ __('General Settings') }}</h5>
            </div>
            <div class="card-body">
              <!-- Display Validation Errors -->
              @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <div class="alert-body">
                    <h5 class="alert-heading mb-2">
                      <i class="ti ti-alert-circle me-2"></i>{{ __('Please fix the following errors:') }}
                    </h5>
                    <ul class="mb-0">
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              @endif

              <!-- Success Message -->
              @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <div class="alert-body">
                    <i class="ti ti-check me-2"></i>{{ session('success') }}
                  </div>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              @endif

              <form id="listingSettingsForm" action="{{ route('admin.listing.create.settings') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-4">
                  <!-- Who Will Create Listing -->
                  <div class="col-12">
                    <div class="form-floating form-floating-outline">
                      <select name="listing_create_settings" id="listing_create_settings" class="form-select select2" required>
                        <option value="" disabled {{ !get_static_option('listing_create_settings') ? 'selected' : '' }}>
                          {{ __('Select who can create listings') }}
                        </option>
                        <option value="all_user" {{ get_static_option('listing_create_settings') == 'all_user' ? 'selected' : '' }}>
                          <i class="ti ti-users me-2"></i>{{ __('All Users') }}
                        </option>
                        <option value="verified_user" {{ get_static_option('listing_create_settings') == 'verified_user' ? 'selected' : '' }}>
                          <i class="ti ti-user-check me-2"></i>{{ __('Only Verified Users') }}
                        </option>
                      </select>
                      <label for="listing_create_settings">
                        <i class="ti ti-user-cog me-2"></i>{{ __('Who Will Create Listing?') }}
                      </label>
                    </div>
                    <div class="form-text">
                      <i class="ti ti-info-circle me-1"></i>
                      {{ __('Choose whether all users or only verified users can create listings') }}
                    </div>
                  </div>

                  <!-- Default Status -->
                  <div class="col-12">
                    <div class="form-floating form-floating-outline">
                      <select name="listing_create_status_settings" id="listing_create_status_settings" class="form-select select2" required>
                        <option value="" disabled {{ !get_static_option('listing_create_status_settings') ? 'selected' : '' }}>
                          {{ __('Select default status') }}
                        </option>
                        <option value="pending" {{ get_static_option('listing_create_status_settings') == 'pending' ? 'selected' : '' }}>
                          <i class="ti ti-clock me-2"></i>{{ __('Pending') }}
                        </option>
                        <option value="approved" {{ get_static_option('listing_create_status_settings') == 'approved' ? 'selected' : '' }}>
                          <i class="ti ti-check me-2"></i>{{ __('Approved') }}
                        </option>
                      </select>
                      <label for="listing_create_status_settings">
                        <i class="ti ti-settings me-2"></i>{{ __('Default Listing Status') }}
                      </label>
                    </div>
                    <div class="form-text">
                      <i class="ti ti-info-circle me-1 text-info"></i>
                      <span class="text-info">{{ __('Set whether new listings should be automatically approved or require manual review') }}</span>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-top mt-4">
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                      <i class="ti ti-info-circle me-1"></i>
                      {{ __('Changes will take effect immediately after saving') }}
                    </small>
                    <div class="btn-group">
                      <button type="button" class="btn btn-outline-secondary waves-effect" onclick="window.location.reload()">
                        <i class="ti ti-refresh me-2"></i>{{ __('Reset') }}
                      </button>
                      <button type="submit" id="updateBtn" class="btn btn-primary waves-effect waves-light">
                        <i class="ti ti-device-floppy me-2"></i>{{ __('Update Settings') }}
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Side Information Card -->
        <div class="col-xl-4 col-lg-6">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">
                <i class="ti ti-info-circle me-2 text-info"></i>{{ __('Settings Guide') }}
              </h5>
            </div>
            <div class="card-body">
              <div class="timeline timeline-center">
                <div class="timeline-item">
                  <span class="timeline-indicator timeline-indicator-primary">
                    <i class="ti ti-users"></i>
                  </span>
                  <div class="timeline-event">
                    <div class="timeline-header mb-1">
                      <h6 class="mb-0">{{ __('User Access Control') }}</h6>
                    </div>
                    <p class="text-muted mb-0">{{ __('Restrict listing creation to verified users only for better quality control') }}</p>
                  </div>
                </div>
                
                <div class="timeline-item">
                  <span class="timeline-indicator timeline-indicator-info">
                    <i class="ti ti-settings"></i>
                  </span>
                  <div class="timeline-event">
                    <div class="timeline-header mb-1">
                      <h6 class="mb-0">{{ __('Status Management') }}</h6>
                    </div>
                    <p class="text-muted mb-0">{{ __('Set listings to pending for manual review or approved for instant publishing') }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Stats Card -->
          <div class="card mt-4">
            <div class="card-header">
              <h5 class="card-title mb-0">
                <i class="ti ti-chart-bar me-2 text-success"></i>{{ __('Current Settings') }}
              </h5>
            </div>
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">{{ __('Access Level') }}</span>
                <div class="badge bg-label-{{ get_static_option('listing_create_settings') == 'all_user' ? 'success' : 'primary' }} rounded-pill">
                  {{ get_static_option('listing_create_settings') == 'all_user' ? __('All Users') : __('Verified Only') }}
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">{{ __('Default Status') }}</span>
                <div class="badge bg-label-{{ get_static_option('listing_create_status_settings') == 'approved' ? 'success' : 'warning' }} rounded-pill">
                  {{ get_static_option('listing_create_status_settings') == 'approved' ? __('Auto Approved') : __('Pending Review') }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection