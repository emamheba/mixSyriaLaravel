@extends('layouts/layoutMaster')

@section('title', __('Color Settings'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/bootstrap-colorpicker/bootstrap-colorpicker.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/bootstrap-colorpicker/bootstrap-colorpicker.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize color picker for all color inputs
    const colorInputs = document.querySelectorAll('.color-picker-input');
    
    colorInputs.forEach(function(input) {
        // Create color picker wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'input-group';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        
        // Create color preview button
        const colorBtn = document.createElement('button');
        colorBtn.type = 'button';
        colorBtn.className = 'btn btn-outline-secondary border-start-0';
        colorBtn.style.backgroundColor = input.value || '#6366f1';
        colorBtn.style.width = '45px';
        colorBtn.innerHTML = '<i class="ti ti-palette ti-sm"></i>';
        wrapper.appendChild(colorBtn);
        
        // Add color input event listener
        input.addEventListener('input', function() {
            colorBtn.style.backgroundColor = this.value;
        });
        
        // Add click event to color button
        colorBtn.addEventListener('click', function() {
            const colorInput = document.createElement('input');
            colorInput.type = 'color';
            colorInput.value = input.value || '#6366f1';
            colorInput.style.visibility = 'hidden';
            colorInput.style.position = 'absolute';
            document.body.appendChild(colorInput);
            
            colorInput.addEventListener('change', function() {
                input.value = this.value;
                colorBtn.style.backgroundColor = this.value;
                document.body.removeChild(this);
            });
            
            colorInput.click();
        });
    });
    
    // Form submission handler
    const form = document.getElementById('colorSettingsForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>{{ __("Updating...") }}';
        });
    }
});
</script>
@endsection

