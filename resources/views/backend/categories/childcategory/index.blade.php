@extends('layouts/layoutMaster')

@section('title', __('Child Categories'))

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

        fetch(`/admin/childcategories/change-status/${categoryId}`, {
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
  var createRoute = "{{ route('childcategories.create') }}";
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
          <th>{{ __('Name') }}</th>
          <th>{{ __('Category') }}</th>
          <th>{{ __('Sub Category') }}</th>
          {{-- <th>{{ __('Brand') }}</th> --}}
          <th>{{ __('Status') }}</th>
          <th>{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($child_categories as $child_category)
          <tr>
            <td></td>
           <td>
                <div class="d-flex justify-content-start align-items-center product-name">
                <div class="avatar-wrapper">
                  <div class="avatar avatar me-4 rounded-2 bg-label-secondary">
                    @if ($child_category->image_url)
                      <img src="{{ $child_category->image_url }}" alt="{{ $child_category->name }}" class="rounded-2">
                    @else
                      <span class="avatar-initial rounded-2 bg-label-primary">{{ substr($child_category->name, 0, 2) }}</span>
                    @endif
                  </div>
                </div>
                <div class="d-flex flex-column">
                  <h6 class="text-nowrap mb-0">{{ $child_category->name }}</h6>
                  <small class="text-truncate d-none d-sm-block">{{ $child_category->description }}</small>
                </div>
              </div>
            </td>
            <td>{{ $child_category?->category?->name }}</td>
            <td>{{ $child_category?->subcategory?->name }}</td>
            {{-- <td>
              <span class="text-truncate">
                @if ($child_category->brand)
                  {{ $child_category->brand->title }}
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
                    data-id="{{ $child_category->id }}"
                    {{ $child_category->status == 1 ? 'checked' : '' }}
                  >
                  <span class="switch-toggle-slider">
                    <span class="switch-on"></span>
                    <span class="switch-off"></span>
                  </span>
                </label>
                <span class="d-none status-value">{{ $child_category->status }}</span>
              </span>
            </td>
            <td>
              <div class="d-inline-block text-nowrap">
                <button class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light"
                        onclick="window.location='{{ route('childcategories.edit', $child_category->id) }}'">
                  <i class="ti ti-edit"></i>
                </button>
                <form action="{{ route('childcategories.destroy', $child_category->id) }}" method="POST" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-icon btn-text-danger rounded-pill waves-effect waves-light">
                    <i class="ti ti-trash"></i>
                  </button>
                </form>
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
