@extends('backend.admin-master')
@section('site-title')
    {{__('Manual Membership Payment Complete Email To Admin')}}
@endsection
@section('style')
    <x-media.css/>
    <x-summernote.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard_orderDetails__header__flex">
                    <div class="dashboard_orderDetails__header__left">
                         <h2 class="dashboard__card__header__title mb-3">{{__('Manual Membership Payment Complete Email To Admin')}}</h2>
                    </div>
                    <div class="dashboard_orderDetails__header__right">
                        <a href="{{route('admin.email.template.all')}}" class="cmnBtn btn_5 btn_bg_info radius-5">{{__('All Email Templates')}}</a>
                    </div>
                </div>
                <x-validation.error/>
                <form action="{{route('admin.email.user.membership.manual.payment.complete.to.admin.template')}}" method="POST">
                    @csrf
                    <div class="form__input__single">
                        <label for="user_membership_manual_payment_complete_to_admin_email_subject" class="form__input__single__label">{{__('Email Subject')}}</label>
                        <input type="text" class="form__control radius-5" name="user_membership_manual_payment_complete_to_admin_email_subject" value="{{get_static_option('user_membership_manual_payment_complete_to_admin_email_subject') ?? 'Membership Manual Payment Complete'}}">
                    </div>
                    <div class="form__input__single">
                        <label for="user_membership_manual_payment_complete_to_admin_message" class="form__input__single__label">{{__('User Membership Manual Payment Complete Message')}}</label>
                        <textarea class="form__control summernote" name="user_membership_manual_payment_complete_to_admin_message">{!! get_static_option('user_membership_manual_payment_complete_to_admin_message') ?? 'A manual membership payment status successfully changed from pending to complete. Membership ID: @membership_id'  !!} </textarea>
                    </div>
                    <div class="d-grid">
                        <small class="form-text"><strong class="text-danger"> @membership_id </strong>{{__('will be replaced by dynamically with membership_id.')}}</small>
                        <small class="form-text"><strong class="text-danger"> @membership_type </strong>{{__('will be replaced by dynamically with membership_type.')}}</small>
                        <small class="form-text"><strong class="text-danger"> @membership_price </strong>{{__('will be replaced by dynamically with membership_price.')}}</small>
                        <small class="form-text"><strong class="text-danger"> @membership_expire_date </strong>{{__('will be replaced by dynamically with membership_expire_date.')}}</small>
                        <small class="form-text"><strong class="text-danger"> @name </strong>{{__('will be replaced by dynamically with name.')}}</small>
                        <small class="form-text"><strong class="text-danger"> @email </strong>{{__('will be replaced by dynamically with email.')}}</small>
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <x-media.js />
    <x-summernote.js/>
@endsection
