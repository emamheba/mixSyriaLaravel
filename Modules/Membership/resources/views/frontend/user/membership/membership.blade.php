@php
$configData = appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Membership'))

<!-- Page Styles -->
@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-pricing.scss'])
<style>
    .search_wrapper {
        display: flex;
        justify-content: flex-end;
    }
    input#string_search {
        padding: 10px;
        border: 1px solid #DFDFDF;
        border-radius: 6px;
    }
    .memberTittle {
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }
    .badge-status {
        font-size: 0.875em;
        padding: 0.4em 0.8em;
    }
    .activeUser { background-color: #28a745; }
    .pending-status { background-color: #ffc107; }
    .cancel-status { background-color: #dc3545; }
</style>
@endsection

@section('content')
<div class="card">
  <!-- Membership Content -->
  <div class="pb-4 rounded-top">
    <div class="container py-12 px-xl-10 px-4">
      <div class="profile-setting menberhsip-plan-new section-padding2">
        <div class="container-1920 plr1">
            <div class="row">
                <div class="col-12">
                    <div class="profile-setting-wraper">
                        <div class="down-body-wraper">
                            <div class="main-body">
                                <x-validation.frontend-error/>
                                <x-frontend.user.responsive-icon/>
                                <div class="your-plan d-flex justify-content-between align-items-center mb-5">
                                    <h3 class="your-plan-head">{{ __('Your Plan') }}</h3>
                                    @php $page_url = \App\Models\Backend\Page::find(get_static_option('membership_plan_page')); @endphp
                                    <a href="@if($page_url){{ url('/' . $page_url->slug) }}@endif" class="btn btn-primary">
                                        {{ __('See All Plans') }} <i class="fa-solid fa-angle-right ms-2"></i>
                                    </a>
                                </div>

                                @include('membership::frontend.user.membership.user-dashboard-membership-plans')

                                @if(!empty($user_membership))
                                <!-- Current Membership Section -->
                                <div class="memberShipCart mt-4">
                                    <div class="card border shadow-none">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between mb-4">
                                                <h4 class="mb-0">{{ __('Verified Membership') }}
                                                    @php
                                                        $today = now();
                                                        $expireDate = \Carbon\Carbon::parse($user_membership->expire_date);
                                                    @endphp
                                                    <span class="badge badge-status 
                                                        @if($expireDate >= $today && $user_membership->payment_status == 'complete' && $user_membership->status === 1) activeUser
                                                        @elseif($expireDate >= $today && $user_membership->payment_status == 'pending' && $user_membership->status === 0) pending-status
                                                        @elseif($expireDate >= $today && $user_membership->payment_status == 'complete' && $user_membership->status === 0) pending-status
                                                        @elseif($expireDate >= $today && empty($user_membership->payment_status) && $user_membership->status === 0) pending-status
                                                        @else cancel-status
                                                        @endif">
                                                        @if($expireDate >= $today && $user_membership->payment_status == 'complete' && $user_membership->status === 1)
                                                            {{ __('Active') }}
                                                        @elseif($expireDate >= $today && $user_membership->payment_status == 'pending' && $user_membership->status === 0)
                                                            {{ __('Inactive') }}
                                                        @elseif($expireDate >= $today && $user_membership->payment_status == 'complete' && $user_membership->status === 0)
                                                            {{ __('Inactive') }}
                                                        @elseif($expireDate >= $today && empty($user_membership->payment_status) && $user_membership->status === 0)
                                                            {{ __('Inactive') }}
                                                        @else
                                                            {{ __('Expire') }}
                                                        @endif
                                                    </span>
                                                </h4>
                                                <div class="btn-group">
                                                    <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#current_membership_modal">
                                                        {{ get_static_option('current_membership_button_title') ?? __('View') }}
                                                    </a>
                                                    @if($user_membership->price != 0)
                                                        <a href="@if($page_url){{ url('/' . $page_url->slug) }}@endif" class="btn btn-primary ms-2">{{__('Upgrade') }}</a>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-1"><strong>{{ __('Billing:') }}</strong> {{ $user_membership->membership?->membership_type?->type }}</p>
                                                    <p class="mb-1"><strong>{{ __('Expire Date:') }}</strong> {{ \Carbon\Carbon::parse($user_membership->expire_date)->isoFormat('DD, MMM YYYY') }}</p>
                                                </div>
                                                @if($user_membership->price != 0)
                                                <div class="col-md-6 text-end">
                                                    <p class="text-muted mb-0">{{ calculateMembershipRemainingTime($user_membership->expire_date) }} remaining</p>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                

                                <!-- Membership History -->
                                <div class="paymentTable mt-5">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h4 class="mb-0">{{__('Membership History')}}</h4>
                                            <x-search.search-in-table :id="'string_search'" :placeholder="__('Enter date to search')" />
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive search_result">
                                                @include('membership::frontend.user.membership.search-result')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Membership Content -->
</div>

@include('membership::addon-view.gateway-markup')
@include('membership::frontend.user.membership.renew-gateway-markup')
@include('membership::frontend.user.membership.user-current-membership-modal')
@include('membership::frontend.user.membership.user-membership-payment-history-modal')
@endsection

@section('page-script')
@vite(['resources/assets/js/pages-pricing.js'])
@include('membership::frontend.user.membership.membership-js')
@include('membership::frontend.user.membership.user-membership-payment-history-modal-js')
@endsection