@extends('layouts/layoutMaster')

@section('title', __('Departments'))

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
    var createRoute = "{{ route('admin.department') }}";
</script>
@vite([
  'resources/assets/js/app-ecommerce-product-list.js'
])
<script>
    document.addEventListener('DOMContentLoaded', function() {
      // Toggle Status
      document.querySelectorAll('.toggle-status').forEach(input => {
        input.addEventListener('change', function() {
          const departmentId = this.dataset.id;
          const newStatus = this.checked ? 1 : 0;

          fetch(`/admin/support-ticket/department-change-status/${departmentId}`, {
            method: 'POST',
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

      // Bulk action
      document.getElementById('bulk-delete-btn').addEventListener('click', function() {
        const checkedIds = Array.from(document.querySelectorAll('.department-checkbox:checked'))
          .map(checkbox => checkbox.value);
        
        if (checkedIds.length === 0) {
          toastr.error('Please select at least one department');
          return;
        }

        if (confirm('Are you sure you want to delete the selected departments?')) {
          const form = document.getElementById('bulk-delete-form');
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'ids[]';
          input.value = checkedIds.join(',');
          form.appendChild(input);
          form.submit();
        }
      });
    });
</script>

@endsection

@section('content')
<div class="app-ecommerce-department">

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ __('Departments') }}</h5>
    <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
      {{ __('Add New Department') }}
    </button>
  </div>
  <div class="card-body">
    <div class="row mb-4">
      <div class="col">
        <button id="bulk-delete-btn" class="btn btn-danger btn-sm">{{ __('Delete Selected') }}</button>
        <form id="bulk-delete-form" action="{{ route('admin.department.delete.bulk.action') }}" method="POST" style="display: none;">
          @csrf
        </form>
      </div>
    </div>
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-departments table border-top">
      <thead>
        <tr>
          <th>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="select-all">
            </div>
          </th>
          <th>{{ __('ID') }}</th>
          <th>{{ __('Name') }}</th>
          <th>{{ __('Status') }}</th>
          <th>{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($departments as $department)
          <tr>
            <td>
              <div class="form-check">
                <input class="form-check-input department-checkbox" type="checkbox" value="{{ $department->id }}">
              </div>
            </td>
            <td>{{ $department->id }}</td>
            <td>
              <div class="d-flex justify-content-start align-items-center department-name">
                <div class="d-flex flex-column">
                  <h6 class="text-nowrap mb-0">{{ $department->name }}</h6>
                </div>
              </div>
            </td>
            <td>
              <span class="text-truncate">
                <label class="switch switch-primary switch-sm">
                  <input
                    type="checkbox"
                    class="switch-input toggle-status"
                    data-id="{{ $department->id }}"
                    {{ $department->status == 1 ? 'checked' : '' }}
                  >
                  <span class="switch-toggle-slider">
                    <span class="switch-on"></span>
                    <span class="switch-off"></span>
                  </span>
                </label>
                <span class="d-none status-value">{{ $department->status }}</span>
              </span>
            </td>
            <td>
              <div class="d-inline-block text-nowrap">
                <button class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light"
                        data-bs-toggle="modal" data-bs-target="#editDepartmentModal{{ $department->id }}">
                  <i class="ti ti-edit"></i>
                </button>
                <form action="{{ route('admin.department.delete', $department->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light" 
                  onclick="return confirm('{{ __('Are you sure you want to delete this department?') }}')">
                    <i class="ti ti-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>

          <!-- Edit Department Modal -->
          <div class="modal fade" id="editDepartmentModal{{ $department->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">{{ __('Edit Department') }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.department.edit') }}" method="POST">
                  @csrf
                  <div class="modal-body">
                    <input type="hidden" name="department_id" value="{{ $department->id }}">
                    <div class="mb-3">
                      <label class="form-label">{{ __('Department Name') }}</label>
                      <input type="text" class="form-control" name="edit_name" value="{{ $department->name }}" required>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
</div>

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Add Department') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.department') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">{{ __('Department Name') }}</label>
            <input type="text" class="form-control" name="name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('Status') }}</label>
            <select class="form-select" name="status">
              <option value="1">{{ __('Active') }}</option>
              <option value="0">{{ __('Inactive') }}</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
