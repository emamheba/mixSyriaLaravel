@extends('frontend.layout.master')
@section('title')
    {{__('User OTP Login')}}
@endsection
@section('page-title')
    {{__('User OTP Login')}}
@endsection
@section('style')
    <style>
        #telephone.error {
            border-color: var(--main-color-one);
        }

        #telephone.success {
            border-color: var(--main-color-three);
        }

        .single-input .iti {
            width: 100%;
        }
    </style>
@endsection
@section('content')
    <div class="loginArea section-padding2">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-sm-12 login-Wrapper">
                    <div class="text-center mb-3">
                        <h3 class="tittle">{{ __('OTP Sign In') }}</h3>
                    </div>
                    <x-validation.frontend-error/>
                    <form action="{{route('user.login.otp')}}" method="post" enctype="multipart/form-data"  class="account-form" id="login_form_order_page">
                        @csrf
                        <div class="error-wrap"></div>
                        <div class="row">
                            <div class="col-12">
                                <label class="infoTitle">{{ __('Phone Number') }}</label>
                                <div class="input-form input-form2">
                                    <input type="hidden" id="country-code" name="country_code">
                                    <input type="tel" name="phone" value="{{old('phone')}}" id="phone" placeholder="{{__('Type Phone')}}">
                                    <span id="phone_availability"></span>

                                    <div class="d-none">
                                        <span id="error-msg" class="hide"></span>
                                        <p id="result" class="d-none"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="btn-wrapper text-center mt-30">
                                    <button type="submit" id="login_btn"  class="cmn-btn4 w-100 mb-60 verify-account">{{ __('Send OTP') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <p class="info mt-3">{{__("Do not have an account")}}
                        <a href="{{route('user.login')}}"   class="active"> <strong>{{__('Sign In')}}</strong> </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
   @include('smsgateway::user.phone-number-check')
@endsection
