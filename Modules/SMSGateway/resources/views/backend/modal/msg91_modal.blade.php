{{-- MSG91 Modal --}}
<div class="modal fade" tabindex="-1" id="msg91_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="ti ti-message me-2"></i>{{ __('MSG91 Configuration') }}
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.sms.gateway.update') }}" method="POST">
                @csrf
                <input type="hidden" name="sms_gateway_name" value="msg91">
                
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                {{ __('Configure your MSG91 credentials to enable SMS functionality') }}
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="msg91_auth_key" class="form-label">
                                {{ __('MSG91 Auth Key') }} <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-key"></i></span>
                                <input type="text" class="form-control" name="msg91_auth_key" 
                                       placeholder="{{ __('Enter MSG91 Auth Key') }}" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="msg91_otp_template_id" class="form-label">
                                {{ __('OTP Template ID') }} <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-template"></i></span>
                                <input type="text" class="form-control" name="msg91_otp_template_id" 
                                       placeholder="{{ __('Enter OTP Template ID') }}" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="msg91_notify_user_register_template_id" class="form-label">
                                {{ __('User Register Template ID') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-user-plus"></i></span>
                                <input type="text" class="form-control" name="msg91_notify_user_register_template_id" 
                                       placeholder="{{ __('Enter User Register Template ID') }}">
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
