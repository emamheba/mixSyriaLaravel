@extends('layouts/layoutMaster')

@section('title', 'Membership Settings - Pages')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
    @include('membership::backend.settings.membership-settings-js')
@endsection

@section('content')

<div class="card">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Membership Settings</h5>
    </div>
    
    <div class="card-body">
        <x-validation.error/>
        
        <form action="{{ route('admin.membership.settings') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="ti ti-info-circle me-2"></i>
                        <div>
                            {{ __('Notice: When a new user registers, by default they will get the free membership. Once it is complete or expired, they must buy a membership for listing ads.') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-semibold" for="register_membership">{{ __('Make Free Membership') }}</label>
                        <select name="register_membership" id="register_membership" class="form-select">
                            <option value="">{{ __('Select membership') }}</option>
                            @foreach($memberships as $sub)
                            <option value="{{ $sub->id }}" {{ get_static_option('register_membership') == $sub->id ? 'selected' : '' }}>
                                {{ $sub?->membership_type?->type ?? '' }} - {{ $sub->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-semibold" for="package_expire_notify_mail_days">{{ __('Expiration Mail Alert Days') }}</label>
                        <select name="package_expire_notify_mail_days[]" id="package_expire_notify_mail_days" class="select2 form-select" multiple>
                            @php
                                $fileds = [1 =>'One Day', 2 => 'Two Days', 3 => 'Three Days', 4 => 'Four Days', 5 => 'Five Days', 6 => 'Six Days', 7=> 'Seven Days'];
                                $package_expire_notify_mail_days = get_static_option('package_expire_notify_mail_days');
                                $decoded = json_decode($package_expire_notify_mail_days) ?? [];
                            @endphp
                            
                            @foreach($fileds as $key => $field)
                                <option value="{{$key}}" 
                                    @foreach($decoded as $day)
                                        {{$day == $key ? 'selected' : ''}}
                                    @endforeach
                                >
                                    {{__($field)}}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">{{ __('Select how many days earlier expiration mail alerts will be sent') }}</small>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-semibold" for="membership_get_started_button_title">{{ __('Membership Buy Button Title') }}</label>
                        <input type="text" class="form-control" id="membership_get_started_button_title" name="membership_get_started_button_title" value="{{get_static_option('membership_get_started_button_title')}}"/>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-semibold" for="membership_upgrade_button_title">{{ __('Membership Upgrade Button Title') }}</label>
                        <input type="text" class="form-control" id="membership_upgrade_button_title" name="membership_upgrade_button_title" value="{{get_static_option('membership_upgrade_button_title')}}"/>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-semibold" for="membership_renew_button_title">{{ __('Membership Renew Button Title') }}</label>
                        <input type="text" class="form-control" id="membership_renew_button_title" name="membership_renew_button_title" value="{{get_static_option('membership_renew_button_title')}}"/>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-semibold" for="membership_renew_modal_title">{{ __('Membership Renew Modal Title') }}</label>
                        <input type="text" class="form-control" id="membership_renew_modal_title" name="membership_renew_modal_title" value="{{get_static_option('membership_renew_modal_title')}}"/>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-semibold" for="current_membership_button_title">{{ __('Current Membership Button Title') }}</label>
                        <input type="text" class="form-control" id="current_membership_button_title" name="current_membership_button_title" value="{{get_static_option('current_membership_button_title')}}"/>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-semibold" for="current_membership_modal_title">{{ __('Current Membership Modal Title') }}</label>
                        <input type="text" class="form-control" id="current_membership_modal_title" name="current_membership_modal_title" value="{{get_static_option('current_membership_modal_title')}}"/>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-semibold" for="renew_button_before_expire_days">{{ __('Renew Button Display Days') }}</label>
                        <select name="renew_button_before_expire_days" id="renew_button_before_expire_days" class="form-select">
                            <option value="" disabled>{{ __('Select day') }}</option>
                            @for ($i = 1; $i <= 30; $i++)
                            <option value="{{ $i }}" @if(get_static_option('renew_button_before_expire_days') == $i) selected @endif>
                                {{ $i }} 
                                @if($i == 1)
                                    {{ __('day_singular') }}
                                @elseif($i == 2)
                                    {{ __('day_dual') }}
                                @elseif($i >= 3 && $i <= 10)
                                    {{ __('days_plural') }}
                                @else
                                    {{ __('day_singular') }}
                                @endif
                            </option>
                        @endfor
                        
                        </select>
                        <small class="text-muted">{{ __('Select the day to display the renew button before membership expires') }}</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> {{ __('Save Changes') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
