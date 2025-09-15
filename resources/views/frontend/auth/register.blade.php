@php
$customizerHidden = 'customizer-hide';
$configData = appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', 'Register')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
  'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('page-style')
@vite([
  'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/cleavejs/cleave.js',
  'resources/assets/vendor/libs/cleavejs/cleave-phone.js',
  'resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('page-script')
@vite([
  'resources/assets/js/pages-auth-multisteps.js'
])
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover authentication-bg">
  <!-- Logo -->
  <a href="{{url('/')}}" class="app-brand auth-cover-brand">
    <span class="app-brand-logo demo">@include('_partials.macros',['height'=>20,'withbg' => "fill: #fff;"])</span>
    <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
  </a>
  <!-- /Logo -->
  <div class="authentication-inner row">

    <!-- Left Text -->
    <div class="d-none d-lg-flex col-lg-4 align-items-center justify-content-center p-5 auth-cover-bg-color position-relative auth-multisteps-bg-height">
      <img src="{{ asset('assets/img/illustrations/auth-register-multisteps-illustration.png') }}" alt="auth-register-multisteps" class="img-fluid" width="280">

      <img src="{{ asset('assets/img/illustrations/auth-register-multisteps-shape-'.$configData['style'].'.png') }}" alt="auth-register-multisteps" class="platform-bg" data-app-light-img="illustrations/auth-register-multisteps-shape-light.png" data-app-dark-img="illustrations/auth-register-multisteps-shape-dark.png">
    </div>
    <!-- /Left Text -->

    <!--  Multi Steps Registration -->
    <div class="d-flex col-lg-8 align-items-center justify-content-center authentication-bg p-5">
      <div class="w-px-700">
        <div id="multiStepsValidation" class="bs-stepper border-none shadow-none mt-5">
          <div class="bs-stepper-header border-none pt-12 px-0">
            <div class="step" data-target="#accountDetailsValidation">
              <button type="button" class="step-trigger">
                {{-- <span class="bs-stepper-circle"><i class="ti ti-file-analytics ti-md"></i></span>
                <span class="bs-stepper-label">
                  <span class="bs-stepper-title">Account</span>
                  <span class="bs-stepper-subtitle">Account Details</span>
                </span> --}}
              </button>
            </div>
          </div>
          <div class="bs-stepper-content px-0">
            <div class="app-brand justify-content-center mb-6">
                <a href="" class="app-brand-link">
                  {{-- <span class="app-brand-logo demo">@include('_partials.macros',['height'=>20,'withbg' => "fill: #fff;"])</span> --}}
                  <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
                </a>
              </div>
              <!-- /Logo -->
              <h4 class="mb-1">Adventure starts here 🚀</h4>
              <p class="mb-6">Make your app management easy and fun!</p>

            <form id="multiStepsForm" action="{{ route('user.register') }}" method="POST"> @csrf
              <!-- Account Details -->
              <div id="accountDetailsValidation" class="content">


                <div class="row g-6">
                    <div class="col-sm-6">
                        <label class="form-label" for="multiStepsFirstName">First Name</label>
                        <input type="text" id="multiStepsFirstName" name="first_name" class="form-control" placeholder="John" />
                      </div>
                      <div class="col-sm-6">
                        <label class="form-label" for="multiStepsLastName">Last Name</label>
                        <input type="text" id="multiStepsLastName" name="last_name" class="form-control" placeholder="Doe" />
                      </div>
                      <div class="col-sm-6">
                        <label class="form-label" for="multiStepsMobile">Mobile</label>
                        <div class="input-group">
                          <span class="input-group-text">US (+1)</span>
                          <input type="text" id="multiStepsMobile" name="phone" class="form-control multi-steps-mobile" placeholder="202 555 0111" />
                        </div>
                      </div>
                  <div class="col-sm-6">
                    <label class="form-label" for="multiStepsUsername">Username</label>
                    <input type="text" name="username" id="multiStepsUsername" class="form-control" placeholder="johndoe" />
                  </div>
                  <div class="col-sm-12">
                    <label class="form-label" for="multiStepsEmail">Email</label>
                    <input type="email" name="email" id="multiStepsEmail" class="form-control" placeholder="john.doe@email.com" aria-label="john.doe" />
                  </div>
                  <div class="col-sm-6 form-password-toggle">
                    <label class="form-label" for="multiStepsPass">Password</label>
                    <div class="input-group input-group-merge">
                      <input type="password" id="multiStepsPass" name="password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="multiStepsPass2" />
                      <span class="input-group-text cursor-pointer" id="multiStepsPass2"><i class="ti ti-eye-off"></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6 form-password-toggle">
                    <label class="form-label" for="multiStepsConfirmPass">Confirm Password</label>
                    <div class="input-group input-group-merge">
                      <input type="password" id="multiStepsConfirmPass" name="confirm_password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="multiStepsConfirmPass2" />
                      <span class="input-group-text cursor-pointer" id="multiStepsConfirmPass2"><i class="ti ti-eye-off"></i></span>
                    </div>
                  </div>


                  <div class="col-12 d-flex justify-content-between">
                    <div class="">
                        <p class="text-center">
                            <span>Already have an account?</span>
                            <a href="{{route('user.login')}}">
                                <span class="align-middle d-sm-inline-block d-none">Sign in instead</span>

                            </a>
                          </p>
                    </div>
                    <button type="submit" class="btn btn-success btn-next btn-submit">Submit</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


@endsection
