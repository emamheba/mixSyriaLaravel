@extends('layouts/layoutMaster')

@section('title', __('All Integrations'))

@section('vendor-style')
{{-- Add any vendor styles required by SweetAlert or Toastr if not globally included in layoutMaster --}}
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/toastr/toastr.scss'
])
@endsection

@section('vendor-script')
{{-- Add any vendor scripts required by SweetAlert or Toastr if not globally included in layoutMaster --}}
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/toastr/toastr.js'
])
@endsection

@section('page-style')
{{-- Custom CSS for the integration cards and layout --}}
<style>
/* Ensure the container provides padding consistent with layoutMaster */
.container-fluid.integration-container {
  padding-top: var(--bs-gutter-y, 1.5rem); /* Add some top padding */
  padding-bottom: var(--bs-gutter-y, 1.5rem); /* Add some bottom padding */
}

.integration-card {
  background: var(--bs-card-bg, #fff); /* Use Bootstrap variable for background */
  border-radius: 12px;
  border: 1px solid var(--bs-border-color, #e5e7eb); /* Use Bootstrap variable for border */
  padding: 24px;
  transition: all 0.3s ease;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.integration-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--bs-box-shadow-lg); /* Use Bootstrap variable for shadow */
  border-color: #8b5cf6; /* Example brand color */
}

.integration-icon {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  font-size: 24px;
  font-weight: 700;
  color: white;
  position: relative;
  overflow: hidden;
  flex-shrink: 0; /* Prevent icon from shrinking */
}

.integration-icon::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 100%);
  border-radius: 12px;
}

