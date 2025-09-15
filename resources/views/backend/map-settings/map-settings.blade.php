@extends('layouts/layoutMaster')

@section('title', __('Google Map Settings'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/bootstrap-switch/bootstrap-switch.scss'
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
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const updateBtn = document.getElementById('update');
    const form = document.querySelector('form');

    if (form && updateBtn) {
        form.addEventListener('submit', function() {
            updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("Updating...") }}';
            updateBtn.disabled = true;
        });
    }

    const mainToggle = document.querySelector('input[name="google_map_settings_on_off"]');
    const settingsContainer = document.getElementById('map-settings-container');

    function toggleSettings() {
        if (mainToggle.checked) {
            settingsContainer.style.display = 'block';
            settingsContainer.classList.remove('fade-out');
            settingsContainer.classList.add('fade-in');
        } else {
            settingsContainer.style.display = 'none';
            settingsContainer.classList.remove('fade-in');
            settingsContainer.classList.add('fade-out');
        }
    }

    if (mainToggle && settingsContainer) {
        mainToggle.addEventListener('change', toggleSettings);
        toggleSettings();
    }
});
</script>

<style>
.fade-in {
    animation: fadeIn 0.3s ease-in forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}


.page-header {
    background-color: #f8f9fa;
    padding: 1.5rem 0;
    margin-bottom: 2rem;
    border-bottom: 1px solid #e0e0e0;
}

.page-header h4 {
    color: #343a40;
    font-weight: 600;
}

.page-header p {
    color: #5a6575;
    margin-bottom: 0;
}


