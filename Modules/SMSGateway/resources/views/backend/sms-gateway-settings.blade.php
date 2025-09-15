@extends('layouts/layoutMaster')

@section('title', __('SMS Gateway Settings'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    var updateStatusRoute = "{{ route('admin.sms.status') }}";
    var otpStatusRoute = "{{ route('admin.sms.login.otp.status') }}";
</script>
@vite([
  'resources/assets/js/sms-gateway-settings.js'
])
@endsection

@section('content')
<div class="row g-4">
  <!-- Header Card -->
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">{{ __('SMS Gateway Settings') }}</h4>
          <p class="text-muted mb-0">{{ __('Manage all SMS gateways from here, you can activate/deactivate any SMS gateway.') }}</p>
        </div>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#settings_option_modal">
            <i class="ti ti-settings me-1"></i>{{ __('SMS Settings') }}
          </button>
          <button type="button" class="btn btn-success btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#test_sms_modal">
            <i class="ti ti-send me-1"></i>{{ __('Test SMS') }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- OTP Status Toggle -->
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-1">{{ __('OTP Login Status') }}</h5>
            <p class="text-muted mb-0">{{ __('Enable or disable OTP verification for user login') }}</p>
          </div>
          <div class="form-check form-switch form-check-primary">
            <input class="form-check-input" type="checkbox" id="otp_login_status" name="otp_login_status" {{ get_static_option('otp_login_status') ? 'checked' : '' }}>
            <label class="form-check-label" for="otp_login_status"></label>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- SMS Gateways Grid -->
  <div class="col-12" id="gateway-grid" style="{{ empty(get_static_option('otp_login_status')) ? 'display: none' : '' }}">
    <div class="row g-4">
      @foreach(\Modules\SMSGateway\app\Http\Services\OtpTraitService::gateways() as $key => $item)
        @php
          $sms_gateway = \Modules\SMSGateway\app\Models\SmsGateway::where('name', $key)->first();
          $status = $sms_gateway->status ?? 0;
          $otp_time = $sms_gateway->otp_expire_time ?? 0;
          $credentials = $sms_gateway->credentials ?? '{}';
        @endphp
        
        <div class="col-xl-4 col-lg-6 col-md-6">
          <div class="card h-100 gateway-card">
            <!-- Gateway Header with Icon -->
            <div class="card-header text-center gateway-header gateway-{{ $key }} {{ $status ? 'active' : '' }}">
              <div class="gateway-icon mb-3">
                @if($key === 'twilio')
                  <i class="ti ti-message-circle-2" style="font-size: 3rem;"></i>
                @elseif($key === 'msg91')
                  <i class="ti ti-message" style="font-size: 3rem;"></i>
                @else
                  <i class="ti ti-messages" style="font-size: 3rem;"></i>
                @endif
              </div>
              <h4 class="text-white mb-0 text-capitalize">{{ $item }}</h4>
              @if($status)
                <span class="badge bg-success mt-2">{{ __('Active') }}</span>
              @else
                <span class="badge bg-secondary mt-2">{{ __('Inactive') }}</span>
              @endif
            </div>

            <div class="card-body text-center">
              <p class="text-muted mb-4">
                {{ __('You can learn more about it from here:') }}
                @if($key === 'twilio')
                  <a href="https://www.twilio.com/" target="_blank" class="text-primary">{{ __('Documentation') }}</a>
                @else
                  <a href="https://www.msg91.com/" target="_blank" class="text-primary">{{ __('Documentation') }}</a>
                @endif
              </p>

              <div class="d-flex gap-2 justify-content-center">
                <button type="button" 
                        class="btn btn-sm waves-effect waves-light gateway-toggle {{ $status ? 'btn-success' : 'btn-outline-secondary' }}"
                        data-option="{{ $key }}"
                        data-status="{{ $status }}">
                  <i class="ti ti-power me-1"></i>
                  {{ $status ? __('Activated') : __('Activate') }}
                </button>

                <button type="button" 
                        class="btn btn-sm btn-outline-primary waves-effect waves-light gateway-settings"
                        data-bs-toggle="modal" 
                        data-bs-target="#{{ $key }}_modal"
                        data-option="{{ $key }}"
                        data-otp-time="{{ $otp_time }}"
                        data-credentials="{{ $credentials }}">
                  <i class="ti ti-settings me-1"></i>
                  {{ __('Settings') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

<!-- Include Modals -->
@include('smsgateway::backend.modal.nexmo_modal')
@include('smsgateway::backend.modal.twilio_modal')
@include('smsgateway::backend.modal.msg91_modal')

<!-- SMS Settings Modal -->
<div class="modal fade" id="settings_option_modal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{{ __('SMS Settings') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.sms.options') }}" method="post">
        @csrf
        <div class="modal-body">
          <h6 class="mb-3">{{ __('Receive SMS when the actions are triggered') }}</h6>
          
          <div class="row g-3">
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="new_user_user" name="new_user_user" {{ get_static_option('new_user_user') ? 'checked' : '' }}>
                <label class="form-check-label" for="new_user_user">
                  {{ __('When new user is registered - for user') }}
                </label>
              </div>
            </div>
            
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="new_user_admin" name="new_user_admin" {{ get_static_option('new_user_admin') ? 'checked' : '' }}>
                <label class="form-check-label" for="new_user_admin">
                  {{ __('When new user is registered - for admin') }}
                </label>
              </div>
            </div>

            <div class="col-12">
              <label for="receiving_phone_number" class="form-label">{{ __('Receiving Phone Number') }}</label>
              <input type="tel" class="form-control" id="receiving_phone_number" name="receiving_phone_number" 
                     value="{{ get_static_option('receiving_phone_number') }}" 
                     placeholder="{{ __('Enter phone number') }}">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">
            {{ __('Cancel') }}
          </button>
          <button type="submit" class="btn btn-primary waves-effect waves-light">
            {{ __('Update Changes') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Test SMS Modal -->
<div class="modal fade" id="test_sms_modal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{{ __('Send Test SMS') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.sms.test') }}" method="post">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="test_phone_number" class="form-label">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
            <input type="tel" class="form-control" id="test_phone_number" name="test_phone_number" 
                   placeholder="{{ __('Enter phone number to test') }}" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">
            {{ __('Cancel') }}
          </button>
          <button type="submit" class="btn btn-success waves-effect waves-light" id="test-sms-btn">
            <i class="ti ti-send me-1"></i>{{ __('Send Test SMS') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
/* Custom Gateway Card Styles */
.gateway-card {
  transition: all 0.3s ease;
  border: 1px solid rgba(0,0,0,0.1);
}

.gateway-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 25px rgba(0,0,0,0.15);
}

.gateway-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 2rem 1rem;
  position: relative;
  overflow: hidden;
}

.gateway-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255,255,255,0.1);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.gateway-header.active::before {
  opacity: 1;
}

.gateway-header.gateway-twilio {
  background: linear-gradient(135deg, #ED213A 0%, #93291E 100%);
}

.gateway-header.gateway-msg91 {
  background: linear-gradient(135deg, #1488CC 0%, #2B32B2 100%);
}

.gateway-header.gateway-nexmo {
  background: linear-gradient(135deg, #5433FF 0%, #20bdff 100%);
}

.gateway-icon {
  opacity: 0.9;
}

.gateway-toggle.btn-success {
  background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
  border: none;
}

.gateway-settings {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  color: white;
}

.gateway-settings:hover {
  background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
  color: white;
}

/* Form Controls Enhancement */
.form-check-input:checked {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-color: #667eea;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.btn-success {
  background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
  border: none;
}

.btn-success:hover {
  background: linear-gradient(135deg, #a8e6cf 0%, #56ab2f 100%);
}

/* Modal Enhancements */
.modal-header {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-bottom: 1px solid rgba(0,0,0,0.1);
}

.modal-content {
  box-shadow: 0 10px 40px rgba(0,0,0,0.15);
  border: none;
}

/* Responsive Design */
@media (max-width: 768px) {
  .gateway-header {
    padding: 1.5rem 1rem;
  }
  
  .gateway-icon i {
    font-size: 2rem !important;
  }
  
  .d-flex.gap-2 .btn {
    flex: 1;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // OTP Status Toggle
    const otpToggle = document.getElementById('otp_login_status');
    const gatewayGrid = document.getElementById('gateway-grid');
    
    otpToggle.addEventListener('change', function() {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("You will be able to revert your decision anytime") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#667eea',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __("Yes!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.get(otpStatusRoute)
                    .then((response) => {
                        if (response.data.type === 'success') {
                            Swal.fire(
                                '{{ __("Updated!") }}',
                                '{{ __("Settings updated successfully") }}',
                                'success'
                            );
                            gatewayGrid.style.display = this.checked ? 'block' : 'none';
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        this.checked = !this.checked;
                    });
            } else {
                this.checked = !this.checked;
            }
        });
    });

    // Gateway Toggle
    document.querySelectorAll('.gateway-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const optionName = this.dataset.option;
            const status = this.dataset.status;
            
            Swal.fire({
                title: '{{ __("Are you sure?") }}',
                text: '{{ __("You will be able to revert your decision anytime") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __("Yes!") }}',
                cancelButtonText: '{{ __("Cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(updateStatusRoute, {
                        option_name: optionName,
                        status: status
                    })
                    .then((response) => {
                        if (response.data.type === 'success') {
                            location.reload();
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        Swal.fire(
                            '{{ __("Error!") }}',
                            '{{ __("An error occurred while updating") }}',
                            'error'
                        );
                    });
                }
            });
        });
    });

    // Gateway Settings Modal
    document.querySelectorAll('.gateway-settings').forEach(button => {
        button.addEventListener('click', function() {
            const option = this.dataset.option;
            const otpTime = this.dataset.otpTime;
            const credentials = JSON.parse(this.dataset.credentials);
            
            const modal = document.getElementById(`${option}_modal`);
            
            // Fill in credentials
            Object.keys(credentials).forEach(key => {
                const input = modal.querySelector(`input[name="${key}"]`);
                if (input) {
                    input.value = credentials[key];
                }
            });
            
            // Set OTP expire time
            const selectElement = modal.querySelector('select[name="user_otp_expire_time"]');
            if (selectElement) {
                const option = selectElement.querySelector(`option[value="${otpTime}"]`);
                if (option) {
                    option.selected = true;
                }
            }
        });
    });
});
</script>
@endsection