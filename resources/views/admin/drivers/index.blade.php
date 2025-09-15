@extends('layouts/layoutMaster')

@section('title', 'Drivers List - Apps')

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
,
@section('page-script')
@vite([
  // 'resources/assets/js/driver-list.js',
//   'resources/assets/js/app-ecommerce-product-list.js'
])

<script>
  var createDriverRoute = "{{ route('drivers.create') }}";
</script>
@endsection

@section('content')

<!-- Statistics Section -->
<div class="card mb-6">
  <div class="card-widget-separator-wrapper">
    <div class="card-body card-widget-separator">
      <div class="row gy-4 gy-sm-1">
        <!-- Total Drivers -->
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
            <div>
              <p class="mb-1">{{ __('Total Drivers') }}</p>
              <h4 class="mb-1">{{ $totalDrivers }}</h4>
              <p class="mb-0"><span class="me-2">{{ __('Active Drivers') }}: {{ $activeDrivers }}</span></p>
            </div>
            <span class="avatar me-sm-6">
              <span class="avatar-initial rounded"><i class="ti-28px ti ti-user text-heading"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none me-6">
        </div>

        <!-- Drivers with Orders -->
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
            <div>
              <p class="mb-1">{{ __('Drivers with Orders') }}</p>
              <h4 class="mb-1">{{ $driversWithOrders }}</h4>
              <p class="mb-0"><span class="me-2">{{ __('Inactive Drivers') }}: {{ $inactiveDrivers }}</span></p>
            </div>
            <span class="avatar p-2 me-lg-6">
              <span class="avatar-initial rounded"><i class="ti-28px ti ti-truck text-heading"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none">
        </div>

        <!-- Total Salary -->
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start border-end pb-4 pb-sm-0 card-widget-3">
            <div>
              <p class="mb-1">{{ __('Total Salary') }}</p>
              <h4 class="mb-1">${{ $totalSalary ?? 2323 }}</h4>
              <p class="mb-0">{{ __('Average Salary') }}: ${{ $averageSalary ?? 2323}}</p>
            </div>
            <span class="avatar p-2 me-sm-6">
              <span class="avatar-initial rounded"><i class="ti-28px ti ti-wallet text-heading"></i></span>
            </span>
          </div>
        </div>

        <!-- New Drivers -->
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="mb-1">{{ __('New Drivers') }}</p>
              <h4 class="mb-1">{{ $newDrivers }}</h4>
              <p class="mb-0"><span class="me-2">{{ __('Last 7 days') }}</span></p>
            </div>
            <span class="avatar p-2">
              <span class="avatar-initial rounded"><i class="ti-28px ti ti-user-plus text-heading"></i></span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Drivers Table -->
<div class="card">
    <div class="card-header">
    <h5 class="card-title">Filter</h5>
    <div class="d-flex justify-content-between align-items-center row pt-4 gap-6 gap-md-0">
        <div class="col-md-4 product_status"></div>
        <div class="col-md-4 product_category"></div>
        <div class="col-md-4 product_stock"></div>
    </div>
    </div>
  {{-- <div class="card-header">
    <h5 class="card-title">{{ __('Filter') }}</h5>
    <div class="d-flex justify-content-between align-items-center row pt-4 gap-6 gap-md-0">
      <!-- Status Filter -->
      <div class="col-md-4 driver_status">
        <select class="form-select" id="driverStatusFilter">
          <option value="">{{ __('Select Status') }}</option>
          <option value="active">{{ __('Active') }}</option>
          <option value="inactive">{{ __('Inactive') }}</option>
        </select>
      </div>

      <!-- Region Filter -->
      <div class="col-md-4 driver_region">
        <select class="form-select" id="driverRegionFilter">
          <option value="">{{ __('Select Region') }}</option>
          @foreach ($regions as $region)
            <option value="{{ $region->id }}">{{ $region->name }}</option>
          @endforeach
        </select>
      </div>

      <!-- Has Order Filter -->
      <div class="col-md-4 driver_has_order">
        <select class="form-select" id="driverHasOrderFilter">
          <option value="">{{ __('Has Order') }}</option>
          <option value="1">{{ __('Yes') }}</option>
          <option value="0">{{ __('No') }}</option>
        </select>
      </div>
    </div>
  </div> --}}

  <!-- Table -->
  <div class="card-datatable table-responsive">
    <table class="datatables-products table border-top">
      <thead>
        <tr>
          <th></th>
          <th>{{ __('Driver') }}</th>
          <th>{{ __('Phone') }}</th>
          <th>{{ __('Salary') }}</th>
          <th>{{ __('Eegion') }}</th>
          <th>{{ __('Has Order') }}</th>
          <th>{{ __('Status') }}</th>
          <th>{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($drivers as $driver)
          <tr>
            <td></td>
            <td>
              <div class="d-flex justify-content-start align-items-center product-name">
                <div class="avatar-wrapper">
                  <div class="avatar avatar me-4 rounded-2 bg-label-secondary">
                    @if ($driver->image)
                      <img src="{{ $driver->image }}" alt="Driver-{{ $driver->id }}" class="rounded-2">
                    @else
                      <span class="avatar-initial rounded-2 bg-label-primary">{{ substr($driver->name, 0, 2) }}</span>
                    @endif
                  </div>
                </div>
                <div class="d-flex flex-column">
                  <h6 class="text-nowrap mb-0">{{ $driver->name }}</h6>
                  <small class="text-truncate d-none d-sm-block">{{ $driver->identity_number }}</small>
                </div>
              </div>
            </td>
            <td>{{ $driver->phone }}</td>
            <td>${{ $driver->salary }}</td>
            <td>{{ $driver->region->name ?? 'N/A' }}</td>
            <td>
              @if ($driver->has_order)
                <span class="badge bg-label-success">{{ __('Yes') }}</span>
              @else
                <span class="badge bg-label-danger">{{ __('No') }}</span>
              @endif
            </td>
            <td>
              @if ($driver->status == 'active')
                <span class="badge bg-label-success">{{ __('Active') }}</span>
              @else
                <span class="badge bg-label-danger">{{ __('Inactive') }}</span>
              @endif
            </td>
            <td>
              <div class="d-inline-block text-nowrap">
                <button class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light">
                  <i class="ti ti-edit" onclick="window.location='{{ route('drivers.edit', $driver->id) }}'"></i>
                </button>
                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect waves-light dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="ti ti-dots-vertical ti-md"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end m-0">
                  <a href="javascript:0;" class="dropdown-item">{{ __('view') }}</a>
                  <a href="javascript:0;" class="dropdown-item">{{ __('suspend') }}</a>
                </div>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection