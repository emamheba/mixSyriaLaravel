@extends('layouts/layoutMaster')

@section('title', __('Login Register Settings'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2_activation').select2({
            theme: 'bootstrap-5',
            placeholder: '{{ __("Select Page") }}',
            allowClear: true
        });
    }

    // Image upload functionality - Updated to match new wrapper structure
    const imageUploadWrappers = document.querySelectorAll('.custom-image-upload-wrapper');

    imageUploadWrappers.forEach(wrapper => {
        const input = wrapper.querySelector('.image-input');
        const preview = wrapper.querySelector('.image-preview');
        const removeBtn = wrapper.querySelector('.remove-image');
        const uploadArea = wrapper.querySelector('.upload-area');
        const uploadText = wrapper.querySelector('.upload-text');
        const placeholderIcon = wrapper.querySelector('.upload-placeholder-icon');


        // Function to update display based on image presence
        const updateDisplay = (imageUrl = null) => {
             if (imageUrl) {
                preview.src = imageUrl;
                preview.style.display = 'block';
                if(removeBtn) removeBtn.style.display = 'block';
                if(uploadArea) uploadArea.classList.add('has-image');
                if(uploadText) uploadText.classList.add('d-none');
                 if(placeholderIcon) placeholderIcon.classList.add('d-none');
            } else {
                preview.src = '';
                preview.style.display = 'none';
                 if(removeBtn) removeBtn.style.display = 'none';
                if(uploadArea) uploadArea.classList.remove('has-image');
                if(uploadText) uploadText.classList.remove('d-none');
                if(placeholderIcon) placeholderIcon.classList.remove('d-none');
            }
        };

        // Initial display based on current image source
        if(preview && preview.src && preview.src !== window.location.href) { // Check if src is actually set
             updateDisplay(preview.src);
        } else {
            updateDisplay(null); // Ensure placeholder is shown if no image
        }


        // Handle file input change
        if (input) {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file size (2MB)
                    const maxSize = 2 * 1024 * 1024;
                    if (file.size > maxSize) {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('{{ __("File size too large") }}');
                        } else {
                            alert('{{ __("File size too large") }}');
                        }
                        input.value = ''; // Clear the input
                        updateDisplay(null); // Reset display
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        updateDisplay(e.target.result);
                    };
                    reader.readAsDataURL(file);
                } else {
                    // If file input is cleared without selecting a file
                    updateDisplay(null);
                }
            });
        }

        // Handle remove button click
        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent form submission if inside form
                e.stopPropagation(); // Prevent triggering uploadArea click
                if (input) input.value = ''; // Clear file input
                updateDisplay(null); // Reset display
                 // You might want to signal backend to remove the old image here if it's a persistent setting
                 // For now, this only clears the client-side view and input
            });
        }

        // Handle upload area click
         if (uploadArea && input) { // Make sure both exist
            uploadArea.addEventListener('click', function(e) {
                 // Only trigger input click if the remove button or input itself wasn't clicked
                if (!e.target.closest('.remove-image') && e.target !== input) {
                     input.click();
                 }
            });
         }


        // Drag and drop functionality
        if (uploadArea) {
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
                if (files.length > 0 && input) {
                    // Assign files and trigger change event
                    input.files = files;
                    const changeEvent = new Event('change', { bubbles: true });
                    input.dispatchEvent(changeEvent);
                }
            });
        }
    });


    // Form submission with loading state
    const form = document.getElementById('loginRegisterForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form && submitBtn) {
        const originalText = submitBtn.innerHTML;

        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("Updating...") }}';

            // Reset button after 10 seconds if something goes wrong (e.g., network error, server timeout)
            // This is a fallback; proper success/error handling should reset the button
            const timeout = setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }, 10000); // 10 seconds

             // You might need to clear this timeout on actual form success/error response if you use AJAX
        });
    }

    // Toggle switch functionality - Simplified
    const socialLoginToggle = document.getElementById('socialLoginToggle');
    const socialLoginWrapper = document.getElementById('socialLoginWrapper');

    if (socialLoginToggle && socialLoginWrapper) {
        // Function to update wrapper class
        const updateSocialLoginWrapper = () => {
            if (socialLoginToggle.checked) {
                socialLoginWrapper.classList.add('active');
            } else {
                socialLoginWrapper.classList.remove('active');
            }
        };

        // Initial state
        updateSocialLoginWrapper();

        // Listen for changes
        socialLoginToggle.addEventListener('change', updateSocialLoginWrapper);
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
        <i class="ti ti-login me-2 text-primary"></i>
        {{ __('Login Register Settings') }}
      </h4>
      <p class="text-muted mb-0">{{ __('Configure login and registration page settings') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
       <span class="badge bg-label-primary fs-6">
        <i class="ti ti-settings me-1"></i>
        {{ __('Authentication Settings') }}
      </span>
    </div>
  </div>

  <!-- Main Content Card -->
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <!-- Validation Errors -->
      @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
          <div class="d-flex align-items-start">
            <div class="avatar avatar-xs me-2 mt-1">
              <span class="avatar-initial rounded bg-danger">
                <i class="ti ti-alert-circle ti-xs"></i>
              </span>
            </div>
            <div class="flex-grow-1">
              <h6 class="alert-heading fw-bold mb-2">{{ __('Please fix the following errors:') }}</h6>
              <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <!-- Success Message -->
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
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

      <form id="loginRegisterForm" action="{{ route('admin.login.register.page.settings') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs nav-justified border-bottom mb-4" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="register-tab" data-bs-toggle="tab" href="#register-settings" role="tab" aria-controls="register-settings" aria-selected="true">
              <i class="ti ti-user-plus me-1"></i> {{ __('Register Page') }}
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="login-tab" data-bs-toggle="tab" href="#login-settings" role="tab" aria-controls="login-settings" aria-selected="false">
              <i class="ti ti-login me-1"></i> {{ __('Login Form') }}
            </a>
          </li>
           <li class="nav-item">
            <a class="nav-link" id="terms-tab" data-bs-toggle="tab" href="#terms-settings" role="tab" aria-controls="terms-settings" aria-selected="false">
              <i class="ti ti-file-text me-1"></i> {{ __('Terms & Conditions') }}
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="social-tab" data-bs-toggle="tab" href="#social-settings" role="tab" aria-controls="social-settings" aria-selected="false">
              <i class="ti ti-share me-1"></i> {{ __('Social Login') }}
            </a>
          </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content p-0">

          <!-- Register Page Settings Tab -->
          <div class="tab-pane fade active show" id="register-settings" role="tabpanel" aria-labelledby="register-tab">
            <div class="row g-4">
              <!-- Register Page Title -->
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text"
                         name="register_page_title"
                         id="register_page_title"
                         class="form-control"
                         value="{{ get_static_option('register_page_title') }}"
                         placeholder="{{ __('Enter register page title') }}">
                  <label for="register_page_title">
                    <i class="ti ti-heading me-1"></i>
                    {{ __('Register Page Title') }}
                  </label>
                </div>
              </div>

              <!-- Register Page Description -->
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text"
                         name="register_page_description"
                         id="register_page_description"
                         class="form-control"
                         value="{{ get_static_option('register_page_description') }}"
                         placeholder="{{ __('Enter register page description') }}">
                  <label for="register_page_description">
                    <i class="ti ti-file-text me-1"></i>
                    {{ __('Register Page Description') }}
                  </label>
                </div>
              </div>

              <!-- Register Page Image -->
              <div class="col-12">
                <label class="form-label fw-medium mb-3">
                  <i class="ti ti-photo me-1 text-primary"></i>
                  {{ __('Register Page Image') }}
                  <span class="badge bg-label-info ms-2">Recommended: 160x50px</span>
                </label>
                <div class="custom-image-upload-wrapper">
                  <div class="upload-area border-dashed border-2 rounded-3 p-4 text-center position-relative">
                    <input type="file" name="register_page_image" class="image-input d-none" accept="image/*">

                    @php $registerImage = get_attachment_image_by_id(get_static_option('register_page_image'), 'thumb'); @endphp
                    <img class="image-preview mb-3 rounded"
                         src="{{ $registerImage['img_url'] ?? '' }}"
                         alt="{{ __('Register Image Preview') }}"
                         style="max-height: 80px; {{ empty($registerImage['img_url']) ? 'display: none;' : '' }}">

                    <div class="upload-placeholder">
                      <div class="avatar avatar-lg mx-auto mb-3">
                        <span class="avatar-initial rounded bg-label-primary upload-placeholder-icon">
                          <i class="ti ti-cloud-upload ti-lg"></i>
                        </span>
                      </div>
                      <div class="upload-text">
                        <h6 class="mb-2">{{ __('Drag & drop or click to upload') }}</h6>
                        <p class="text-muted small mb-0">{{ __('PNG, JPG up to 2MB') }}</p>
                      </div>
                       <button type="button" class="btn btn-primary btn-sm mt-3 waves-effect waves-light choose-file-btn">
                        <i class="ti ti-upload me-1"></i>
                        {{ __('Choose File') }}
                       </button>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-danger remove-image position-absolute top-0 end-0 m-2" style="{{ empty($registerImage['img_url']) ? 'display: none;' : '' }}">
                      <i class="ti ti-x ti-xs"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Login Settings Tab -->
          <div class="tab-pane fade" id="login-settings" role="tabpanel" aria-labelledby="login-tab">
             <div class="row g-4">
               <!-- Login Form Title -->
               <div class="col-lg-8">
                 <div class="form-floating form-floating-outline">
                   <input type="text"
                          name="login_form_title"
                          id="login_form_title"
                          class="form-control"
                          value="{{ get_static_option('login_form_title') }}"
                          placeholder="{{ __('Enter login form title') }}">
                   <label for="login_form_title">
                     <i class="ti ti-forms me-1"></i>
                     {{ __('Login Form Title') }}
                   </label>
                 </div>
               </div>
             </div>
          </div>

          <!-- Terms & Conditions Tab -->
          <div class="tab-pane fade" id="terms-settings" role="tabpanel" aria-labelledby="terms-tab">
             <div class="row g-4">
               <div class="col-lg-8">
                 @php
                   $all_pages = \App\Models\Backend\Page::select('id','title','slug')->latest()->get();
                 @endphp
                 <div class="form-floating form-floating-outline">
                   <select name="select_terms_condition_page" id="select_terms_condition_page" class="form-select select2_activation">
                     <option value="">{{ __('Select Page') }}</option>
                     @foreach($all_pages as $page)
                       <option @if(get_static_option('select_terms_condition_page') == $page->slug) selected @endif value="{{ $page->slug }}">{{ $page->title }}</option>
                     @endforeach
                   </select>
                   <label for="select_terms_condition_page">
                     <i class="ti ti-page-break me-1"></i>
                     {{ __('Set Terms & Condition Page') }}
                   </label>
                 </div>
               </div>
             </div>
          </div>

          <!-- Social Login Settings Tab -->
          <div class="tab-pane fade" id="social-settings" role="tabpanel" aria-labelledby="social-tab">
            <div class="row g-4">
              <div class="col-12">
                <div id="socialLoginWrapper" class="p-4 rounded-3 border bg-label-light transition-border">
                  <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-grow-1 me-3">
                      <div class="d-flex align-items-center mb-2">
                         <div class="avatar avatar-sm me-3">
                           <span class="avatar-initial rounded bg-label-info">
                             <i class="ti ti-brand-google"></i>
                           </span>
                         </div>
                         <h6 class="mb-0 fw-bold">{{ __('Social Login on Register Page') }}</h6>
                      </div>
                      <p class="text-muted mb-0 small">
                        {{ __('Enable to show social login options (Google, Facebook, etc.) on the registration page') }}
                      </p>
                    </div>
                    <div class="flex-shrink-0">
                      <div class="form-check form-switch form-check-reverse">
                        <input class="form-check-input"
                               type="checkbox"
                               name="register_page_social_login_show_hide"
                               id="socialLoginToggle"
                               value="1"
                               {{ !empty(get_static_option('register_page_social_login_show_hide')) ? 'checked' : '' }}>
                        <label class="form-check-label" for="socialLoginToggle"></label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!-- /Tab Content -->

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

{{-- Custom Styles for the new design --}}
@section('page-style')
<style>
/* Header Styling */
.container-p-y > .d-flex.flex-column.flex-md-row {
    padding-bottom: 0 !important; /* Adjust spacing below header if needed */
}

/* Main Card Styling */
.card.shadow-sm {
    border: none; /* Remove outer border */
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08) !important; /* Softer, more prominent shadow */
}

.card-body {
    padding: 2rem !important; /* Increased padding */
}


/* Tab Navigation Styling */
.nav-tabs {
    border-bottom: 1px solid var(--bs-border-color);
    margin-bottom: 2rem; /* Space below tabs */
}

.nav-tabs .nav-link {
    border: none; /* Remove individual link borders */
    border-bottom: 2px solid transparent; /* Bottom border for active indicator */
    color: var(--bs-body-color); /* Default text color */
    padding: 1rem 1.5rem; /* Adjust padding */
    transition: color 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-tabs .nav-link:hover:not(.active) {
    color: var(--bs-primary); /* Highlight color on hover */
    border-color: rgba(var(--bs-primary-rgb), 0.2); /* Subtle border on hover */
}

.nav-tabs .nav-link.active {
    color: var(--bs-primary); /* Active text color */
    border-color: var(--bs-primary); /* Active indicator color */
    background-color: transparent; /* No background on active */
     font-weight: 600;
}

.nav-tabs .nav-link i {
    font-size: 1.1rem; /* Adjust icon size */
}

/* Tab Content Styling */
.tab-content {
    padding: 0 !important; /* Remove default tab content padding */
}

.tab-pane {
    padding-top: 0.5rem; /* Space above content in pane */
}

/* Form Floating Outline Enhancements */
.form-floating-outline > .form-control,
.form-floating-outline > .form-select {
  background-color: var(--bs-body-bg);
  border: 1px solid var(--bs-border-color);
  transition: all 0.2s ease;
}

.form-floating-outline > .form-control:focus,
.form-floating-outline > .form-select:focus {
  border-color: var(--bs-primary);
  box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

/* Custom Image Upload Area */
.custom-image-upload-wrapper .upload-area {
    cursor: pointer;
    transition: all 0.3s ease;
    min-height: 180px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border-color: var(--bs-border-color) !important;
    background-color: var(--bs-gray-100); /* Light background */
    position: relative; /* For remove button positioning */
}

.custom-image-upload-wrapper .upload-area:hover {
    border-color: var(--bs-primary) !important;
    background-color: rgba(var(--bs-primary-rgb), 0.05);
}

.custom-image-upload-wrapper .upload-area.drag-over {
    border-color: var(--bs-primary) !important;
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}

.custom-image-upload-wrapper .upload-area.has-image {
    border-color: var(--bs-success) !important;
    background-color: var(--bs-white); /* White background when image is present */
}

.custom-image-upload-wrapper .image-preview {
    max-width: 100%;
    max-height: 120px; /* Larger preview */
    border-radius: var(--bs-border-radius);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.custom-image-upload-wrapper .upload-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.custom-image-upload-wrapper .upload-placeholder-icon .ti {
    font-size: 2rem; /* Larger icon */
}

.custom-image-upload-wrapper .upload-text h6 {
    font-weight: 600;
}

.custom-image-upload-wrapper .remove-image {
    display: none; /* Hidden by default, shown by JS */
    z-index: 2; /* Above image */
    opacity: 0.8;
}

.custom-image-upload-wrapper .remove-image:hover {
    opacity: 1;
}

/* Border Dashed */
.border-dashed {
  border-style: dashed !important;
  border-width: 2px !important;
}

/* Toggle Wrapper Styling */
#socialLoginWrapper {
    transition: all 0.3s ease;
    border: 1px solid var(--bs-border-color) !important; /* Default border */
    background-color: var(--bs-card-bg); /* Default background */
}

#socialLoginWrapper.active {
    border-color: var(--bs-success) !important; /* Success border when active */
    background-color: rgba(var(--bs-success-rgb), 0.05); /* Light success background */
}

#socialLoginWrapper.active .text-muted {
    color: var(--bs-body-color) !important; /* Darker text color when active */
}

#socialLoginWrapper .form-check-input[type=checkbox]:checked {
     background-color: var(--bs-success);
     border-color: var(--bs-success);
}

.transition-border {
    transition: border-color 0.3s ease, background-color 0.3s ease;
}

/* General Improvements */
.badge {
  font-weight: 500;
  letter-spacing: 0.5px;
}

.avatar-initial {
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Select2 Enhancements */
/* Ensure Select2 matches Bootstrap 5 form-floating height */
.select2-container--bootstrap-5 .select2-selection {
  min-height: calc(3.5rem + 2px) !important; /* Match form-floating height */
  border-radius: var(--bs-border-radius) !important;
  padding-top: 1rem !important; /* Adjust padding for label */
}

.select2-container--bootstrap-5 .select2-selection__rendered {
    line-height: 1.5 !important;
    padding-left: 0.875rem !important; /* Match default input padding */
    color: var(--bs-body-color) !important;
}

.select2-container--bootstrap-5 .select2-selection__arrow b {
    top: 65% !important; /* Adjust arrow position */
}

/* Ensure space when select2 is in form-floating */
.form-floating-outline > .select2-container--bootstrap-5 {
    padding-top: 0.875rem; /* Add space for the label */
    padding-bottom: 0.875rem;
}

.form-floating-outline > label {
     z-index: 2; /* Ensure label is above select2 */
}

.form-floating-outline > .select2-container--bootstrap-5 .select2-selection {
    border-color: inherit; /* Let the parent handle focus border */
    box-shadow: none;
}


/* Responsive Adjustments */
@media (max-width: 768px) {
  .card-body {
    padding: 1.5rem !important;
  }

  .nav-tabs .nav-link {
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
  }

  .nav-tabs .nav-link i {
      font-size: 1rem;
  }

  .custom-image-upload-wrapper .upload-area {
    min-height: 150px;
    padding: 1.5rem !important;
  }

  .custom-image-upload-wrapper .image-preview {
     max-height: 60px;
  }

  .custom-image-upload-wrapper .upload-placeholder-icon .ti {
     font-size: 1.5rem;
  }

    #socialLoginWrapper {
        padding: 1.5rem !important;
    }
}

/* Animation for fade-in */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.5s ease-out forwards;
}
.tab-pane.show {
     animation: fadeIn 0.4s ease-out forwards;
}


</style>
@endsection

@endsection