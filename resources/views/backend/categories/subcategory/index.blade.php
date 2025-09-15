@extends('layouts/layoutMaster')

@section('title', __('Sub Category'))

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
    document.addEventListener('DOMContentLoaded', function() {
      // Toggle Status
      document.querySelectorAll('.toggle-status').forEach(input => {
        input.addEventListener('change', function() {
          const categoryId = this.dataset.id;
          const newStatus = this.checked ? 1 : 0;

          fetch(`/admin/subcategories/change-status/${categoryId}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: newStatus })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              this.closest('.text-truncate').querySelector('.status-value').textContent = newStatus;
              toastr.success('Status updated successfully!');
            } else {
              toastr.error('Failed to update status');
              this.checked = !this.checked;
            }
          })
          .catch(error => {
            console.error('Error:', error);
            toastr.error('An error occurred');
            this.checked = !this.checked;
          });
        });
      });
    });
</script>

<script>
  var createRoute = "{{ route('subcategories.create') }}";
</script>
@endsection

@section('content')
<div class="app-ecommerce-product">

<div class="card">
  <div class="card-datatable table-responsive">
    <table class="datatables-products table border-top">
      <thead>
        <tr>
          <th></th>
          <th>{{__('Name')}}</th>
          <th>{{__('Parent Category')}}</th>
          {{-- <th>{{__('Brand')}}</th> --}}
          <th>{{__('Status')}}</th>
          <th>{{__('Actions')}}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($subcategories as $subcategory)
          <tr>
            <td></td>
            <td>
              <div class="d-flex justify-content-start align-items-center product-name">
                <div class="avatar-wrapper">
                  <div class="avatar avatar me-4 rounded-2 bg-label-secondary">
                    @if ($subcategory->image)
                      <img src="{{ asset($subcategory->image) }}" alt="SubCategory-{{ $subcategory->id }}" class="rounded-2">
                    @else
                      <span class="avatar-initial rounded-2 bg-label-primary">{{ substr($subcategory->name, 0, 2) }}</span>
                    @endif
                  </div>
                </div>
                <div class="d-flex flex-column">
                  <h6 class="text-nowrap mb-0">{{ $subcategory->name }}</h6>
                  <small class="text-truncate d-none d-sm-block">{{ $subcategory->description }}</small>
                </div>
              </div>
            </td>
            <td>
              <span class="text-truncate d-flex align-items-center text-heading">
                @if ($subcategory?->category?->name == 'Electronics')
                  <span class="avatar-sm rounded-circle d-flex justify-content-center align-items-center bg-label-danger me-4 p-3"><i class="ti ti-device-mobile ti-sm"></i></span>
                @elseif ($subcategory?->category?->name == 'Fashion')
                  <span class="avatar-sm rounded-circle d-flex justify-content-center align-items-center bg-label-success me-4"><i class="ti ti-shirt ti-sm"></i></span>
                @endif

                @if ($subcategory?->category?->name !== null)
                    {{ $subcategory->category->name }}
                @else
                  {{ __('No Category') }}
                @endif
              </span>
            </td>
            {{-- <td>
              <span class="text-truncate">
                @if ($subcategory->brand)
                  {{ $subcategory->brand->title }}
                @else
                  {{ __('No Brand') }}
                @endif
              </span>
            </td> --}}
            <td>
                <span class="text-truncate">
                  <label class="switch switch-primary switch-sm">
                    <input
                      type="checkbox"
                      class="switch-input toggle-status"
                      data-id="{{ $subcategory->id }}"
                      {{ $subcategory->status == 1 ? 'checked' : '' }}
                    >
                    <span class="switch-toggle-slider">
                      <span class="switch-on"></span>
                      <span class="switch-off"></span>
                    </span>
                  </label>
                  <span class="d-none status-value">{{ $subcategory->status }}</span>
                </span>
              </td>
            <td>
              <div class="d-inline-block text-nowrap">
                <button class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light"
                        onclick="window.location='{{ route('subcategories.edit', $subcategory->id) }}'">
                  <i class="ti ti-edit"></i>
                </button>
                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect waves-light dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="ti ti-dots-vertical ti-md"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end m-0">
                  <a href="" class="dropdown-item">{{__('View')}}</a>
                  <form action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger">{{__('Delete')}}</button>
                  </form>
                </div>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
</div>

@endsection