/* Gradient styles for icons */
.google_analytics { background: linear-gradient(135deg, #4285f4, #34a853); }
.google_tags { background: linear-gradient(135deg, #fbbc04, #ea4335); }
.facebook_pixels { background: linear-gradient(135deg, #1877f2, #42a5f5); }
.addroll { background: linear-gradient(135deg, #ff6b35, #f7941d); }
.whatsapp { background: linear-gradient(135deg, #25d366, #128c7e); }
.messenger { background: linear-gradient(135deg, #0084ff, #44bdf7); }
.twakto { background: linear-gradient(135deg, #40e0d0, #00ced1); }
.crisp { background: linear-gradient(135deg, #1972f5, #4dabf7); }
.tidio { background: linear-gradient(135deg, #34c759, #30d158); }
.captcha { background: linear-gradient(135deg, #4285f4, #1a73e8); }
.captcha2 { background: linear-gradient(135deg, #ea4335, #fbbc04); }
.instagram { background: linear-gradient(135deg, #e4405f, #fd1d1d, #fcb045); }
.google_adsense { background: linear-gradient(135deg, #4285f4, #0f9d58); }
.social_login { background: linear-gradient(135deg, #6366f1, #8b5cf6); }

.integration-title {
  font-size: 18px;
  font-weight: 600;
  color: var(--bs-heading-color, #1f2937); /* Use Bootstrap variable */
  margin-bottom: 8px;
}

.integration-description {
  color: var(--bs-secondary-color, #6b7280); /* Use Bootstrap variable */
  font-size: 14px;
  line-height: 1.5;
  margin-bottom: 24px;
  flex-grow: 1; /* Allows description to fill space */
}

.integration-actions {
  display: flex;
  gap: 8px;
  margin-top: auto; /* Pushes buttons to the bottom */
  flex-wrap: wrap; /* Allow buttons to wrap on smaller screens */
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  margin-bottom: 16px;
}

.status-active {
  background: var(--bs-success-bg-subtle, #dcfce7); /* Use Bootstrap variable */
  color: var(--bs-success-text-emphasis, #16a34a); /* Use Bootstrap variable */
}

.status-inactive {
  background: var(--bs-danger-bg-subtle, #fef2f2); /* Use Bootstrap variable */
  color: var(--bs-danger-text-emphasis, #dc2626); /* Use Bootstrap variable */
}

.btn-integration {
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  justify-content: center; /* Center button content */
}

.btn-toggle {
  background: var(--bs-gray-200, #f3f4f6); /* Use Bootstrap variable */
  color: var(--bs-gray-700, #374151); /* Use Bootstrap variable */
  flex: 1; /* Allow toggle button to grow */
  min-width: 100px; /* Minimum width to prevent being too small */
}

.btn-toggle:hover {
  background: var(--bs-gray-300, #e5e7eb); /* Use Bootstrap variable */
  color: var(--bs-gray-800, #1f2937); /* Use Bootstrap variable */
}

.btn-toggle.active {
  background: var(--bs-success, #10b981); /* Use Bootstrap variable */
  color: white;
}

.btn-toggle.active:hover {
  background: var(--bs-success-dark, #059669); /* Use Bootstrap variable */
}

.btn-toggle.inactive {
  background: var(--bs-danger, #ef4444); /* Use Bootstrap variable */
  color: white;
}

.btn-toggle.inactive:hover {
  background: var(--bs-danger-dark, #dc2626); /* Use Bootstrap variable */
}

.btn-settings {
  background: var(--bs-primary, #6366f1); /* Use Bootstrap variable */
  color: white;
  min-width: 100px;
}

.btn-settings:hover {
  background: var(--bs-primary-dark, #4f46e5); /* Use Bootstrap variable */
  color: white;
}

/* Specific styles for the page header */
.page-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Keep gradient */
  border-radius: 16px;
  padding: 32px;
  margin-bottom: 32px;
  color: white;
}

.page-header h1 {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 8px;
  color: white; /* Keep color white */
}

.page-header p {
  font-size: 16px;
  opacity: 0.9;
  margin: 0;
  color: rgba(255, 255, 255, 0.9); /* Ensure paragraph color is visible */
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .integration-actions {
    flex-direction: column; /* Stack buttons vertically */
    gap: 12px; /* Add more space when stacked */
  }

  .btn-integration {
     width: 100%; /* Full width when stacked */
     min-width: auto; /* Remove min-width constraint */
  }
  .col-lg-4.col-md-6 {
      width: 100%; /* Ensure full width on smaller screens */
      flex: none; /* Override flex-basis calculation */
  }
}

/* Adjust Bootstrap modal styling to match custom styles */
.modal-content {
  border-radius: 16px;
  border: none;
  box-shadow: var(--bs-box-shadow-xl); /* Use Bootstrap variable */
}

.modal-header {
  border-bottom: 1px solid var(--bs-border-color); /* Use Bootstrap variable */
  padding: 24px;
}

.modal-title {
  font-size: 20px;
  font-weight: 600;
  color: var(--bs-heading-color); /* Use Bootstrap variable */
}

.modal-body {
  padding: 24px;
}

.modal-footer {
  border-top: 1px solid var(--bs-border-color); /* Use Bootstrap variable */
  padding: 24px;
}

/* Form group and control styling */
.form-group { /* Keep this for compatibility if used directly */
  margin-bottom: var(--bs-gutter-y, 1.5rem); /* Use Bootstrap variable */
}

/* Replicate the switch_box styling if layoutMaster doesn't provide a similar one */
/* Note: layoutMaster likely has its own Bootstrap switch styling. 
   If you want the specific style from your second example, keep this block.
   Otherwise, remove it and use Bootstrap's default form-check-switch. */
.switch_box {
  position: relative;
  display: inline-block;
  width: 50px; /* Adjusted width */
  height: 28px; /* Adjusted height */
  margin-bottom: 1rem; /* Add some space below the switch */
}

.switch_box input {
  opacity: 0;
  width: 0;
  height: 0;
}

.switch_box label {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: var(--bs-gray-400, #ccc); /* Use Bootstrap variable */
  transition: .4s;
  border-radius: 28px; /* Adjusted border-radius */
}

.switch_box label:before {
  position: absolute;
  content: "";
  height: 20px; /* Adjusted size */
  width: 20px; /* Adjusted size */
  left: 4px; /* Adjusted position */
  bottom: 4px; /* Adjusted position */
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

.switch_box input:checked + label {
  background-color: var(--bs-primary); /* Use Bootstrap variable */
}

.switch_box input:checked + label:before {
  transform: translateX(22px); /* Adjusted translation */
}


/* Ensure consistency with layoutMaster form styles */
.form-label { /* Use Bootstrap default form-label */
  display: block;
  margin-bottom: 0.5rem; /* Use Bootstrap variable */
  font-size: 14px;
  font-weight: 500;
  color: var(--bs-body-color); /* Use Bootstrap variable */
}

.form-control { /* Use Bootstrap default form-control */
  display: block;
  width: 100%;
  padding: 0.375rem 0.75rem; /* Use Bootstrap variable */
  font-size: 1rem; /* Use Bootstrap variable */
  font-weight: 400;
  line-height: 1.5;
  color: var(--bs-body-color); /* Use Bootstrap variable */
  background-color: var(--bs-form-control-bg); /* Use Bootstrap variable */
  background-clip: padding-box;
  border: var(--bs-border-width) solid var(--bs-border-color); /* Use Bootstrap variables */
  appearance: none;
  border-radius: var(--bs-border-radius); /* Use Bootstrap variable */
  transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out; /* Use Bootstrap variables */
}

.form-control:focus {
  color: var(--bs-body-color); /* Use Bootstrap variable */
  background-color: var(--bs-form-control-bg); /* Use Bootstrap variable */
  border-color: var(--bs-focus-border-color); /* Use Bootstrap variable */
  outline: 0;
  box-shadow: var(--bs-box-shadow-inset), var(--bs-focus-ring-box-shadow); /* Use Bootstrap variables */
}

.info-text {
    font-size: 0.875em;
    color: var(--bs-secondary-color); /* Use Bootstrap variable */
    margin-top: 0.5rem; /* Use Bootstrap variable */
}
</style>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle integration activation/deactivation
        document.querySelectorAll('.btn-toggle').forEach(button => { // Target the new button class
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const optionName = this.dataset.option;
                const currentStatus = this.dataset.status;
                const newStatus = currentStatus === 'on' ? 'off' : 'on'; // Toggle status
                const button = this; // Reference the button element

                Swal.fire({
                    title: '{{__("Are you sure?")}}',
                    text: '{{__("You will be able to revert your decision anytime.")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{__('Yes, change it!')}}",
                    cancelButtonText: "{{__('Cancel')}}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("{{ route('admin.integration.activation') }}", {
                            method: 'POST', // Or PUT, depending on your route definition
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                option_name: optionName,
                                status: newStatus,
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.type === 'success') {
                                // Update button text and class based on the *new* status
                                if (data.status === 'on') {
                                    button.textContent = '{{ __("Deactivate") }}';
                                    button.classList.remove('inactive');
                                    button.classList.add('active');
                                    toastr.success("{{ __('Successfully Activated') }}");
                                } else {
                                    button.textContent = '{{ __("Activate") }}';
                                    button.classList.remove('active');
                                    button.classList.add('inactive');
                                    toastr.warning("{{ __('Successfully Deactivated') }}");
                                }
                                button.dataset.status = data.status; // Update the data-status attribute

                                // Update the status badge text and class
                                const statusBadge = button.closest('.integration-card').querySelector('.status-badge');
                                if (statusBadge) {
                                    statusBadge.textContent = data.status === 'on' ? '{{ __("Active") }}' : '{{ __("Inactive") }}';
                                    statusBadge.classList.remove('status-active', 'status-inactive');
                                    statusBadge.classList.add(data.status === 'on' ? 'status-active' : 'status-inactive');
                                     // Update the icon within the badge
                                    const statusIcon = statusBadge.querySelector('i');
                                    if(statusIcon) {
                                         statusIcon.className = 'ti ' + (data.status === 'on' ? 'ti-check' : 'ti-x');
                                    }
                                }


                            } else {
                                toastr.error(data.msg || '{{ __("Failed to update integration status") }}');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('{{ __("An error occurred while updating status.") }}');
                        });
                    }
                });
            });
        });
    });
</script>
@endsection

@section('content')
@php
  $method = "get_static_option"; // Keep your helper function usage
@endphp

{{-- Use a container for consistent padding with layoutMaster --}}
<div class="container-fluid integration-container">

  <!-- Page Header -->
  {{-- Using Bootstrap classes for margin/padding for consistency --}}
  <div class="page-header mb-4">
    <h1>{{ __('All Integrations') }}</h1>
    <p>{{ __('Manage all integrations from here, you can activate/deactivate integrations and configure their settings.') }}</p>
  </div>

  <!-- Integrations Grid -->
  {{-- Using Bootstrap grid system --}}
  <div class="row g-4">
    <!-- Google Analytics GT4 -->
    <div class="col-lg-4 col-md-6 col-12">
      <div class="integration-card">
        <div class="integration-icon google_analytics">GA</div>
        <div class="status-badge {{ $method('google_analytics_gt4_status') == 'on' ? 'status-active' : 'status-inactive' }}">
           <i class="ti {{ $method('google_analytics_gt4_status') == 'on' ? 'ti-check' : 'ti-x' }}"></i>
          {{ $method('google_analytics_gt4_status') == 'on' ? __('Active') : __('Inactive') }}
        </div>
        <h3 class="integration-title">{{ __('Google Analytics GT4') }}</h3>
        <p class="integration-description">{{ __('Configure Google Analytics (GT4) to track website performance and user behavior with advanced insights.') }}</p>
        <div class="integration-actions">
          <button class="btn-integration btn-toggle {{ $method('google_analytics_gt4_status') == 'on' ? 'active' : 'inactive' }}"
                  data-option="google_analytics_gt4_status"
                  data-status="{{ $method('google_analytics_gt4_status') }}">
            <i class="ti {{ $method('google_analytics_gt4_status') == 'on' ? 'ti-power' : 'ti-power-off' }}"></i>
            {{ $method('google_analytics_gt4_status') == 'on' ? __('Deactivate') : __('Activate') }}
          </button>
          <button class="btn-integration btn-settings" data-bs-toggle="modal" data-bs-target="#google_analytics_modal">
            <i class="ti ti-settings"></i>
            {{ __('Settings') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Google Tags Manager -->
    <div class="col-lg-4 col-md-6 col-12">
      <div class="integration-card">
        <div class="integration-icon google_tags">GTM</div>
         <div class="status-badge {{ $method('google_tag_manager_status') == 'on' ? 'status-active' : 'status-inactive' }}">
           <i class="ti {{ $method('google_tag_manager_status') == 'on' ? 'ti-check' : 'ti-x' }}"></i>
          {{ $method('google_tag_manager_status') == 'on' ? __('Active') : __('Inactive') }}
        </div>
        <h3 class="integration-title">{{ __('Google Tags Manager') }}</h3>
        <p class="integration-description">{{ __('Manage and deploy marketing tags without modifying code. Streamline tag management for better performance.') }}</p>
        <div class="integration-actions">
          <button class="btn-integration btn-toggle {{ $method('google_tag_manager_status') == 'on' ? 'active' : 'inactive' }}"
                  data-option="google_tag_manager_status"
                  data-status="{{ $method('google_tag_manager_status') }}">
            <i class="ti {{ $method('google_tag_manager_status') == 'on' ? 'ti-power' : 'ti-power-off' }}"></i>
            {{ $method('google_tag_manager_status') == 'on' ? __('Deactivate') : __('Activate') }}
          </button>
          <button class="btn-integration btn-settings" data-bs-toggle="modal" data-bs-target="#google_tag_manager_modal">
            <i class="ti ti-settings"></i>
            {{ __('Settings') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Facebook Pixels -->
    <div class="col-lg-4 col-md-6 col-12">
      <div class="integration-card">
        <div class="integration-icon facebook_pixels">FB</div>
        <div class="status-badge {{ $method('facebook_pixels_status') == 'on' ? 'status-active' : 'status-inactive' }}">
           <i class="ti {{ $method('facebook_pixels_status') == 'on' ? 'ti-check' : 'ti-x' }}"></i>
          {{ $method('facebook_pixels_status') == 'on' ? __('Active') : __('Inactive') }}
        </div>
        <h3 class="integration-title">{{ __('Facebook Pixels') }}</h3>
        <p class="integration-description">{{ __('Track conversions, optimize ads, and build targeted audiences for your Facebook advertising campaigns.') }}</p>
        <div class="integration-actions">
          <button class="btn-integration btn-toggle {{ $method('facebook_pixels_status') == 'on' ? 'active' : 'inactive' }}"
                  data-option="facebook_pixels_status"
                  data-status="{{ $method('facebook_pixels_status') }}">
            <i class="ti {{ $method('facebook_pixels_status') == 'on' ? 'ti-power' : 'ti-power-off' }}"></i>
            {{ $method('facebook_pixels_status') == 'on' ? __('Deactivate') : __('Activate') }}
          </button>
          <button class="btn-integration btn-settings" data-bs-toggle="modal" data-bs-target="#facebook_pixels_modal">
            <i class="ti ti-settings"></i>
            {{ __('Settings') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Adroll -->
    <div class="col-lg-4 col-md-6 col-12">
      <div class="integration-card">
        <div class="integration-icon addroll">AR</div>
        <div class="status-badge {{ $method('adroll_pixels_status') == 'on' ? 'status-active' : 'status-inactive' }}">
           <i class="ti {{ $method('adroll_pixels_status') == 'on' ? 'ti-check' : 'ti-x' }}"></i>
          {{ $method('adroll_pixels_status') == 'on' ? __('Active') : __('Inactive') }}
        </div>
        <h3 class="integration-title">{{ __('AdRoll') }}</h3>
        <p class="integration-description">{{ __('Retarget website visitors across web, social, and email to increase conversions and brand awareness.') }}</p>
        <div class="integration-actions">
          <button class="btn-integration btn-toggle {{ $method('adroll_pixels_status') == 'on' ? 'active' : 'inactive' }}"
                  data-option="adroll_pixels_status"
                  data-status="{{ $method('adroll_pixels_status') }}">
            <i class="ti {{ $method('adroll_pixels_status') == 'on' ? 'ti-power' : 'ti-power-off' }}"></i>
            {{ $method('adroll_pixels_status') == 'on' ? __('Deactivate') : __('Activate') }}
          </button>
          <button class="btn-integration btn-settings" data-bs-toggle="modal" data-bs-target="#adroll_pixels_modal">
            <i class="ti ti-settings"></i>
            {{ __('Settings') }}
          </button>
        </div>
      </div>
    </div>

    <!-- WhatsApp -->
    <div class="col-lg-4 col-md-6 col-12">
      <div class="integration-card">
        <div class="integration-icon whatsapp">WA</div>
        <div class="status-badge {{ $method('whatsapp_status') == 'on' ? 'status-active' : 'status-inactive' }}">
           <i class="ti {{ $method('whatsapp_status') == 'on' ? 'ti-check' : 'ti-x' }}"></i>
          {{ $method('whatsapp_status') == 'on' ? __('Active') : __('Inactive') }}
        </div>
        <h3 class="integration-title">{{ __('WhatsApp') }}</h3>
        <p class="integration-description">{{ __('Enable direct WhatsApp communication with your customers for instant support and engagement.') }}</p>
        <div class="integration-actions">
          <button class="btn-integration btn-toggle {{ $method('whatsapp_status') == 'on' ? 'active' : 'inactive' }}"
                  data-option="whatsapp_status"
                  data-status="{{ $method('whatsapp_status') }}">
            <i class="ti {{ $method('whatsapp_status') == 'on' ? 'ti-power' : 'ti-power-off' }}"></i>
            {{ $method('whatsapp_status') == 'on' ? __('Deactivate') : __('Activate') }}
          </button>
          <button class="btn-integration btn-settings" data-bs-toggle="modal" data-bs-target="#whatsapp_modal">
            <i class="ti ti-settings"></i>
            {{ __('Settings') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Messenger -->
    <div class="col-lg-4 col-md-6 col-12">
      <div class="integration-card">
        <div class="integration-icon messenger">MSG</div>
        <div class="status-badge {{ $method('messenger_status') == 'on' ? 'status-active' : 'status-inactive' }}">
           <i class="ti {{ $method('messenger_status') == 'on' ? 'ti-check' : 'ti-x' }}"></i>
          {{ $method('messenger_status') == 'on' ? __('Active') : __('Inactive') }}
        </div>
        <h3 class="integration-title">{{ __('Messenger') }}</h3>
        <p class="integration-description">{{ __('Integrate Facebook Messenger to provide real-time customer support and enhance user engagement.') }}</p>
        <div class="integration-actions">
          <button class="btn-integration btn-toggle {{ $method('messenger_status') == 'on' ? 'active' : 'inactive' }}"
                  data-option="messenger_status"
                  data-status="{{ $method('messenger_status') }}">
            <i class="ti {{ $method('messenger_status') == 'on' ? 'ti-power' : 'ti-power-off' }}"></i>
            {{ $method('messenger_status') == 'on' ? __('Deactivate') : __('Activate') }}
          </button>
          <button class="btn-integration btn-settings" data-bs-toggle="modal" data-bs-target="#messenger_modal">
            <i class="ti ti-settings"></i>
            {{ __('Settings') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Twak.to -->
    <div class="col-lg-4 col-md-6 col-12">
      <div class="integration-card">
        <div class="integration-icon twakto">TK</div>
        <div class="status-badge {{ $method('twakto_status') == 'on' ? 'status-active' : 'status-inactive' }}">
           <i class="ti {{ $method('twakto_status') == 'on' ? 'ti-check' : 'ti-x' }}"></i>
          {{ $method('twakto_status') == 'on' ? __('Active') : __('Inactive') }}
        </div>
        <h3 class="integration-title">{{ __('Twak.to API') }}</h3>
        <p class="integration-description">{{ __('Add live chat functionality to monitor and chat with visitors on your website in real-time.') }}</p>
        <div class="integration-actions">
          <button class="btn-integration btn-toggle {{ $method('twakto_status') == 'on' ? 'active' : 'inactive' }}"
                  data-option="twakto_status"
                  data-status="{{ $method('twakto_status') }}">
            <i class="ti {{ $method('twakto_status') == 'on' ? 'ti-power' : 'ti-power-off' }}"></i>
            {{ $method('twakto_status') == 'on' ? __('Deactivate') : __('Activate') }}
          </button>
          <button class="btn-integration btn-settings" data-bs-toggle="modal" data-bs-target="#twakto_modal">
            <i class="ti ti-settings"></i>
            {{ __('Settings') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Crisp -->
    <div class="col-lg-4 col-md-6 col-12">
      <div class="integration-card">
        <div class="integration-icon crisp">CR</div>
        <div class="status-badge {{ $method('crsip_status') == 'on' ? 'status-active' : 'status-inactive' }}">
           <i class="ti {{ $method('crsip_status') == 'on' ? 'ti-check' : 'ti-x' }}"></i>
          {{ $method('crsip_status') == 'on' ? __('Active') : __('Inactive') }}
        </div>
        <h3 class="integration-title">{{ __('Crisp') }}</h3>
        <p class="integration-description">{{ __('Beautiful and powerful live chat platform to improve customer experience and support.') }}</p>
        <div class="integration-actions">
          <button class="btn-integration btn-toggle {{ $method('crsip_status') == 'on' ? 'active' : 'inactive' }}"
                  data-option="crsip_status"
                  data-status="{{ $method('crsip_status') }}">
            <i class="ti {{ $method('crsip_status') == 'on' ? 'ti-power' : 'ti-power-off' }}"></i>
            {{ $method('crsip_status') == 'on' ? __('Deactivate') : __('Activate') }}
          </button>
          <button class="btn-integration btn-settings" data-bs-toggle="modal" data-bs-target="#crsip_modal">
            <i class="ti ti-settings"></i>
            {{ __('Settings') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Tidio -->
    <div class="col-lg-4 col-md-6 col-12">
      <div class="integration-card">
        <div class="integration-icon tidio">TD</div>
        <div class="status-badge {{ $method('tidio_status') == 'on' ? 'status-active' : 'status-inactive' }}">
           <i class="ti {{ $method('tidio_status') == 'on' ? 'ti-check' : 'ti-x' }}"></i>
          {{ $method('tidio_status') == 'on' ? __('Active') : __('Inactive') }}
        </div>
        <h3 class="integration-title">{{ __('Tidio') }}</h3>
        <p class="integration-description">{{ __('Combine live chat, chatbots, and email marketing in one powerful customer service platform.') }}</p>
        <div class="integration-actions">
          <button class="btn-integration btn-toggle {{ $method('tidio_status') == 'on' ? 'active' : 'inactive' }}"
                  data-option="tidio_status"
                  data-status="{{ $method('tidio_status') }}">
            <i class="ti {{ $method('tidio_status') == 'on' ? 'ti-power' : 'ti-power-off' }}"></i>
            {{ $method('tidio_status') == 'on' ? __('Deactivate') : __('Activate') }}
          </button>
          <button class="btn-integration btn-settings" data-bs-toggle="modal" data-bs-target="#tidio_modal">
            <i class="ti ti-settings"></i>
            {{ __('Settings') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Google Captcha V3 -->
    <div class="col-lg-4 col-md-6 col-12">
      <div class="integration-card">
        <div class="integration-icon captcha">GC3</div>
        <div class="status-badge {{ $method('captcha_status') == 'on' ? 'status-active' : 'status-inactive' }}">
           <i class="ti {{ $method('captcha_status') == 'on' ? 'ti-check' : 'ti-x' }}"></i>
          {{ $method('captcha_status') == 'on' ? __('Active') : __('Inactive') }}
        </div>
        <h3 class="integration-title">{{ __('Google Captcha V3') }}</h3>
        <p class="integration-description">{{ __('Protect your website from spam and abuse while letting real users pass through easily.') }}</p>
        <div class="integration-actions">
          <button class="btn-integration btn-toggle {{ $method('captcha_status') == 'on' ? 'active' : 'inactive' }}"
                  data-option="captcha_status"
                  data-status="{{ $method('captcha_status') }}">
            <i class="ti {{ $method('captcha_status') == 'on' ? 'ti-power' : 'ti-power-off' }}"></i>
            {{ $method('captcha_status') == 'on' ? __('Deactivate') : __('Activate') }}
          </button>
          <button class="btn-integration btn-settings" data-bs-toggle="modal" data-bs-target="#google_captcha_modal">
            <i class="ti ti-settings"></i>
            {{ __('Settings') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Google Captcha V2 -->
    <div class="col-lg-4 col-md-6 col-12">
      <div class="integration-card">
        <div class="integration-icon captcha2">GC2</div>
        <div class="status-badge {{ $method('site_google_captcha_enable') == 'on' ? 'status-active' : 'status-inactive' }}">
           <i class="ti {{ $method('site_google_captcha_enable') == 'on' ? 'ti-check' : 'ti-x' }}"></i>
          {{ $method('site_google_captcha_enable') == 'on' ? __('Active') : __('Inactive') }}
        </div>
        <h3 class="integration-title">{{ __('Google Captcha V2') }}</h3>
        <p class="integration-description">{{ __('Classic reCAPTCHA challenge to verify users and prevent automated spam attacks.') }}</p>
        <div class="integration-actions">
          <button class="btn-integration btn-toggle {{ $method('site_google_captcha_enable') == 'on' ? 'active' : 'inactive' }}"
                  data-option="site_google_captcha_enable"
                  data-status="{{ $method('site_google_captcha_enable') }}">
            <i class="ti {{ $method('site_google_captcha_enable') == 'on' ? 'ti-power' : 'ti-power-off' }}"></i>
            {{ $method('site_google_captcha_enable') == 'on' ? __('Deactivate') : __('Activate') }}
          </button>
          <button class="btn-integration btn-settings" data-bs-toggle="modal" data-bs-target="#google_captcha2_modal">
            <i class="ti ti-settings"></i>
            {{ __('Settings') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Instagram -->
    <div class="col-lg-4 col-md-6 col-12">
      <div class="integration-card">
        <div class="integration-icon instagram">IG</div>
        <div class="status-badge {{ $method('instagram_status') == 'on' ? 'status-active' : 'status-inactive' }}">
           <i class="ti {{ $method('instagram_status') == 'on' ? 'ti-check' : 'ti-x' }}"></i>
          {{ $method('instagram_status') == 'on' ? __('Active') : __('Inactive') }}
        </div>
        <h3 class="integration-title">{{ __('Instagram') }}</h3>
        <p class="integration-description">{{ __('Display Instagram feeds and connect your social media presence with your website seamlessly.') }}</p>
        <div class="integration-actions">
          <button class="btn-integration btn-toggle {{ $method('instagram_status') == 'on' ? 'active' : 'inactive' }}"
                  data-option="instagram_status"
                  data-status="{{ $method('instagram_status') }}">
            <i class="ti {{ $method('instagram_status') == 'on' ? 'ti-power' : 'ti-power-off' }}"></i>
            {{ $method('instagram_status') == 'on' ? __('Deactivate') : __('Activate') }}
          </button>
          <button class="btn-integration btn-settings" data-bs-toggle="modal" data-bs-target="#instagram_modal">
            <i class="ti ti-settings"></i>
            {{ __('Settings') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Google Adsense -->
    <div class="col-lg-4 col-md-6 col-12">
      <div class="integration-card">
        <div class="integration-icon google_adsense">AD</div>
        <div class="status-badge {{ $method('google_adsense_status') == 'on' ? 'status-active' : 'status-inactive' }}">
           <i class="ti {{ $method('google_adsense_status') == 'on' ? 'ti-check' : 'ti-x' }}"></i>
          {{ $method('google_adsense_status') == 'on' ? __('Active') : __('Inactive') }}
        </div>
        <h3 class="integration-title">{{ __('Google Adsense') }}</h3>
        <p class="integration-description">{{ __('Monetize your website content by displaying relevant ads from Google.') }}</p>
        <div class="integration-actions">
          <button class="btn-integration btn-toggle {{ $method('google_adsense_status') == 'on' ? 'active' : 'inactive' }}"
                  data-option="google_adsense_status"
                  data-status="{{ $method('google_adsense_status') }}">
            <i class="ti {{ $method('google_adsense_status') == 'on' ? 'ti-power' : 'ti-power-off' }}"></i>
            {{ $method('google_adsense_status') == 'on' ? __('Deactivate') : __('Activate') }}
          </button>
          <button class="btn-integration btn-settings" data-bs-toggle="modal" data-bs-target="#google_adsense">
            <i class="ti ti-settings"></i>
            {{ __('Settings') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Social Login -->
    <div class="col-lg-4 col-md-6 col-12">
      <div class="integration-card">
        <div class="integration-icon social_login">SL</div>
        <div class="status-badge {{ $method('social_login_status') == 'on' ? 'status-active' : 'status-inactive' }}">
           <i class="ti {{ $method('social_login_status') == 'on' ? 'ti-check' : 'ti-x' }}"></i>
          {{ $method('social_login_status') == 'on' ? __('Active') : __('Inactive') }}
        </div>
        <h3 class="integration-title">{{ __('Social Login') }}</h3>
        <p class="integration-description">{{ __('Allow users to sign in using their social media accounts (Facebook, Google) for easier access.') }}</p>
        <div class="integration-actions">
          <button class="btn-integration btn-toggle {{ $method('social_login_status') == 'on' ? 'active' : 'inactive' }}"
                  data-option="social_login_status"
                  data-status="{{ $method('social_login_status') }}">
            <i class="ti {{ $method('social_login_status') == 'on' ? 'ti-power' : 'ti-power-off' }}"></i>
            {{ $method('social_login_status') == 'on' ? __('Deactivate') : __('Activate') }}
          </button>
          <button class="btn-integration btn-settings" data-bs-toggle="modal" data-bs-target="#social_login">
            <i class="ti ti-settings"></i>
            {{ __('Settings') }}
          </button>
        </div>
      </div>
    </div>

  </div> {{-- / .row --}}

</div> {{-- / .container-fluid --}}


{{-- Modals --}}

{{-- Messenger Modal --}}
<div class="modal fade" tabindex="-1" id="messenger_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__("Messenger")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.integration') }}" method="post">
                @csrf
                <input type="hidden" name="data_type" value="messenger">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="messenger_page_id">{{__("Messenger Page ID")}}</label>
                        <input type="text" id="messenger_page_id" name="messenger_page_id" class="form-control" value="{{get_static_option("messenger_page_id")}}" placeholder="{{__('Enter Page ID')}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Tidio Modal --}}
<div class="modal fade" tabindex="-1" id="tidio_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__("Tidio")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.integration') }}" method="post">
                @csrf
                <input type="hidden" name="data_type" value="tidio">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tidio_chat_page_id">{{__("Chat Page ID")}}</label>
                        <input type="text" id="tidio_chat_page_id" name="tidio_chat_page_id" class="form-control" value="{{get_static_option("tidio_chat_page_id")}}" placeholder="{{__('Enter Chat Page ID')}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Crisp Modal --}}
<div class="modal fade" tabindex="-1" id="crsip_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__("Crsip")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.integration') }}" method="post">
                @csrf
                <input type="hidden" name="data_type" value="crsip">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="crsip_website_id">{{__("Website ID")}}</label>
                        <input type="text" id="crsip_website_id" name="crsip_website_id" class="form-control" value="{{get_static_option("crsip_website_id")}}" placeholder="{{__('Enter Website ID')}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Twak.to Modal --}}
<div class="modal fade" tabindex="-1" id="twakto_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__("Twak.to")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.integration') }}" method="post">
                @csrf
                <input type="hidden" name="data_type" value="twakto">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="twakto_widget_id">{{__("Widget ID")}}</label>
                        <input type="text" id="twakto_widget_id" name="twakto_widget_id" class="form-control" value="{{get_static_option("twakto_widget_id")}}" placeholder="{{__('Enter Widget ID')}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Whatsapp Modal --}}
<div class="modal fade" tabindex="-1" id="whatsapp_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__("What's App")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.integration') }}" method="post">
                @csrf
                <input type="hidden" name="data_type" value="whatsapp">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="whatsapp_mobile_number">{{__("What's App Mobile Number With Country Code")}}</label>
                        <input type="text" id="whatsapp_mobile_number" name="whatsapp_mobile_number" class="form-control" value="{{get_static_option("whatsapp_mobile_number")}}" placeholder="{{__('e.g., +11234567890')}}">
                    </div>
                     <div class="form-group">
                        <label for="whatsapp_initial_text">{{__("Initial Message")}}</label>
                        <input type="text" id="whatsapp_initial_text" name="whatsapp_initial_text" class="form-control" value="{{get_static_option("whatsapp_initial_text")}}" placeholder="{{__('e.g., Hello!')}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Adroll Pixels Modal --}}
<div class="modal fade" tabindex="-1" id="adroll_pixels_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__("AdRoll Pixels")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.integration') }}" method="post">
                @csrf
                <input type="hidden" name="data_type" value="adroll_pixels">
                <div class="modal-body">
                    <div class="form-group">
                         <label for="adroll_adviser_id">{{__("Adroll Adviser ID")}}</label>
                         <input type="text" id="adroll_adviser_id" name="adroll_adviser_id" class="form-control" value="{{$method("adroll_adviser_id")}}" placeholder="{{__('Enter Adviser ID')}}">
                    </div>
                     <div class="form-group">
                        <label for="adroll_publisher_id">{{__("Adroll Publisher ID")}}</label>
                        <input type="text" id="adroll_publisher_id" name="adroll_publisher_id" class="form-control" value="{{$method("adroll_publisher_id")}}" placeholder="{{__('Enter Publisher ID')}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Facebook Pixels Modal --}}
<div class="modal fade" tabindex="-1" id="facebook_pixels_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__("Facebook Pixels")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.integration') }}" method="post">
                @csrf
                <input type="hidden" name="data_type" value="facebook_pixels">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="facebook_pixels_id">{{__("Facebook Pixels ID")}}</label>
                        <input type="text" id="facebook_pixels_id" name="facebook_pixels_id" class="form-control" value="{{$method("facebook_pixels_id")}}" placeholder="{{__('Enter Pixels ID')}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Google Analytics GT4 Modal --}}
<div class="modal fade" tabindex="-1" id="google_analytics_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__("Google Analytics GT4")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.integration') }}" method="post">
                @csrf
                <input type="hidden" name="data_type" value="google_analytics">
                <div class="modal-body">
                    <div class="form-group">
                         <label for="google_analytics_gt4_ID">{{__("Google Analytics GT4 ID")}}</label>
                         <input type="text" id="google_analytics_gt4_ID" name="google_analytics_gt4_ID" class="form-control" value="{{$method("google_analytics_gt4_ID")}}" placeholder="{{__('Enter GT4 ID')}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Google Tag Manager Modal --}}
<div class="modal fade" tabindex="-1" id="google_tag_manager_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__("Google Tag Manager")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.integration') }}" method="post">
                @csrf
                <input type="hidden" name="data_type" value="google_tag_manager">
                <div class="modal-body">
                    <div class="form-group">
                         <label for="google_tag_manager_ID">{{__("Google Tag Manager ID")}}</label>
                         <input type="text" id="google_tag_manager_ID" name="google_tag_manager_ID" class="form-control" value="{{$method("google_tag_manager_ID")}}" placeholder="{{__('Enter Tag Manager ID')}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Google Captcha V3 Modal --}}
<div class="modal fade" tabindex="-1" id="google_captcha_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__("Google Captcha V3")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.integration') }}" method="post">
                @csrf
                <input type="hidden" name="data_type" value="google_captcha_v3">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="site_google_captcha_v3_site_key">{{__("Google Captcha V3 Site Key")}}</label>
                        <input type="text" id="site_google_captcha_v3_site_key" name="site_google_captcha_v3_site_key" class="form-control" value="{{$method("site_google_captcha_v3_site_key")}}" placeholder="{{__('Enter Site Key')}}">
                    </div>

                    <div class="form-group">
                        <label for="site_google_captcha_v3_secret_key">{{__("Google Captcha V3 Secret Key")}}</label>
                        <input type="text" id="site_google_captcha_v3_secret_key" name="site_google_captcha_v3_secret_key" class="form-control" value="{{$method("site_google_captcha_v3_secret_key")}}" placeholder="{{__('Enter Secret Key')}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Google Captcha V2 Modal --}}
<div class="modal fade" tabindex="-1" id="google_captcha2_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__("Google Captcha V2")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.integration') }}" method="post">
                @csrf
                <input type="hidden" name="data_type" value="google_captcha_v2">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recaptcha_2_site_key">{{__("Google Captcha V2 Site Key")}}</label>
                        <input type="text" id="recaptcha_2_site_key" name="recaptcha_2_site_key" class="form-control" value="{{$method("recaptcha_2_site_key")}}" placeholder="{{__('Enter Site Key')}}">
                    </div>

                    <div class="form-group">
                        <label for="recaptcha_2_secret_key">{{__("Google Captcha V2 Secret Key")}}</label>
                        <input type="text" id="recaptcha_2_secret_key" name="recaptcha_2_secret_key" class="form-control" value="{{$method("recaptcha_2_secret_key")}}" placeholder="{{__('Enter Secret Key')}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Instagram Modal --}}
<div class="modal fade" tabindex="-1" id="instagram_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__("Instagram")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.integration') }}" method="post">
                @csrf
                <input type="hidden" name="data_type" value="instagram">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="instagram_access_token">{{__("Instagram Access Token")}}</label>
                        <input type="text" id="instagram_access_token" name="instagram_access_token" class="form-control" value="{{get_static_option("instagram_access_token")}}" placeholder="{{__('Enter Access Token')}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Google Adsense Modal --}}
<div class="modal fade" tabindex="-1" id="google_adsense" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__("Google Adsense")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.integration') }}" method="post">
                @csrf
                <input type="hidden" name="data_type" value="google_adsense">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="google_adsense_publisher_id">{{__("Google Adsense Publisher ID")}}</label>
                        <input type="text" name="google_adsense_publisher_id" id="google_adsense_publisher_id"  class="form-control" value="{{get_static_option("google_adsense_publisher_id")}}" placeholder="{{__('Enter Publisher ID')}}">
                    </div>
                    <div class="form-group">
                        <label for="google_adsense_customer_id">{{__("Google Adsense Customer ID")}}</label>
                        <input type="text" name="google_adsense_customer_id" id="google_adsense_customer_id"  class="form-control" value="{{get_static_option("google_adsense_customer_id")}}" placeholder="{{__('Enter Customer ID')}}">
                    </div>
                    <p class="info-text">{{ __('Follow doc for Google Adsense Publisher ID and Customer ID') }}
                        <a href="#" target="_blank"><i class="ti ti-external-link"></i></a>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Social login Modal -->
<div class="modal fade" tabindex="-1" id="social_login" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> {{-- Use modal-lg for wider modal --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__("Social Login")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.integration') }}" method="post">
                @csrf
                <input type="hidden" name="data_type" value="social_login">
                <div class="modal-body">
                    {{-- Facebook Settings --}}
                    <h6 class="mb-3">{{__("Facebook Login Settings")}}</h6>
                    {{-- Using your custom switch_box structure as per the provided example HTML --}}
                    <div class="form__input__single d-grid mt-3">
                        <label for="enable_facebook_login"><strong>{{__('Enable/Disable Facebook Login')}}</strong></label>
                        <div class="switch_box style_7"> {{-- Keeping the style_7 class if your CSS uses it --}}
                            <input type="checkbox" id="enable_facebook_login" name="enable_facebook_login"  @if(!empty(get_static_option('enable_facebook_login'))) checked @endif>
                            <label></label>
                        </div>
                        <small class="form-text text-muted">  {{__('Enable, means Frontend register page show social login')}} </small>
                    </div>

                    <div class="form-group">
                        <label for="facebook_client_id">{{__('Facebook Client ID')}}</label>
                        <input type="text" id="facebook_client_id" name="facebook_client_id"  class="form-control" value="{{get_static_option('facebook_client_id')}}" placeholder="{{__('Enter Client ID')}}">
                    </div>
                    <div class="form-group">
                        <label for="facebook_client_secret">{{__('Facebook Client Secret')}}</label>
                        <input type="text" id="facebook_client_secret" name="facebook_client_secret"  class="form-control" value="{{get_static_option('facebook_client_secret')}}" placeholder="{{__('Enter Client Secret')}}">
                    </div>
                    <div class="form-group">
                        <label for="facebook_callback_url">{{__('Facebook Callback URL')}}</label>
                        <input type="text" id="facebook_callback_url" name="facebook_callback_url"  class="form-control" value="{{get_static_option('facebook_callback_url')}}" placeholder="{{__('Enter Callback URL')}}">
                        <p class="info-text">{{__('facebook callback url for your app')}} <code>{{url('/')}}/facebook/callback</code>
                            <a href="https://bytesed.com/docs/facebook-login/" target="_blank">
                                <i class="ti ti-external-link"></i>
                            </a>
                        </p>
                    </div>

                    {{-- Google Settings --}}
                    <h6 class="mb-3 mt-4">{{__("Google Login Settings")}}</h6> {{-- Added margin top --}}
                     {{-- Using your custom switch_box structure as per the provided example HTML --}}
                     <div class="form__input__single d-grid mt-3">
                         <label for="enable_google_login"><strong>{{__('Enable/Disable Google Login')}}</strong></label>
                         <div class="switch_box style_7"> {{-- Keeping the style_7 class if your CSS uses it --}}
                             <input type="checkbox" id="enable_google_login" name="enable_google_login"  @if(!empty(get_static_option('enable_google_login'))) checked @endif>
                             <label></label>
                         </div>
                         <small class="form-text text-muted">  {{__('Enable, means Frontend register page show social login')}} </small>
                     </div>

                     <div class="form-group">
                         <label for="google_client_id">{{__('Google Client ID')}}</label>
                         <input type="text" id="google_client_id" name="google_client_id"  class="form-control" value="{{get_static_option('google_client_id')}}" placeholder="{{__('Enter Client ID')}}">
                     </div>
                     <div class="form-group">
                         <label for="google_client_secret">{{__('Google Client Secret')}}</label>
                         <input type="text" id="google_client_secret" name="google_client_secret"  class="form-control" value="{{get_static_option('google_client_secret')}}" placeholder="{{__('Enter Client Secret')}}">
                     </div>
                     <div class="form-group">
                         <label for="google_callback_url">{{__('Google Callback URL')}}</label>
                         <input type="text" id="google_callback_url" name="google_callback_url"  class="form-control" value="{{get_static_option('google_callback_url')}}" placeholder="{{__('Enter Callback URL')}}">
                         <p class="info-text">{{__('google callback url for your app')}} <code>{{url('/')}}/google/callback</code> <a href="https://bytesed.com/docs/google-login/" target="_blank"><i class="ti ti-external-link"></i></a></p>
                     </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection