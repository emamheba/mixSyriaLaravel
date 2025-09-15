@extends('backend.admin-master')
@section('site-title')
    {{ __('Pusher Settings') }}
@endsection
@section('style')
    <x-media.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Pusher Settings')}}</h2>
                <x-validation.error/>
                <x-notice.general-notice :class="'mt-5'" :description="__('Notice: To activate live chat you must setup your pusher credentials.')" />
                <form action="{{route('admin.pusher.settings')}}" method="post">
                    @csrf
                    <x-form.text :title="__('Pusher App ID')" :name="'PUSHER_APP_ID'" :value="env('PUSHER_APP_ID') ?? '' " :placeholder="__('Pusher App ID')"/>
                    <x-form.text :title="__('Pusher App Key')" :name="'PUSHER_APP_KEY'" :value="env('PUSHER_APP_KEY') ?? '' " :placeholder="__('Pusher App Key')"/>
                    <x-form.text :title="__('Pusher App Secret')" :name="'PUSHER_APP_SECRET'" :value="env('PUSHER_APP_SECRET') ?? '' " :placeholder="__('Pusher App Secret')"/>
                    <x-form.text :title="__('Pusher App Cluster')" :name="'PUSHER_APP_CLUSTER'" :value="env('PUSHER_APP_CLUSTER') ?? '' " :placeholder="__('Pusher App Cluster')"/>
                    <div class="btn_wrapper mt-2 mx-2 my-2">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-media.markup/>
@endsection
