@extends('frontend.layout.master')
@section('title')
    {{__('User OTP Verification')}}
@endsection
@section('page-title')
    {{__('Verify OTP')}}
@endsection
@section('style')
    <style>
        .active:hover{
            color: var(--main-color-one);
        }
    </style>
@endsection
@section('content')
    <div class="loginArea section-padding2">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-sm-12 login-Wrapper">
                    <div class="text-center mb-3">
                        <h3 class="tittle">{{ __('Verify OTP') }}</h3>
                        <h5 class="countdown text-center my-2"></h5>
                        <div class="alert alert-info alert-bs-dismissible fade show mt-5 mb-1 mx-auto d-inline-block"
                             role="alert"> {{__('An OTP has been sent on your phone number.')}}
                        </div>
                    </div>
                    <x-validation.frontend-error/>
                        <form action="{{route('user.login.otp.verification')}}" method="post" enctype="multipart/form-data" class="account-form" id="login_form_order_page">
                            @csrf
                            <div class="error-wrap"></div>
                            <div class="row">
                                <div class="col-12">
                                    <label for="exampleInputEmail1" class="infoTitle">{{__('OTP Code')}} </label>
                                    <div class="input-form input-form2">
                                      <input class="form--control" type="number" name="otp" value="{{old('otp')}}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="btn-wrapper text-center mt-50">
                                        <button type="submit" id="login_btn"  class="cmn-btn4 w-100 mb-60 verify-account">{{ __('Send OTP') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <p class="info mt-3 d-flex justify-content-between">
                        <a href="{{route('user.login.otp')}}" class="active"> {{__('Update number?')}} </a>
                        <a href="{{route('user.login.otp.resend')}}" class="active"> {{__('Resend OTP code again?')}} </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @php
        $expire_time = 0;

        if (!empty($userOtp) && !now()->isAfter($userOtp->expire_date)){
            $expire_time = $userOtp ? now()->diffInRealSeconds($userOtp->expire_date) : 0;
        }
    @endphp
    <script>
        let expire_time = `{{$expire_time}}`;

        let interval = setInterval(function() {
            if (expire_time > 0)
            {
                expire_time--;
            }

            let countdown = $('.countdown');
            if (parseInt(expire_time) === 0)
            {
                countdown.removeClass('text-dark').addClass('text-danger').text(`{{__('The OTP is expired')}}`)
                return clearInterval(interval);
            }

            countdown.addClass('text-dark').text(expire_time + ` {{__('Seconds')}}`)
        }, 1000);
    </script>
@endsection
