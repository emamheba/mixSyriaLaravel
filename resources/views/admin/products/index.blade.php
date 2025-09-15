@extends('layouts/layoutMaster')

@section('title', 'eCommerce Product List - Apps')

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
@vite([
  'resources/assets/js/app-ecommerce-product-list.js'
])

<script>
  var createProductRoute = "{{ route('products.create') }}";
</script>
@endsection

@section('content')

<div class="card mb-6">
  <div class="card-widget-separator-wrapper">
    <div class="card-body card-widget-separator">
      <div class="row gy-4 gy-sm-1">
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
            <div>
              <p class="mb-1">In-store Sales</p>
              <h4 class="mb-1">$5,345.43</h4>
              <p class="mb-0"><span class="me-2">5k orders</span><span class="badge bg-label-success">+5.7%</span></p>
            </div>
            <span class="avatar me-sm-6">
              <span class="avatar-initial rounded"><i class="ti-28px ti ti-smart-home text-heading"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none me-6">
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
            <div>
              <p class="mb-1">Website Sales</p>
              <h4 class="mb-1">$674,347.12</h4>
              <p class="mb-0"><span class="me-2">21k orders</span><span class="badge bg-label-success">+12.4%</span></p>
            </div>
            <span class="avatar p-2 me-lg-6">
              <span class="avatar-initial rounded"><i class="ti-28px ti ti-device-laptop text-heading"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none">
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start border-end pb-4 pb-sm-0 card-widget-3">
            <div>
              <p class="mb-1">Discount</p>
              <h4 class="mb-1">$14,235.12</h4>
              <p class="mb-0">6k orders</p>
            </div>
            <span class="avatar p-2 me-sm-6">
              <span class="avatar-initial rounded"><i class="ti-28px ti ti-gift text-heading"></i></span>
            </span>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="mb-1">Affiliate</p>
              <h4 class="mb-1">$8,345.23</h4>
              <p class="mb-0"><span class="me-2">150 orders</span><span class="badge bg-label-danger">-3.5%</span></p>
            </div>
            <span class="avatar p-2">
              <span class="avatar-initial rounded"><i class="ti-28px ti ti-wallet text-heading"></i></span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5 class="card-title">Filter</h5>
    <div class="d-flex justify-content-between align-items-center row pt-4 gap-6 gap-md-0">
      <div class="col-md-4 product_status"></div>
      <div class="col-md-4 product_category"></div>
      <div class="col-md-4 product_stock"></div>
    </div>
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-products table border-top">
      <thead>
        <tr>
          <th></th>
          <th>Product</th>
          <th>Category</th>
          <th>Stock</th>
          <th>Price</th>
          <th>Qty</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($products as $product)
          <tr>
            <td></td>
            <td>
              <div class="d-flex justify-content-start align-items-center product-name">
                <div class="avatar-wrapper">
                  <div class="avatar avatar me-4 rounded-2 bg-label-secondary">
                    @if ($product->image)
                      <img src="{{ $product->image }}" alt="Product-{{ $product->id }}" class="rounded-2">
                    @else
                      <span class="avatar-initial rounded-2 bg-label-primary">{{ substr($product->name, 0, 2) }}</span>
                    @endif
                  </div>
                </div>
                <div class="d-flex flex-column">
                  <h6 class="text-nowrap mb-0">{{ $product->name }}</h6>
                  <small class="text-truncate d-none d-sm-block">{{ $product->brand }}</small>
                </div>
              </div>
            </td>
            <td>
              <span class="text-truncate d-flex align-items-center text-heading">
                @if ($product->subCategory->name == 'Household')
                  <span class="avatar-sm rounded-circle d-flex justify-content-center align-items-center bg-label-warning me-4 p-3"><i class="ti ti-home-2 ti-sm"></i></span>
                @elseif ($product->subCategory->name == 'Office')
                  <span class="avatar-sm rounded-circle d-flex justify-content-center align-items-center bg-label-info me-4 p-3"><i class="ti ti-briefcase ti-sm"></i></span>
                @elseif ($product->subCategory->name == 'Electronics')
                  <span class="avatar-sm rounded-circle d-flex justify-content-center align-items-center bg-label-danger me-4 p-3"><i class="ti ti-device-mobile ti-sm"></i></span>
                @elseif ($product->subCategory->name == 'Shoes')
                  <span class="avatar-sm rounded-circle d-flex justify-content-center align-items-center bg-label-success me-4"><i class="ti ti-shoe ti-sm"></i></span>
                @elseif ($product->subCategory->name == 'Accessories')
                  <span class="avatar-sm rounded-circle d-flex justify-content-center align-items-center bg-label-secondary me-4"><i class="ti ti-device-watch ti-sm"></i></span>
                @elseif ($product->subCategory->name == 'Game')
                  <span class="avatar-sm rounded-circle d-flex justify-content-center align-items-center bg-label-primary me-4"><i class="ti ti-device-gamepad-2 ti-sm"></i></span>
                @endif
                {{ $product->subCategory->name }}
              </span>
            </td>
            <td>
              <span class="text-truncate">
                @if ($product->stock == 1)
                  <label class="switch switch-primary switch-sm">
                    <input type="checkbox" class="switch-input" checked>
                    <span class="switch-toggle-slider">
                      <span class="switch-on"></span>
                    </span>
                  </label>
                @else
                  <label class="switch switch-primary switch-sm">
                    <input type="checkbox" class="switch-input">
                    <span class="switch-toggle-slider">
                      <span class="switch-off"></span>
                    </span>
                  </label>
                @endif
                <span class="d-none">{{ $product->stock }}</span>
              </span>
            </td>
            <td>{{ $product->price }}</td>
            <td>{{ $product->quantity }}</td>
            <td>
              @if ($product->status == 1)
                <span class="badge bg-label-success">Active</span>
              @elseif ($product->status == 0)
                <span class="badge bg-label-danger">Inactive</span>
              @endif
            </td>
            <td>
              <div class="d-inline-block text-nowrap">
                <button class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light"><i class="ti ti-edit" onclick="window.location='{{ route('products.edit', $product->id) }}'"></i></button>
                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect waves-light dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></button>
                <div class="dropdown-menu dropdown-menu-end m-0">
                  <a href="javascript:0;" class="dropdown-item">View</a>
                  <a href="javascript:0;" class="dropdown-item">Suspend</a>
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