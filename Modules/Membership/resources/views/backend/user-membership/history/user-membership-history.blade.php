@extends('layouts/layoutMaster')
@section('site-title')
    {{ __('User Memberships History') }}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('User membership History') }}</h4>
                            <span class="text-info">{{ __('user earlier membership history list') }}</span>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <div class="d-flex text-right w-100 mt-3">
                                <input class="form__control" name="string_search" id="string_search" placeholder="{{ __('Search') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-2 mb-2">
                    <div class="dashboard_orderDetails__estimate__author">
                        <h6 class="mt-2 mb-2">{{ __('User Details:') }}</h6>
                        <div class="dashboard_orderDetails__estimate__author__flex">
                            <div class="dashboard_orderDetails__estimate__author__thumb">
                                {!! render_image_markup_by_attachment_id($user_info->image, ' ', 'thumb') !!}
                            </div>
                            <div class="dashboard_orderDetails__estimate__author__contents">
                                <h6 class="dashboard_orderDetails__estimate__author__name">{{ $user_info->fullname }}</h6>
                                <p class="dashboard_orderDetails__estimate__author__email">{{ $user_info->email }}</p>
                                <p class="dashboard_orderDetails__estimate__author__email">{{ $user_info->phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Membership filter section end -->
                <div class="custom_table style-04 search_result">
                    @include('membership::backend.user-membership.history.search-result')
                </div>
            </div>
        </div>
    </div>
    @include('membership::backend.user-membership.history.history-manual-payment-modal')
@endsection
@section('scripts')
    @include('membership::backend.user-membership.history.membership-history-js')
@endsection
