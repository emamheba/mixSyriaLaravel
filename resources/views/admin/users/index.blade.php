@extends('layouts/layoutMaster')
@section('title', __('customer.Customers'))

@section('vendor-style')
@vite([
   'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js'
])
@endsection

@section('page-script')
@vite('resources/assets/js/customer-all.js')
@endsection

@section('content')
<div class="card">
  <div class="card-datatable table-responsive">
    <table class="datatables-customers table border-top">
      <thead>
        <tr>
          <th></th>
          <th>{{ __('Customer') }}</th>
          <th>{{ __('Email') }}</th>
          <th>{{ __('Phone') }}</th>
          <th>{{ __('Status') }}</th>
          <th class="text-nowrap">{{ __('Total Spent') }}</th>
          <th>{{ __('Orders') }}</th>
          <th>{{ __('Registered At') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
        <tr>
          <td></td>
          <td>
            <div class="d-flex justify-content-start align-items-center customer-name">
              <div class="avatar-wrapper">
                <div class="avatar avatar-sm me-3">
                  @php
                    $states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
                    $state = $states[array_rand($states)];
                    $initials = Illuminate\Support\Str::upper(Illuminate\Support\Str::substr($user->name, 0, 1));
                  @endphp
                  <span class="avatar-initial rounded-circle bg-label-{{ $state }}">{{ $initials }}</span>
                </div>
              </div>
              <div class="d-flex flex-column">
                <a href="{{ route('users.show', $user) }}" class="text-heading"><span class="fw-medium">{{ $user->name }}</span></a>
                <small>ID: {{ $user->id }}</small>
              </div>
            </div>
          </td>
          <td>{{ $user->email }}</td>
          <td>{{ $user->phone ?? __('N/A') }}</td>
          <td>
            <span class="badge bg-label-{{ $user->status ? 'success' : 'danger' }}">
              {{ $user->status ? __('Active') : __('Inactive') }}
            </span>
          </td>
          <td><span class="fw-medium text-heading">{{ $user->orders->sum('subtotal').' SAR' }}</span></td>
          <td>{{ $user->orders_count }}</td>
          <td>{{ $user->created_at->diffForHumans() }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEcommerceCustomerAdd" aria-labelledby="offcanvasEcommerceCustomerAddLabel">
    <div class="offcanvas-header">
      <h5 id="offcanvasEcommerceCustomerAddLabel" class="offcanvas-title">{{ __('Add Customer') }}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body border-top mx-0 flex-grow-0">
      <form class="ecommerce-customer-add pt-0" id="eCommerceCustomerAddForm" action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="ecommerce-customer-add-basic mb-4">
          <h6 class="mb-6">{{ __('Basic Information') }}</h6>
          <div class="mb-6">
            <label class="form-label" for="ecommerce-customer-add-name">{{ __('Name') }}*</label>
            <input type="text" class="form-control" id="ecommerce-customer-add-name" 
                   name="name" required aria-label="John Doe" />
          </div>
          <div class="mb-6">
            <label class="form-label" for="ecommerce-customer-add-email">{{ __('Email') }}*</label>
            <input type="email" id="ecommerce-customer-add-email" class="form-control" 
                   name="email" required aria-label="john.doe@example.com" />
          </div>
          <div class="mb-6">
            <label class="form-label" for="ecommerce-customer-add-phone">{{ __('Phone') }}</label>
            <input type="tel" id="ecommerce-customer-add-phone" class="form-control" 
                   name="phone" aria-label="+1234567890" />
          </div>
          <div class="mb-6">
            <label class="form-label" for="ecommerce-customer-add-password">{{ __('Password') }}*</label>
            <input type="password" id="ecommerce-customer-add-password" class="form-control" 
                   name="password" required />
          </div>
        </div>

        <div class="d-sm-flex mb-6">
          <div class="me-auto mb-2 mb-md-0">
            <h6 class="mb-1">{{ __('Account Status') }}</h6>
            <small class="text-muted">{{ __('Enable/disable user account') }}</small>
          </div>
          <div class="form-check form-switch my-auto me-n2">
            <input type="checkbox" class="form-check-input" name="status" checked />
          </div>
        </div>
        <div>
          <button type="submit" class="btn btn-primary me-sm-4">{{ __('Add Customer') }}</button>
          <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection