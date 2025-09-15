@extends('frontend.layout.master')
@section('site_title',__('Enquiries'))
@section('style')
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
        i.las.la-trash-alt {
            font-size: 26px;
            color: red;
        }
    </style>
@endsection
@section('content')
    <div class="profile-setting all-enquries section-padding2 setting-page-with-table">
        <div class="container-1920 plr1">
            <div class="row">
                <div class="col-12">
                    <div class="profile-setting-wraper">
                        @include('frontend.user.layout.partials.user-profile-background-image')
                        <div class="down-body-wraper">
                            @include('frontend.user.layout.partials.sidebar')
                            <div class="main-body">
                                <x-validation.frontend-error />
                                <x-frontend.user.responsive-icon/>
                                <div class="paymentTable">
                                    <div class="single-profile-settings" id="display_client_profile_info">
                                        <div class="single-profile-settings-header">
                                            <div class="single-profile-settings-header-flex">
                                                <h4 class="memberTittle"> {{ __('All Enquiries') }} </h4>
                                                <x-search.search-in-table :id="'string_search'" :placeholder="__('Enter date to search')" :class="'form-control radius-10'" />
                                            </div>
                                        </div>
                                        <div class="single-profile-settings-inner profile-border-top">
                                            <div class="custom_table style-04 search_result">
                                                @include('membership::frontend.user.enquiry.search-result')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                       </div>
                   </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('membership::frontend.user.enquiry.enquiry-js')
@endsection
