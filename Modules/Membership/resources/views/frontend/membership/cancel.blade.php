@extends('frontend.layout.master')
@section('site_title',__('Payment Cancel'))
@section('content')
    <section class="aboutArea section-padding2 plr sectionBg1">
        <div class="container-fluid">
            <div class="row justify-content-between align-items-center">
                <div class="col-xxl-12 col-xl-12 col-lg-12">
                    <div class="about-caption">
                        <!-- Section Tittle -->
                        <div class="section-tittle section-tittle2 mb-40">
                            <h2 class="tittle wow fadeInUp text-warning" data-wow-delay="0.1s">{{ __('OPPS!') }}</h2>
                            <p  class="wow fadeInUp" data-wow-delay="0.2s">{{ __('Payment') }} <strong class="text-danger">{{ __('Cancel') }}</strong> </p>
                        </div>
                        <div class="btn-wrapper">
                            <a href="{{ route('user.membership.all') }}" class="cmn-btn3 mb-10 wow fadeInRight" data-wow-delay="0.3s">{{ __('Back to Membership') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
