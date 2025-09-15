@extends('layouts/layoutMaster')

@section('title', 'Order Details')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/cleavejs/cleave.js',
  'resources/assets/vendor/libs/cleavejs/cleave-phone.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('page-script')
@vite([
  // 'resources/assets/js/app-ecommerce-order-details.js',
  // 'resources/assets/js/modal-add-new-address.js',
  // 'resources/assets/js/modal-edit-user.js'
])
@endsection

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
  <div class="d-flex flex-column justify-content-center">
    <div class="mb-1">
      <span class="h5">{{ __('Order') }} #{{ $order->id }}</span>
      <span class="badge bg-label-{{ $order->payment_status === 'paid' ? 'success' : 'danger' }} me-1 ms-2">
        {{ ucfirst($order->payment_status) }}
      </span>
      <span class="badge bg-label-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : ($order->status === 'refunded' ? 'info' : 'danger')) }}">
        {{ ucfirst($order->status) }}
      </span>
    </div>
    <p class="mb-0">{{ $order->created_at->format('M d, Y, h:i A') }}</p>
  </div>
  <div class="d-flex align-content-center flex-wrap gap-2">
    <button class="btn btn-label-danger delete-order">{{ __('Delete Order') }}</button>
  </div>
</div>

<!-- Order Details Table -->

