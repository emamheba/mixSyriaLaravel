@extends('layouts/layoutMaster')

@section('title', __('Basic Settings'))

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
        $('.form-select').select2();
        
        // Handle email verification toggle
        const emailVerifyToggle = document.getElementById('user_email_verify_enable_disable');
        if (emailVerifyToggle) {
            emailVerifyToggle.addEventListener('change', function() {
                if (this.checked) {
                    const otpToggle = document.getElementById('user_otp_verify_enable_disable');
                    if (otpToggle) otpToggle.checked = false;
                }
            });
        }

        // Handle OTP verification toggle
        const otpVerifyToggle = document.getElementById('user_otp_verify_enable_disable');
        if (otpVerifyToggle) {
            otpVerifyToggle.addEventListener('change', function() {
                if (this.checked) {
                    const emailToggle = document.getElementById('user_email_verify_enable_disable');
                    if (emailToggle) emailToggle.checked = false;
                }
            });
        }

        // Form submission with loading state
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('update');
        
        if (form && submitBtn) {
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("Updating...") }}';
            });
        }
    });
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">{{ __('Basic Settings') }}</h4>
                    <p class="text-muted mb-0">{{ __('Configure your website basic settings and preferences') }}</p>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                        </li>
                        <li class="breadcrumb-item">{{ __('General Settings') }}</li>
                        <li class="breadcrumb-item active">{{ __('Basic Settings') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-settings me-2"></i>{{ __('Basic Configuration') }}
                    </h5>
                </div>
                
                <div class="card-body">
                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <h6 class="alert-heading mb-1">{{ __('Please fix the following errors:') }}</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.general.basic.settings') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Basic Information Section -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-muted fw-semibold mb-3">
                                    <i class="ti ti-info-circle me-1"></i>{{ __('Site Information') }}
                                </h6>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="site_title" class="form-label">{{ __('Site Title') }}</label>
                                <input type="text" 
                                       name="site_title" 
                                       id="site_title" 
                                       class="form-control" 
                                       value="{{ get_static_option('site_title') }}"
                                       placeholder="{{ __('Enter site title') }}">
                            </div>

                            <div class="col-md-6">
                                <label for="site_tag_line" class="form-label">{{ __('Site Tag Line') }}</label>
                                <input type="text" 
                                       name="site_tag_line" 
                                       id="site_tag_line" 
                                       class="form-control" 
                                       value="{{ get_static_option('site_tag_line') }}"
                                       placeholder="{{ __('Enter site tagline') }}">
                            </div>

                            <div class="col-md-6">
                                <label for="site_footer_copyright" class="form-label">{{ __('Footer Copyright') }}</label>
                                <input type="text" 
                                       name="site_footer_copyright" 
                                       id="site_footer_copyright" 
                                       class="form-control" 
                                       value="{{ get_static_option('site_footer_copyright') }}"
                                       placeholder="{{ __('Enter copyright text') }}">
                                <div class="form-text">
                                    <i class="ti ti-info-circle me-1"></i>
                                    {{ __('{copy} will replace by ©; and {year} will be replaced by current year.') }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="site_canonical_url_type" class="form-label">{{ __('Canonical URL Type') }}</label>
                                <select name="site_canonical_url_type" id="site_canonical_url_type" class="form-select">
                                    <option value="self" @if(get_static_option('site_canonical_url_type') === 'self') selected @endif>
                                        {{ __('Self') }}
                                    </option>
                                    <option value="alternative" @if(get_static_option('site_canonical_url_type') === 'alternative') selected @endif>
                                        {{ __('Alternative') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- System Configuration Section -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-muted fw-semibold mb-3">
                                    <i class="ti ti-settings-cog me-1"></i>{{ __('System Configuration') }}
                                </h6>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <!-- User Email Verification -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ __('User Email Verification') }}</h6>
                                                <small class="text-muted">{{ __('Require users to verify their email') }}</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="user_email_verify_enable_disable" 
                                                       id="user_email_verify_enable_disable"
                                                       @if(!empty(get_static_option('user_email_verify_enable_disable'))) checked @endif>
                                                <label class="form-check-label" for="user_email_verify_enable_disable"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Maintenance Mode -->
                            {{-- <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ __('Maintenance Mode') }}</h6>
                                                <small class="text-muted">{{ __('Enable site maintenance mode') }}</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="site_maintenance_mode" 
                                                       id="site_maintenance_mode"
                                                       @if(!empty(get_static_option('site_maintenance_mode'))) checked @endif>
                                                <label class="form-check-label" for="site_maintenance_mode"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <!-- SSL Redirection -->
                            {{-- <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ __('Force SSL Redirection') }}</h6>
                                                <small class="text-muted">{{ __('Force HTTPS for all requests') }}</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="site_force_ssl_redirection" 
                                                       id="site_force_ssl_redirection"
                                                       @if(!empty(get_static_option('site_force_ssl_redirection'))) checked @endif>
                                                <label class="form-check-label" for="site_force_ssl_redirection"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <!-- Admin Preloader -->
                            {{-- <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ __('Admin Preloader') }}</h6>
                                                <small class="text-muted">{{ __('Show loading animation in admin panel') }}</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="admin_loader_animation" 
                                                       id="admin_loader_animation"
                                                       @if(!empty(get_static_option('admin_loader_animation'))) checked @endif>
                                                <label class="form-check-label" for="admin_loader_animation"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <!-- Site Preloader -->
                            {{-- <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ __('Site Preloader') }}</h6>
                                                <small class="text-muted">{{ __('Show loading animation on website') }}</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="site_loader_animation" 
                                                       id="site_loader_animation"
                                                       @if(!empty(get_static_option('site_loader_animation'))) checked @endif>
                                                <label class="form-check-label" for="site_loader_animation"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                        {{ __('Cancel') }}
                                    </button>
                                    <button type="submit" id="update" class="btn btn-primary">
                                        <i class="ti ti-check me-1"></i>
                                        {{ __('Update Settings') }}
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
@endsection