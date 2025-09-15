@extends('layouts/layoutMaster')

@section('title', __('tags'))

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
<script>
    var createRoute = "{{ route('tags.create') }}";
  </script>
@vite([
  'resources/assets/js/app-ecommerce-product-list.js'
])
<script>
    document.addEventListener('DOMContentLoaded', function() {
      // Toggle Status
      document.querySelectorAll('.toggle-status').forEach(input => {
        input.addEventListener('change', function() {
          const tagId = this.dataset.id;
          const newStatus = this.checked ? 1 : 0;

          fetch(`/tags/change-status/${tagId}`, {
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
          <th></th>
          <th>{{ __('Status') }}</th>
          <th>{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($tags as $tag)
          <tr>
            <td></td>
            <td>
              <div class="d-flex justify-content-start align-items-center product-name">
                <div class="d-flex flex-column">
                  <h6 class="text-nowrap mb-0">{{ $tag->name }}</h6>
                </div>
              </div>
            </td>
            <td></td>
            <td>
              <span class="text-truncate">
                <label class="switch switch-primary switch-sm">
                  <input
                    type="checkbox"
                    class="switch-input toggle-status"
                    data-id="{{ $tag->id }}"
                    {{ $tag->status == 1 ? 'checked' : '' }}
                  >
                  <span class="switch-toggle-slider">
                    <span class="switch-on"></span>
                    <span class="switch-off"></span>
                  </span>
                </label>
                <span class="d-none status-value">{{ $tag->status }}</span>
              </span>
            </td>
            <td>
              <div class="d-inline-block text-nowrap">
                <button class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light"
                        onclick="window.location='{{ route('tags.edit', $tag->id) }}'">
                  <i class="ti ti-edit"></i>
                </button>
                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect waves-light dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="ti ti-dots-vertical ti-md"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end m-0">
                  <a href="" class="dropdown-item">{{ __('View') }}</a>
                  <form action="{{ route('tags.destroy', $tag->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger">{{ __('Delete') }}</button>
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
