@extends('layouts/layoutMaster')

@section('title', __('Order List'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'
])
@endsection

@section('page-script')
@vite([
  // 'resources/assets/js/app-ecommerce-order-list.js'
])

<script>
$('#shareProject').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget); // Button that triggered the modal
    const orderId = button.data('order-id'); // Extract info from data-* attributes
    $(this).find('input[name="order_id"]').val(orderId);
});
</script>
@endsection

@section('content')
<!-- Order List Widget -->

<div class="card mb-6">
  <div class="card-widget-separator-wrapper">
    <div class="card-body card-widget-separator">
      <div class="row gy-4 gy-sm-1">
        <!-- Pending Payment -->
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $pendingPaymentCount }}</h4>
              <p class="mb-0">{{ __('Pending Payment') }}</p>
            </div>
            <span class="avatar me-sm-6">
              <span class="avatar-initial bg-label-secondary rounded text-heading">
                <i class="ti-26px ti ti-calendar-stats text-heading"></i>
              </span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none me-6">
        </div>

        <!-- Completed Orders -->
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $completedOrdersCount }}</h4>
              <p class="mb-0">{{ __('Completed') }}</p>
            </div>
            <span class="avatar p-2 me-lg-6">
              <span class="avatar-initial bg-label-secondary rounded"><i class="ti-26px ti ti-checks text-heading"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none">
        </div>

        <!-- Refunded Orders -->
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start border-end pb-4 pb-sm-0 card-widget-3">
            <div>
              <h4 class="mb-0">{{ $refundedOrdersCount }}</h4>
              <p class="mb-0">{{ __('Refunded') }}</p>
            </div>
            <span class="avatar p-2 me-sm-6">
              <span class="avatar-initial bg-label-secondary rounded"><i class="ti-26px ti ti-wallet text-heading"></i></span>
            </span>
          </div>
        </div>

        <!-- Failed Orders -->
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h4 class="mb-0">{{ $failedOrdersCount }}</h4>
              <p class="mb-0">{{ __('Failed') }}</p>
            </div>
            <span class="avatar p-2">
              <span class="avatar-initial bg-label-secondary rounded"><i class="ti-26px ti ti-alert-octagon text-heading"></i></span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Order List Table -->
<div class="card">
  <div class="card-datatable table-responsive">
    <table class="datatables-order table border-top">
      <thead>
        <tr>
          <th></th>
          <th>{{ __('Order ID') }}</th>
          <th>{{ __('Date') }}</th>
          <th>{{ __('Customer') }}</th>
          <th>{{ __('Subtotal') }}</th>
          <th>{{ __('Discount') }}</th>
          <th>{{ __('Coupon') }}</th>
          <th>{{ __('Delivery') }}</th>
          <th>{{ __('Total Amount') }}</th>
          <th>{{ __('Payment Method') }}</th>
          <th>{{ __('Payment Status') }}</th>
          <th>{{ __('Status') }}</th>
          <th>{{ __('Driver Appointed') }}</th>
          <th>{{ __('Driver ID') }}</th>
          <th>{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($orders as $order)
          <tr>
            <td></td>
            <td>{{ $order->id }}</td>
            <td>{{ $order->created_at->format('d M Y') }}</td>
            <td>{{ $order->user->name }}</td>
            <td>${{ number_format($order->subtotal, 2) }}</td>
            <td>${{ number_format($order->discount_amount, 2) }}</td>
            <td>{{ $order->coupon ? $order->coupon->code : 'N/A' }}</td>
            <td>${{ number_format($order->delivery_amount, 2) }}</td>
            <td>${{ number_format($order->total_amount, 2) }}</td>
            <td>{{ $order->payment_method }}</td>
            <td>
              @if ($order->payment_status === 'paid')
                <span class="badge bg-label-success">{{ __('Paid') }}</span>
              @else
                <span class="badge bg-label-danger">{{ __('Pending') }}</span>
              @endif
            </td>
            <td>
              @if ($order->status === 'completed')
                <span class="badge bg-label-success">{{ __('Completed') }}</span>
              @elseif ($order->status === 'pending')
                <span class="badge bg-label-warning">{{ __('Pending') }}</span>
              @elseif ($order->status === 'refunded')
                <span class="badge bg-label-info">{{ __('Refunded') }}</span>
              @elseif ($order->status === 'failed')
                <span class="badge bg-label-danger">{{ __('Failed') }}</span>
              @endif
            </td>
            <td>
              @if ($order->driver_appointed)
                <span class="badge bg-label-success">{{ __('Yes') }}</span>
              @else
                <span class="badge bg-label-danger">{{ __('No') }}</span>
              @endif
            </td>
            <td>{{ $order->driver_id ?? 'N/A' }}</td>
            <td>
              <div class="d-inline-block text-nowrap">
                <button class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#shareProject" data-order-id="{{ $order->id }}">
                  <i class="ti ti-edit" onclick=""></i>
                </button>
                <a href="{{ route('orders.show', $order->id)}}" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill"><i class="ti ti-eye ti-md"></i></a>
                {{-- <a href="{{ route('orders.appointDriver.form')}}" class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light"><i class="ti ti-edit"></i></a> --}}
                {{-- <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect waves-light dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="ti ti-dots-vertical ti-md"></i>
                </button> --}}
                {{-- <div class="dropdown-menu dropdown-menu-end m-0">
                  <a href="{{ route('orders.show', $order->id)}}" class="dropdown-item">{{ __('View') }}</a>
                  <a href="javascript:0;" class="dropdown-item">{{ __('Cancel') }}</a>
                </div> --}}
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@include('admin/orders/appoint-driver')

@endsection