@section('content')
<div class="row">
  <div class="col-xl-8 col-lg-10 mx-auto">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div>
          <h5 class="card-title mb-0">{{ __('Color Settings') }}</h5>
          <small class="text-muted">{{ __('Customize your website colors and theme appearance') }}</small>
        </div>
        <div class="card-header-elements">
          <span class="badge bg-label-primary">
            <i class="ti ti-palette me-1"></i>{{ __('Theme Colors') }}
          </span>
        </div>
      </div>
      
      <div class="card-body">
        @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
              <i class="ti ti-alert-circle me-2"></i>
              <div>
                <h6 class="alert-heading mb-1">{{ __('Error!') }}</h6>
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
              <i class="ti ti-check-circle me-2"></i>
              <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <form action="{{ route('admin.general.color.settings') }}" method="POST" id="colorSettingsForm">
          @csrf
          
          <!-- Primary Colors Section -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="bg-label-primary rounded p-3 mb-4">
                <h6 class="text-primary mb-2">
                  <i class="ti ti-color-swatch me-2"></i>{{ __('Primary Brand Colors') }}
                </h6>
                <small class="text-muted">{{ __('These colors define your brand identity and primary interface elements') }}</small>
              </div>
            </div>
            
            <div class="col-md-4 mb-3">
              <label class="form-label fw-medium" for="site_main_color_one">
                {{ __('Main Color One') }}
                <i class="ti ti-info-circle ms-1" data-bs-toggle="tooltip" title="{{ __('Primary brand color used for buttons, links, and highlights') }}"></i>
              </label>
              <input type="text" 
                     name="site_main_color_one" 
                     id="site_main_color_one"
                     class="form-control color-picker-input" 
                     value="{{ get_static_option('site_main_color_one', '#6366f1') }}"
                     placeholder="#6366f1">
              <small class="form-text text-muted">{{ __('Primary brand color for buttons and highlights') }}</small>
            </div>
            
            <div class="col-md-4 mb-3">
              <label class="form-label fw-medium" for="site_main_color_two">
                {{ __('Main Color Two') }}
                <i class="ti ti-info-circle ms-1" data-bs-toggle="tooltip" title="{{ __('Secondary brand color for accents and hover states') }}"></i>
              </label>
              <input type="text" 
                     name="site_main_color_two" 
                     id="site_main_color_two"
                     class="form-control color-picker-input" 
                     value="{{ get_static_option('site_main_color_two', '#8b5cf6') }}"
                     placeholder="#8b5cf6">
              <small class="form-text text-muted">{{ __('Secondary color for accents and interactions') }}</small>
            </div>
            
            <div class="col-md-4 mb-3">
              <label class="form-label fw-medium" for="site_main_color_three">
                {{ __('Main Color Three') }}
                <i class="ti ti-info-circle ms-1" data-bs-toggle="tooltip" title="{{ __('Tertiary brand color for additional elements') }}"></i>
              </label>
              <input type="text" 
                     name="site_main_color_three" 
                     id="site_main_color_three"
                     class="form-control color-picker-input" 
                     value="{{ get_static_option('site_main_color_three', '#06b6d4') }}"
                     placeholder="#06b6d4">
              <small class="form-text text-muted">{{ __('Tertiary color for additional design elements') }}</small>
            </div>
          </div>

          <!-- Text Colors Section -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="bg-label-info rounded p-3 mb-4">
                <h6 class="text-info mb-2">
                  <i class="ti ti-typography me-2"></i>{{ __('Typography Colors') }}
                </h6>
                <small class="text-muted">{{ __('Colors for text elements, headings, and content readability') }}</small>
              </div>
            </div>
            
            <div class="col-md-4 mb-3">
              <label class="form-label fw-medium" for="heading_color">
                {{ __('Heading Color') }}
                <i class="ti ti-info-circle ms-1" data-bs-toggle="tooltip" title="{{ __('Color for all heading elements (h1, h2, h3, etc.)') }}"></i>
              </label>
              <input type="text" 
                     name="heading_color" 
                     id="heading_color"
                     class="form-control color-picker-input" 
                     value="{{ get_static_option('heading_color', '#1e293b') }}"
                     placeholder="#1e293b">
              <small class="form-text text-muted">{{ __('Color for titles and headings throughout the site') }}</small>
            </div>
            
            <div class="col-md-4 mb-3">
              <label class="form-label fw-medium" for="light_color">
                {{ __('Light Text Color') }}
                <i class="ti ti-info-circle ms-1" data-bs-toggle="tooltip" title="{{ __('Color for secondary text and descriptions') }}"></i>
              </label>
              <input type="text" 
                     name="light_color" 
                     id="light_color"
                     class="form-control color-picker-input" 
                     value="{{ get_static_option('light_color', '#64748b') }}"
                     placeholder="#64748b">
              <small class="form-text text-muted">{{ __('Color for secondary text and descriptions') }}</small>
            </div>
            
            <div class="col-md-4 mb-3">
              <label class="form-label fw-medium" for="extra_light_color">
                {{ __('Extra Light Color') }}
                <i class="ti ti-info-circle ms-1" data-bs-toggle="tooltip" title="{{ __('Color for subtle text and placeholders') }}"></i>
              </label>
              <input type="text" 
                     name="extra_light_color" 
                     id="extra_light_color"
                     class="form-control color-picker-input" 
                     value="{{ get_static_option('extra_light_color', '#94a3b8') }}"
                     placeholder="#94a3b8">
              <small class="form-text text-muted">{{ __('Color for subtle text elements and placeholders') }}</small>
            </div>
          </div>

          <!-- Color Preview Section -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="bg-label-secondary rounded p-4">
                <h6 class="mb-3">
                  <i class="ti ti-eye me-2"></i>{{ __('Color Preview') }}
                </h6>
                <div class="row g-3">
                  <div class="col-md-2 col-4">
                    <div class="text-center">
                      <div class="rounded-circle mx-auto mb-2" 
                           style="width: 50px; height: 50px; background-color: {{ get_static_option('site_main_color_one', '#6366f1') }};"
                           id="preview_color_one"></div>
                      <small class="text-muted">{{ __('Primary') }}</small>
                    </div>
                  </div>
                  <div class="col-md-2 col-4">
                    <div class="text-center">
                      <div class="rounded-circle mx-auto mb-2" 
                           style="width: 50px; height: 50px; background-color: {{ get_static_option('site_main_color_two', '#8b5cf6') }};"
                           id="preview_color_two"></div>
                      <small class="text-muted">{{ __('Secondary') }}</small>
                    </div>
                  </div>
                  <div class="col-md-2 col-4">
                    <div class="text-center">
                      <div class="rounded-circle mx-auto mb-2" 
                           style="width: 50px; height: 50px; background-color: {{ get_static_option('site_main_color_three', '#06b6d4') }};"
                           id="preview_color_three"></div>
                      <small class="text-muted">{{ __('Tertiary') }}</small>
                    </div>
                  </div>
                  <div class="col-md-2 col-4">
                    <div class="text-center">
                      <div class="rounded-circle mx-auto mb-2" 
                           style="width: 50px; height: 50px; background-color: {{ get_static_option('heading_color', '#1e293b') }};"
                           id="preview_heading"></div>
                      <small class="text-muted">{{ __('Heading') }}</small>
                    </div>
                  </div>
                  <div class="col-md-2 col-4">
                    <div class="text-center">
                      <div class="rounded-circle mx-auto mb-2" 
                           style="width: 50px; height: 50px; background-color: {{ get_static_option('light_color', '#64748b') }};"
                           id="preview_light"></div>
                      <small class="text-muted">{{ __('Light') }}</small>
                    </div>
                  </div>
                  <div class="col-md-2 col-4">
                    <div class="text-center">
                      <div class="rounded-circle mx-auto mb-2" 
                           style="width: 50px; height: 50px; background-color: {{ get_static_option('extra_light_color', '#94a3b8') }};"
                           id="preview_extra_light"></div>
                      <small class="text-muted">{{ __('Extra Light') }}</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="d-flex justify-content-between align-items-center pt-4 border-top">
            <div class="text-muted">
              <i class="ti ti-info-circle me-1"></i>
              <small>{{ __('Changes will be applied immediately after saving') }}</small>
            </div>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                <i class="ti ti-reload me-1"></i>{{ __('Reset') }}
              </button>
              <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="ti ti-device-floppy me-1"></i>{{ __('Update Colors') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Real-time color preview updates
document.addEventListener('DOMContentLoaded', function() {
    const colorInputs = {
        'site_main_color_one': 'preview_color_one',
        'site_main_color_two': 'preview_color_two', 
        'site_main_color_three': 'preview_color_three',
        'heading_color': 'preview_heading',
        'light_color': 'preview_light',
        'extra_light_color': 'preview_extra_light'
    };
    
    Object.entries(colorInputs).forEach(([inputId, previewId]) => {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        
        if (input && preview) {
            input.addEventListener('input', function() {
                preview.style.backgroundColor = this.value;
            });
        }
    });
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
});
</script>
@endsection