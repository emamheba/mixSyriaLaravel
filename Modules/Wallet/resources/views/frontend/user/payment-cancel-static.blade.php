@extends('frontend.layout.master')
@section('site_title',__('Payment Cancel'))
@section('content')
    <div class="proDetails section-padding2">
        <div class="container-1310">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8 col-md-8 ">
                    <h4 class="congratulation-contents-title"> {{ __('OPPS!') }} </h4>
                    <p class="congratulation-contents-para">{{ __('Payment') }} <strong>{{ __('Cancel') }}</strong> </p>
                    <div class="btn-wrapper mt-4">
                        <a href="{{ route('user.wallet.history') }}" class="btn-profile btn-bg-1">{{ __('Back to Deposit') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



