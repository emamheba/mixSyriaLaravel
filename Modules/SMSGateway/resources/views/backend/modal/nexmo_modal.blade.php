
{{-- Nexmo Modal --}}
<div class="modal fade" tabindex="-1" id="nexmo_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="ti ti-messages me-2"></i>{{ __('Nexmo Configuration') }}
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.sms.gateway.update') }}" method="POST">
                @csrf
                <input type="hidden" name="sms_gateway_name" value="nexmo">
                
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                {{ __('Configure your Nexmo credentials to enable SMS functionality') }}
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="nexmo_api_key" class="form-label">
                                {{ __('API Key') }} <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-key"></i></span>
                                <input type="text" class="form-control" name="nexmo_api_key" 
                                       placeholder="{{ __('Enter Nexmo API Key') }}" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="nexmo_api_secret" class="form-label">
                                {{ __('API Secret') }} <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                <input type="text" class="form-control" name="nexmo_api_secret" 
                                       placeholder="{{ __('Enter API Secret') }}" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="user_otp_expire_time" class="form-label">
                                {{ __('OTP Expire Time') }}
                            </label>
                            <select name="user_otp_expire_time" class="form-select">
                                <option value="30">{{ __('30 Seconds') }}</option>
                                @for($i=1; $i<=5; $i=$i+0.5)
                                    <option value="{{ $i }}">
                                        {{ __($i . ($i > 1 ? ' Minutes' : ' Minute')) }}
                                    </option>
                                @endfor
                            </select>
                            <div class="form-text">{{ __('Set how long the OTP remains valid') }}</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <i class="ti ti-check me-1"></i>{{ __('Update Settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
