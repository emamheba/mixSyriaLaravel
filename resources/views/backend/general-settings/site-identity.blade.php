@extends('layouts/layoutMaster')

@section('title', __('Site Identity Settings'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/dropzone/dropzone.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/dropzone/dropzone.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image upload functionality
    const imageWrappers = document.querySelectorAll('.image-upload-wrapper');
    
    imageWrappers.forEach(wrapper => {
        const input = wrapper.querySelector('.image-input');
        const preview = wrapper.querySelector('.image-preview');
        const removeBtn = wrapper.querySelector('.remove-image');
        const uploadArea = wrapper.querySelector('.upload-area');
        const uploadPlaceholder = wrapper.querySelector('.upload-placeholder');
        const chooseBtn = wrapper.querySelector('.choose-file-btn');
        
        // Handle file input change
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (2MB for logos, 1MB for favicon)
                const maxSize = wrapper.closest('.col-lg-4').querySelector('.card-title').textContent.includes('Favicon') ? 1024 * 1024 : 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    toastr.error('{{ __("File size too large") }}');
                    input.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    removeBtn.style.display = 'block';
                    uploadPlaceholder.classList.add('d-none');
                    uploadArea.classList.add('has-image');
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Handle remove button
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            input.value = '';
            preview.style.display = 'none';
            removeBtn.style.display = 'none';
            uploadPlaceholder.classList.remove('d-none');
            uploadArea.classList.remove('has-image');
        });
        
        // Handle choose file button
        chooseBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            input.click();
        });
        
        // Handle upload area click
        uploadArea.addEventListener('click', function() {
            input.click();
        });
        
        // Drag and drop functionality
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('drag-over');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                input.dispatchEvent(new Event('change'));
            }
        });
    });
    
    // Form submission with loading state
    const form = document.getElementById('siteIdentityForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form && submitBtn) {
        const originalText = submitBtn.innerHTML;
        
        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("Updating...") }}';
            
            // Reset button after 10 seconds if something goes wrong
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }, 10000);
        });
    }
});
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div>
      <h4 class="fw-bold mb-1">
        <i class="ti ti-photo me-2 text-primary"></i>
        {{ __('Site Identity Settings') }}
      </h4>
      <p class="text-muted mb-0">{{ __('Manage your website branding and visual identity') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-label-primary fs-6">
        <i class="ti ti-settings me-1"></i>
        {{ __('General Settings') }}
      </span>
    </div>
  </div>

  <!-- Main Content -->
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between border-bottom">
          <h5 class="card-title mb-0 d-flex align-items-center">
            <div class="avatar avatar-sm me-2">
              <span class="avatar-initial rounded bg-label-primary">
                <i class="ti ti-photo"></i>
              </span>
            </div>
            {{ __('Brand Assets') }}
          </h5>
          <span class="badge bg-label-warning">
            <i class="ti ti-star-filled me-1"></i>
            {{ __('Required') }}
          </span>
        </div>
        
        <div class="card-body">
          <!-- Validation Errors -->
          @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <div class="d-flex align-items-start">
                <div class="avatar avatar-xs me-2 mt-1">
                  <span class="avatar-initial rounded bg-danger">
                    <i class="ti ti-alert-circle ti-xs"></i>
                  </span>
                </div>
                <div class="flex-grow-1">
                  <h6 class="alert-heading mb-2">{{ __('Please fix the following errors:') }}</h6>
                  <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                      <li class="mb-1">{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              </div>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          <!-- Success Message -->
          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <div class="d-flex align-items-center">
                <div class="avatar avatar-xs me-2">
                  <span class="avatar-initial rounded bg-success">
                    <i class="ti ti-check ti-xs"></i>
                  </span>
                </div>
                <div class="flex-grow-1">
                  {{ session('success') }}
                </div>
              </div>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          <form id="siteIdentityForm" action="{{ route('admin.general.site.identity') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row g-4">
              <!-- Site Logo -->
              <div class="col-lg-4 col-md-6">
                <div class="card border h-100 hover-shadow">
                  <div class="card-header pb-2 border-bottom-0">
                    <div class="d-flex align-items-center justify-content-between">
                      <h6 class="card-title mb-0 d-flex align-items-center">
                        <i class="ti ti-brand-adobe me-2 text-primary"></i>
                        {{ __('Site Logo') }}
                      </h6>
                      <span class="badge bg-label-info fs-tiny">160x50</span>
                    </div>
                    <small class="text-muted">{{ __('Main brand logo for light backgrounds') }}</small>
                  </div>
                  <div class="card-body pt-2">
                    <div class="image-upload-wrapper">
                      <div class="upload-area border-dashed border-2 rounded-3 p-4 text-center position-relative">
                        <input type="file" name="site_logo" class="image-input d-none" accept="image/*">
                        <input type="hidden" name="site_logo_id" value="{{ get_static_option('site_logo') }}">
                        
                        @php $siteLogo = get_attachment_image_by_id(get_static_option('site_logo'), 'thumb'); @endphp
                        <img class="image-preview mb-3 rounded" 
                             src="{{ $siteLogo['img_url'] ?? '' }}" 
                             alt="Site Logo Preview" 
                             style="max-height: 80px; {{ empty($siteLogo['img_url']) ? 'display: none;' : '' }}">
                        
                        <div class="upload-placeholder {{ !empty($siteLogo['img_url']) ? 'd-none' : '' }}">
                          <div class="avatar avatar-lg mx-auto mb-3">
                            <span class="avatar-initial rounded bg-label-primary">
                              <i class="ti ti-cloud-upload ti-lg"></i>
                            </span>
                          </div>
                          <h6 class="mb-2">{{ __('Upload Site Logo') }}</h6>
                          <p class="text-muted small mb-3">{{ __('Drag & drop or click to browse') }}</p>
                          <small class="text-muted">{{ __('PNG, JPG up to 2MB') }}</small>
                        </div>
                        
                        <button type="button" class="btn btn-sm btn-outline-danger remove-image position-absolute" style="top: 8px; right: 8px; {{ empty($siteLogo['img_url']) ? 'display: none;' : '' }}">
                          <i class="ti ti-x ti-xs"></i>
                        </button>
                        
                        <button type="button" class="btn btn-primary btn-sm choose-file-btn mt-2 waves-effect waves-light">
                          <i class="ti ti-upload me-1"></i>
                          {{ __('Choose File') }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Site White Logo -->
              <div class="col-lg-4 col-md-6">
                <div class="card border h-100 hover-shadow">
                  <div class="card-header pb-2 border-bottom-0">
                    <div class="d-flex align-items-center justify-content-between">
                      <h6 class="card-title mb-0 d-flex align-items-center">
                        <i class="ti ti-brand-adobe me-2 text-white bg-dark rounded p-1"></i>
                        {{ __('Site White Logo') }}
                      </h6>
                      <span class="badge bg-label-info fs-tiny">160x50</span>
                    </div>
                    <small class="text-muted">{{ __('White version for dark backgrounds') }}</small>
                  </div>
                  <div class="card-body pt-2">
                    <div class="image-upload-wrapper">
                      <div class="upload-area upload-area-dark border-dashed border-2 rounded-3 p-4 text-center position-relative bg-dark">
                        <input type="file" name="site_white_logo" class="image-input d-none" accept="image/*">
                        <input type="hidden" name="site_white_logo_id" value="{{ get_static_option('site_white_logo') }}">
                        
                        @php $siteWhiteLogo = get_attachment_image_by_id(get_static_option('site_white_logo'), 'thumb'); @endphp
                        <img class="image-preview mb-3 rounded" 
                             src="{{ $siteWhiteLogo['img_url'] ?? '' }}" 
                             alt="White Logo Preview" 
                             style="max-height: 80px; {{ empty($siteWhiteLogo['img_url']) ? 'display: none;' : '' }}">
                        
                        <div class="upload-placeholder text-white {{ !empty($siteWhiteLogo['img_url']) ? 'd-none' : '' }}">
                          <div class="avatar avatar-lg mx-auto mb-3">
                            <span class="avatar-initial rounded bg-light">
                              <i class="ti ti-cloud-upload ti-lg text-dark"></i>
                            </span>
                          </div>
                          <h6 class="mb-2">{{ __('Upload White Logo') }}</h6>
                          <p class="small mb-3 opacity-75">{{ __('Drag & drop or click to browse') }}</p>
                          <small class="opacity-75">{{ __('PNG, JPG up to 2MB') }}</small>
                        </div>
                        
                        <button type="button" class="btn btn-sm btn-outline-light remove-image position-absolute" style="top: 8px; right: 8px; {{ empty($siteWhiteLogo['img_url']) ? 'display: none;' : '' }}">
                          <i class="ti ti-x ti-xs"></i>
                        </button>
                        
                        <button type="button" class="btn btn-light btn-sm choose-file-btn mt-2 waves-effect waves-light">
                          <i class="ti ti-upload me-1"></i>
                          {{ __('Choose File') }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Favicon -->
              <div class="col-lg-4 col-md-6">
                <div class="card border h-100 hover-shadow">
                  <div class="card-header pb-2 border-bottom-0">
                    <div class="d-flex align-items-center justify-content-between">
                      <h6 class="card-title mb-0 d-flex align-items-center">
                        <i class="ti ti-device-desktop me-2 text-success"></i>
                        {{ __('Favicon') }}
                      </h6>
                      <span class="badge bg-label-info fs-tiny">40x40</span>
                    </div>
                    <small class="text-muted">{{ __('Browser tab icon') }}</small>
                  </div>
                  <div class="card-body pt-2">
                    <div class="image-upload-wrapper">
                      <div class="upload-area border-dashed border-2 rounded-3 p-4 text-center position-relative">
                        <input type="file" name="site_favicon" class="image-input d-none" accept="image/*">
                        <input type="hidden" name="site_favicon_id" value="{{ get_static_option('site_favicon') }}">
                        
                        @php $siteFavicon = get_attachment_image_by_id(get_static_option('site_favicon'), 'thumb'); @endphp
                        <img class="image-preview mb-3 rounded-2" 
                             src="{{ $siteFavicon['img_url'] ?? '' }}" 
                             alt="Favicon Preview" 
                             style="max-height: 50px; {{ empty($siteFavicon['img_url']) ? 'display: none;' : '' }}">
                        
                        <div class="upload-placeholder {{ !empty($siteFavicon['img_url']) ? 'd-none' : '' }}">
                          <div class="avatar avatar-lg mx-auto mb-3">
                            <span class="avatar-initial rounded bg-label-success">
                              <i class="ti ti-device-desktop ti-lg"></i>
                            </span>
                          </div>
                          <h6 class="mb-2">{{ __('Upload Favicon') }}</h6>
                          <p class="text-muted small mb-3">{{ __('Drag & drop or click to browse') }}</p>
                          <small class="text-muted">{{ __('ICO, PNG up to 1MB') }}</small>
                        </div>
                        
                        <button type="button" class="btn btn-sm btn-outline-danger remove-image position-absolute" style="top: 8px; right: 8px; {{ empty($siteFavicon['img_url']) ? 'display: none;' : '' }}">
                          <i class="ti ti-x ti-xs"></i>
                        </button>
                        
                        <button type="button" class="btn btn-primary btn-sm choose-file-btn mt-2 waves-effect waves-light">
                          <i class="ti ti-upload me-1"></i>
                          {{ __('Choose File') }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Guidelines -->
            <div class="row mt-5">
              <div class="col-12">
                <div class="alert alert-primary border-0" role="alert">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded bg-primary">
                          <i class="ti ti-info-circle"></i>
                        </span>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="alert-heading mb-2">
                        <i class="ti ti-bulb me-1"></i>
                        {{ __('Image Guidelines & Best Practices') }}
                      </h6>
                      <div class="row">
                        <div class="col-md-6">
                          <ul class="mb-0">
                            <li class="mb-1">{{ __('Use high-quality images for better visual appeal') }}</li>
                            <li class="mb-1">{{ __('Site Logo: Horizontal format, transparent background preferred') }}</li>
                            <li class="mb-1">{{ __('White Logo: Same design as main logo but in white color') }}</li>
                          </ul>
                        </div>
                        <div class="col-md-6">
                          <ul class="mb-0">
                            <li class="mb-1">{{ __('Favicon: Square format, simple design works best') }}</li>
                            <li class="mb-1">{{ __('Supported formats: PNG, JPG, ICO (favicon only)') }}</li>
                            <li class="mb-1">{{ __('Optimize images for web to reduce loading time') }}</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
              <div class="col-12">
                <div class="d-flex justify-content-end gap-3">
                  <button type="button" class="btn btn-outline-secondary waves-effect" onclick="window.location.reload()">
                    <i class="ti ti-refresh me-2"></i>
                    {{ __('Reset Changes') }}
                  </button>
                  <button type="submit" id="submitBtn" class="btn btn-primary waves-effect waves-light">
                    <i class="ti ti-device-floppy me-2"></i>
                    {{ __('Update Changes') }}
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Upload Area Styles */
.upload-area {
  cursor: pointer;
  transition: all 0.3s ease;
  min-height: 200px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  border-color: var(--bs-border-color) !important;
}

.upload-area:hover {
  border-color: var(--bs-primary) !important;
  background-color: rgba(var(--bs-primary-rgb), 0.05);
  transform: translateY(-1px);
}

.upload-area.drag-over {
  border-color: var(--bs-primary) !important;
  background-color: rgba(var(--bs-primary-rgb), 0.1);
  transform: scale(1.02);
}

.upload-area.has-image {
  border-color: var(--bs-success) !important;
  background-color: rgba(var(--bs-success-rgb), 0.05);
}

.upload-area-dark:hover {
  border-color: var(--bs-light) !important;
  background-color: rgba(255, 255, 255, 0.1);
}

/* Image Preview */
.image-preview {
  max-width: 100%;
  border-radius: var(--bs-border-radius);
  box-shadow: 0 2px 12px rgba(0,0,0,0.15);
  transition: all 0.3s ease;
}

.image-preview:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}

/* Border Dashed */
.border-dashed {
  border-style: dashed !important;
  border-width: 2px !important;
}

/* Card Enhancements */
.card {
  transition: all 0.3s ease;
  border: 1px solid var(--bs-border-color);
}

.hover-shadow:hover {
  box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
  transform: translateY(-2px);
}

/* Alert Enhancements */
.alert {
  border-radius: var(--bs-border-radius-lg);
  border: none;
}

/* Button Enhancements */
.btn {
  border-radius: var(--bs-border-radius);
  font-weight: 500;
  transition: all 0.2s ease;
}

.btn:hover {
  transform: translateY(-1px);
}

/* Badge Enhancements */
.badge {
  font-weight: 500;
  letter-spacing: 0.5px;
}

.fs-tiny {
  font-size: 0.7rem !important;
}

/* Avatar Enhancements */
.avatar-initial {
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .upload-area {
    min-height: 150px;
    padding: 1.5rem !important;
  }
  
  .card-header {
    padding: 1rem;
  }
  
  .card-body {
    padding: 1rem;
  }
}

/* Loading Animation */
@keyframes pulse-border {
  0% { border-color: var(--bs-primary); }
  50% { border-color: rgba(var(--bs-primary-rgb), 0.5); }
  100% { border-color: var(--bs-primary); }
}

.upload-area:focus-within {
  animation: pulse-border 2s infinite;
}

/* File Input Focus */
.image-input:focus + .upload-area {
  border-color: var(--bs-primary) !important;
  box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}
</style>

@endsection