<div class="row">
  <div class="col-12 col-lg-8">
    <div class="card mb-6">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0">{{ __('Order details') }}</h5>
        <h6 class="m-0"><a href="javascript:void(0)">{{ __('Edit') }}</a></h6>
      </div>
      <div class="card-datatable table-responsive">
        <table class="datatables-order-details table border-top">
          <thead>
            <tr>
              <th></th>
              <th class="w-50">{{ __('Products') }}</th>
              <th class="w-25">{{ __('Price') }}</th>
              <th class="w-25">{{ __('Qty') }}</th>
              <th>{{ __('Total') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($order->orderItems as $item)
              <tr>
                <td></td>
                <td>
                  <div class="d-flex justify-content-start align-items-center text-nowrap">
                    <div class="avatar-wrapper">
                      <div class="avatar avatar-sm me-3">
                        <img src="{{ asset($item->product->image) }}" alt="product-{{ $item->product->name }}" class="rounded-2">
                      </div>
                    </div>
                    <div class="d-flex flex-column">
                      <h6 class="text-heading mb-0">{{ $item->product->name }}</h6>
                      <small>{{ $item->product?->brand ?? "RAM"}}</small>
                    </div>
                  </div>
                </td>
                <td>${{ number_format($item->product_price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->total_price, 2) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <div class="d-flex justify-content-end align-items-center m-6 mb-2">
          <div class="order-calculations">
            <div class="d-flex justify-content-start mb-2">
              <span class="w-px-100 text-heading">{{ __('Subtotal') }}:</span>
              <h6 class="mb-0">${{ number_format($order->subtotal, 2) }}</h6>
            </div>
            <div class="d-flex justify-content-start mb-2">
              <span class="w-px-100 text-heading">{{ __('Discount') }}:</span>
              <h6 class="mb-0">${{ number_format($order->discount_amount, 2) }}</h6>
            </div>
            <div class="d-flex justify-content-start mb-2">
              <span class="w-px-100 text-heading">{{ __('Delivery') }}:</span>
              <h6 class="mb-0">${{ number_format($order->delivery_amount, 2) }}</h6>
            </div>
            <div class="d-flex justify-content-start">
              <h6 class="w-px-100 mb-0">{{ __('Total') }}:</h6>
              <h6 class="mb-0">${{ number_format($order->total_amount, 2) }}</h6>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-6">
      <div class="card-header">
        <h5 class="card-title m-0">{{ __('Shipping activity') }}</h5>
      </div>
      <div class="card-body pt-1">
        <ul class="timeline pb-0 mb-0">
          <li class="timeline-item timeline-item-transparent border-primary">
            <span class="timeline-point timeline-point-primary"></span>
            <div class="timeline-event">
              <div class="timeline-header">
                <h6 class="mb-0">{{ __('Order was placed (Order ID: #') }}{{ $order->id }})</h6>
                <small class="text-muted">{{ $order->created_at->format('l h:i A') }}</small>
              </div>
              <p class="mt-3">{{ __('Your order has been placed successfully') }}</p>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent border-primary">
            <span class="timeline-point timeline-point-primary"></span>
            <div class="timeline-event">
              <div class="timeline-header">
                <h6 class="mb-0">{{ __('Pick-up') }}</h6>
                <small class="text-muted">{{ __('Wednesday 11:29 AM') }}</small>
              </div>
              <p class="mt-3 mb-3">{{ __('Pick-up scheduled with courier') }}</p>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent border-left-dashed">
            <span class="timeline-point timeline-point-secondary"></span>
            <div class="timeline-event">
              <div class="timeline-header">
                <h6 class="mb-0">{{ __('Dispatched') }}</h6>
                <small class="text-muted">{{ __('Thursday 11:29 AM') }}</small>
              </div>
              <p class="mt-3 mb-3">{{ __('Item has been picked up by courier') }}</p>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent border-left-dashed">
            <span class="timeline-point timeline-point-secondary"></span>
            <div class="timeline-event">
              <div class="timeline-header">
                <h6 class="mb-0">{{ __('Package arrived') }}</h6>
                <small class="text-muted">{{ __('Saturday 15:20 AM') }}</small>
              </div>
              <p class="mt-3 mb-3">{{ __('Package arrived at an Amazon facility, NY') }}</p>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent border-left-dashed">
            <span class="timeline-point timeline-point-secondary"></span>
            <div class="timeline-event">
              <div class="timeline-header">
                <h6 class="mb-0">{{ __('Dispatched for delivery') }}</h6>
                <small class="text-muted">{{ __('Today 14:12 PM') }}</small>
              </div>
              <p class="mt-3 mb-3">{{ __('Package has left an Amazon facility, NY') }}</p>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent border-transparent pb-0">
            <span class="timeline-point timeline-point-secondary"></span>
            <div class="timeline-event pb-0">
              <div class="timeline-header">
                <h6 class="mb-0">{{ __('Delivery') }}</h6>
              </div>
              <p class="mt-1 mb-0">{{ __('Package will be delivered by tomorrow') }}</p>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
  

  <div class="col-12 col-lg-4">
    <!-- Customer Details Card -->
    <div class="card mb-6">
      <div class="card-header">
        <h5 class="card-title m-0">{{ __('Customer details') }}</h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-start align-items-center mb-6">
          <div class="avatar me-3">
            <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
          </div>
          <div class="d-flex flex-column">
            <a href="{{ url('app/user/view/account') }}" class="text-body text-nowrap">
              <h6 class="mb-0">{{ $order->user->name }}</h6>
            </a>
            <span>{{ __('Customer ID: #') }}{{ $order->user->id }}</span>
          </div>
        </div>
        <div class="d-flex justify-content-start align-items-center mb-6">
          <span class="avatar rounded-circle bg-label-success me-3 d-flex align-items-center justify-content-center">
            <i class='ti ti-shopping-cart ti-lg'></i>
          </span>
          <h6 class="text-nowrap mb-0">{{ $order->user->orders->count() }} {{ __('Orders') }}</h6>
        </div>
        <div class="d-flex justify-content-between">
          <h6 class="mb-1">{{ __('Contact info') }}</h6>
          <h6 class="mb-1"><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#editUser">{{ __('Edit') }}</a></h6>
        </div>
        <p class="mb-1">{{ __('Email:') }} {{ $order->user->email }}</p>
        <p class="mb-0">{{ __('Mobile:') }} {{ $order->user->phone ?? 'N/A' }}</p>
      </div>
    </div>

    <!-- Shipping Address Card -->
    <div class="card mb-6">
      <div class="card-header d-flex justify-content-between">
        <h5 class="card-title m-0">{{ __('Shipping address') }}</h5>
        <h6 class="m-0"><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#addNewAddress">{{ __('Edit') }}</a></h6>
      </div>
      <div class="card-body">
        <p class="mb-0">{{ $order->shipping_address }}</p>
      </div>
    </div>

    <!-- Billing Address Card -->
    <div class="card mb-6">
      <div class="card-header d-flex justify-content-between">
        <h5 class="card-title m-0">{{ __('Billing address') }}</h5>
        <h6 class="m-0"><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#addNewAddress">{{ __('Edit') }}</a></h6>
      </div>
      <div class="card-body">
        <p class="mb-6">{{ $order->shipping_address }}</p>
        <h5 class="mb-1">{{ $order->payment_method }}</h5>
        <p class="mb-0">{{ __('Card Number:') }} ******{{ substr($order->payment_method, -4) }}</p>
      </div>
    </div>
  </div>


</div>

<!-- Modals -->
@include('_partials/_modals/modal-edit-user')
@include('_partials/_modals/modal-add-new-address')

@endsection
