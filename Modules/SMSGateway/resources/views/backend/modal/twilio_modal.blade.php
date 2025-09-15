<div class="modal fade" tabindex="-1" id="twilio_modal">
    <div class="modal-dialog modal-dialog-centered"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <h4 class="modal-title"> 
                    <i class="ti ti-message me-2"></i>{{ __('Twilio Configuration') }} 
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button> 
            </div>

            <form action="{{route('admin.sms.gateway.update')}}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="sms_gateway_name" value="twilio">

                <div class="modal-body"> 
                    <div class="row g-3"> 

                        <div class="col-12">
                            <div class="alert alert-info"> 
                                <i class="ti ti-info-circle me-2"></i>
                                {{ __('Configure your Twilio credentials to enable SMS functionality') }} 
                            </div>
                        </div>

                      
                        <div class="col-12"> 
                            <label for="TWILIO_SID" class="form-label"> 
                                <strong>{{__('Twilio SID')}} <span class="text-danger">*</span> </strong>
                            </label>
                            <div class="input-group"> 
                                <span class="input-group-text"><i class="ti ti-key"></i></span> 
                                <input type="text"  class="form-control" name="twilio_sid" value=""
                                       placeholder="{{ __('Enter Twilio SID')}}" required> 
                            </div>
                        </div>

                        <div class="col-12"> 
                            <label for="TWILIO_AUTH_TOKEN" class="form-label"> 
                                <strong>{{__('Twilio Auth Token')}} <span class="text-danger">*</span></strong>
                            </label>
                            <div class="input-group"> 
                                <span class="input-group-text"><i class="ti ti-key"></i></span> 
                                <input type="text"  class="form-control" name="twilio_auth_token" value=""
                                       placeholder="{{ __('Enter Twilio Auth Token')}}" required> 
                            </div>
                        </div>

                        <div class="col-12"> 
                            <label for="TWILIO_NUMBER" class="form-label">
                                <strong>{{__('Valid Twilio Number')}} <span class="text-danger">*</span> </strong>
                            </label>
                            <div class="input-group"> 
                                <span class="input-group-text"><i class="ti ti-phone"></i></span> 
                                <input type="text" class="form-control" name="twilio_number" value=""
                                       placeholder="{{ __('Enter Valid Twilio Number')}}" required> 
                            </div>
                        </div>

                      
                        <div class="col-12"> 
                            <label for="user_otp_expire_time" class="form-label"> 
                                <strong>{{__('OTP Expire Time')}}</strong> 
                            </label>
                            <select name="user_otp_expire_time" class="form-select"> 
                                <option  value="30">{{__('30 Seconds')}}</option> 
                                @for($i=1; $i<=5; $i=$i+0.5)
                                    <option value="{{$i}}">{{__($i . ($i > 1 ? ' Minutes' : ' Minute'))}}</option>
                                @endfor
                            </select>
                            <div class="form-text">{{__('Set how long the OTP remains valid')}}</div> 
                        </div>

                    

                    </div> 
                </div> 

                <div class="modal-footer"> 
                     <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">
                        {{ __('Cancel') }} 
                    </button>
                    <button type="submit" id="update" class="btn btn-primary waves-effect waves-light"> 
                        <i class="ti ti-check me-1"></i>{{__('Update Settings')}} 
                    </button>
                </div>
            </form>
        </div> 
    </div> 
</div>