.card {
    border-radius: 16px;
    overflow: hidden;
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.card.help-card {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.settings-header, .help-header {
    background: linear-gradient(135deg, #696cff 0%, #8b5cf6 100%);
    color: white;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.settings-header .icon-wrapper, .help-header .icon-wrapper {
    background: rgba(255, 255, 255, 0.2);
    width: 40px;
    height: 40px;
    border-radius: 8px;
}

.settings-header h5, .help-header h5 {
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: white;
}

.settings-header p, .help-header p {
     color: rgba(255, 255, 255, 0.8);
     margin-bottom: 0;
     font-size: 0.9rem;
}


.status-badge {
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    text-transform: uppercase;
}

.status-badge.active {
    background-color: rgba(40, 199, 111, 0.15);
    color: #28c76f;
}

.status-badge.inactive {
    background-color: rgba(234, 84, 85, 0.15);
    color: #ea5455;
}


.toggle-section {
    background-color: #f0f2ff;
    border: 1px solid #d0d8ff;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.toggle-info h6 {
    margin-bottom: 0.3rem;
    font-weight: 600;
    color: #343a40;
}

.toggle-info small {
    color: #6c757d;
    line-height: 1.4;
    display: block;
}


.switch-modern {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 28px;
    flex-shrink: 0;
}

.switch-modern input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #696cff;
}

input:checked + .slider:before {
    transform: translateX(22px);
}


.form-floating-modern {
    position: relative;
    margin-bottom: 1.5rem;
}

.form-floating-modern .form-control {
    height: 56px;
    border: 1px solid #d0d8ff;
    border-radius: 8px;
    padding: 1.5rem 1rem 0.5rem;
    background: #fff;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    box-shadow: none;
}

.form-floating-modern .form-control:focus {
    border-color: #696cff;
    box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
    background: #fff;
    outline: none;
}

.form-floating-modern label {
    position: absolute;
    top: 0.8rem;
    left: 1rem;
    background: white;
    padding: 0 0.4rem;
    color: #6c757d;
    transition: all 0.3s ease;
    pointer-events: none;
    font-size: 0.85rem;
    font-weight: 500;
    z-index: 2;
    transform-origin: 0 0;
    transform: translateY(0) scale(1);
    will-change: transform;
}

.form-floating-modern .form-control:focus ~ label,
.form-floating-modern .form-control:not(:placeholder-shown) ~ label {
    transform: translateY(-0.9rem) scale(0.85);
    color: #696cff;
}


.btn-modern {
    background: linear-gradient(135deg, #696cff 0%, #8b5cf6 100%);
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(105, 108, 255, 0.3);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(105, 108, 255, 0.4);
    background: linear-gradient(135deg, #5a67d8 0%, #805ad5 100%);
     color: white;
}

.btn-modern:focus {
    box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25), 0 4px 15px rgba(105, 108, 255, 0.3);
}


.quick-info-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem 0;
    border-bottom: 1px solid #e9ecef;
}

.quick-info-item:last-child {
    border-bottom: none;
}

.info-badge {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
    font-size: 16px;
}

.info-content h6 {
    margin-bottom: 0.2rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: #343a40;
}

.info-content small {
    color: #6c757d;
    line-height: 1.4;
    font-size: 0.85rem;
}

.info-badge.bg-primary { background-color: #696cff !important; }
.info-badge.bg-success { background-color: #28c76f !important; }
.info-badge.bg-warning { background-color: #ffab00 !important; }


@media (max-width: 991.98px) {
    .page-header {
        padding: 1rem 0;
        margin-bottom: 1.5rem;
    }
    .toggle-section {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    .switch-modern {
        align-self: flex-end;
    }
}

</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="page-header mb-4">
    <div class="container-xxl flex-grow-1">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <h4 class="fw-bold mb-1">
            <i class="ti ti-map-pin me-2 text-primary"></i>
            {{ __('Google Map Settings') }}
          </h4>
          <p>{{ __('Configure Google Maps integration for your application') }}</p>
        </div>
        <div class="status-badge {{ !empty(get_static_option('google_map_settings_on_off')) ? 'active' : 'inactive' }}">
          <i class="ti ti-circle-filled" style="font-size: 8px;"></i>
          {{ !empty(get_static_option('google_map_settings_on_off')) ? __('Active') : __('Inactive') }}
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-xl-8 col-lg-7">
      <div class="card shadow-sm">
        <div class="settings-header">
          <div class="d-flex align-items-center">
            <div class="icon-wrapper me-3">
              <i class="ti ti-settings text-white" style="font-size: 20px;"></i>
            </div>
            <div>
              <h5 class="mb-1">{{ __('Map Configuration') }}</h5>
              <p class="small">{{ __('Configure your Google Maps API settings') }}</p>
            </div>
          </div>
        </div>

        <div class="card-body p-4">
          <x-validation.error/>

          <form action="{{ route('admin.map.settings.page') }}" method="POST">
            @csrf

            <div class="toggle-section">
              <div class="toggle-info">
                <h6>{{ __('Enable Google Maps') }}</h6>
                <small>{{ __('Turn on/off Google Maps functionality') }}</small>
              </div>
              <label class="switch-modern">
                <input type="checkbox" name="google_map_settings_on_off" @if(!empty(get_static_option('google_map_settings_on_off'))) checked @endif>
                <span class="slider"></span>
              </label>
            </div>

            <div id="map-settings-container" class="settings-container">
              <div class="row g-4">
                <div class="col-12">
                  <div class="form-floating-modern">
                    <input
                      type="text"
                      class="form-control"
                      name="google_map_api_key"
                      id="google_map_api_key"
                      value="{{ get_static_option('google_map_api_key') }}"
                      placeholder=" "
                    >
                    <label for="google_map_api_key">{{ __('Google Map API Key') }}</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating-modern">
                    <input
                      type="text"
                      class="form-control"
                      name="google_map_search_placeholder_title"
                      id="google_map_search_placeholder_title"
                      value="{{ get_static_option('google_map_search_placeholder_title') }}"
                      placeholder=" "
                    >
                    <label for="google_map_search_placeholder_title">{{ __('Search Placeholder Text') }}</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating-modern">
                    <input
                      type="text"
                      class="form-control"
                      name="google_map_search_button_title"
                      id="google_map_search_button_title"
                      value="{{ get_static_option('google_map_search_button_title') }}"
                      placeholder=" "
                    >
                    <label for="google_map_search_button_title">{{ __('Search Button Text') }}</label>
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex gap-3 mt-4 pt-3 border-top">
              <button type="submit" id="update" class="btn btn-modern flex-fill">
                <i class="ti ti-device-floppy me-2"></i>
                {{ __('Update Settings') }}
              </button>
              <button type="reset" class="btn btn-outline-secondary">
                <i class="ti ti-refresh me-2"></i>
                {{ __('Reset') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-lg-5">
      <div class="card help-card">
        <div class="help-header">
          <div class="d-flex align-items-center">
            <div class="icon-wrapper me-3">
              <i class="ti ti-help text-white" style="font-size: 20px;"></i>
            </div>
            <div>
              <h5 class="mb-1">{{ __('Need Help?') }}</h5>
              <p class="small">{{ __('Get assistance with setup') }}</p>
            </div>
          </div>
        </div>

        <div class="card-body p-4">
          <p class="text-muted mb-4">{{ __('Learn how to generate your Google Maps API key with our step-by-step video guide.') }}</p>

          <a href="https://www.youtube.com/watch?v=2_HZObVbe-g"
             target="_blank"
             class="btn btn-primary w-100 mb-4"
             data-bs-toggle="tooltip"
             title="{{ __('Open in new tab') }}">
            <i class="ti ti-brand-youtube me-2"></i>
            {{ __('Watch Tutorial Video') }}
          </a>
        </div>
      </div>

      <div class="card help-card mt-4">
        <div class="card-body p-4">
          <h6 class="fw-semibold mb-3">
            <i class="ti ti-info-circle me-2 text-info"></i>
            {{ __('Quick Info') }}
          </h6>

          <div class="quick-info-item">
            <div class="info-badge bg-primary">
              <i class="ti ti-key text-white"></i>
            </div>
            <div class="info-content">
              <h6>{{ __('API Key Required') }}</h6>
              <small>{{ __('You need a valid Google Maps API key to use this feature') }}</small>
            </div>
          </div>

          <div class="quick-info-item">
            <div class="info-badge bg-success">
              <i class="ti ti-shield-check text-white"></i>
            </div>
            <div class="info-content">
              <h6>{{ __('Secure & Safe') }}</h6>
              <small>{{ __('Your API key is stored securely and encrypted') }}</small>
            </div>
          </div>

          <div class="quick-info-item">
            <div class="info-badge bg-warning">
              <i class="ti ti-refresh text-white"></i>
            </div>
            <div class="info-content">
              <h6>{{ __('Real-time Updates') }}</h6>
              <small>{{ __('Changes take effect immediately after saving') }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection