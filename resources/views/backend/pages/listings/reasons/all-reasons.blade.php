@extends('layouts/layoutMaster')

@section('title', __('All Report Reasons'))

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
  'resources/assets/js/app-ecommerce-order-list.js'
])
@endsection

@section('content')

<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ __('All Report Reasons') }}</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReasonModal">
      <i class="ti ti-plus"></i> {{ __('Add New Reason') }}
    </button>
  </div>
  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-6">
        <input type="text" id="search_string" class="form-control" placeholder="{{ __('Search Reason...') }}">
      </div>
      <div class="col-md-2">
        <button class="btn btn-secondary" id="searchBtn">{{ __('Search') }}</button>
      </div>
      <div class="col-md-4 text-end">
        <form id="bulkDeleteForm" action="{{ route('admin.report.reason.delete.bulk.action') }}" method="POST">
          @csrf
          <input type="hidden" name="ids[]" id="bulk_reason_ids">
          <button type="submit" class="btn btn-danger d-none" id="bulkDeleteBtn">{{ __('Bulk Delete') }}</button>
        </form>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-striped mb-0" id="reasonTable">
        <thead>
          <tr>
            <th><input type="checkbox" class="form-check-input" id="bulkSelectAll"></th>
            <th>{{ __('ID') }}</th>
            <th>{{ __('Reason Title') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody id="reasonTableBody">
          @foreach($all_reasons as $reason)
          <tr>
            <td>
              <input type="checkbox" class="form-check-input bulk-checkbox" value="{{ $reason->id }}">
            </td>
            <td>{{ $reason->id }}</td>
            <td>{{ $reason->title }}</td>
            <td>
              <button type="button"
                      class="btn btn-sm btn-warning edit_reason_btn"
                      data-bs-toggle="modal"
                      data-bs-target="#editReasonModal"
                      data-id="{{ $reason->id }}"
                      data-title="{{ $reason->title }}">
                {{ __('Edit') }}
              </button>
              <form action="{{ route('admin.report.reason.delete', $reason->id) }}"
                    method="POST"
                    class="d-inline-block"
                    onsubmit="return confirm('{{ __('Are you sure you want to delete this reason?') }}');">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger">
                  {{ __('Delete') }}
                </button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      {{ $all_reasons->links() }}
    </div>
  </div>
</div>

<div class="modal fade" id="addReasonModal" tabindex="-1" aria-labelledby="addReasonModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('admin.report.reason.all') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addReasonModalLabel">{{ __('Add New Reason') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"
                  aria-label="{{ __('Close') }}"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="reason_title" class="form-label">{{ __('Reason Title') }}</label>
            <input type="text" class="form-control" id="reason_title"
                   name="title" required maxlength="191">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">{{ __('Add Reason') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editReasonModal" tabindex="-1" aria-labelledby="editReasonModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('admin.report.reason.edit') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="editReasonModalLabel">{{ __('Edit Reason') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"
                  aria-label="{{ __('Close') }}"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="edit_reason_id" name="reason_id">
          <div class="mb-3">
            <label for="edit_reason_title" class="form-label">{{ __('Reason Title') }}</label>
            <input type="text" class="form-control" id="edit_reason_title"
                   name="title" required maxlength="191">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">{{ __('Update Reason') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('custom_js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const bulkSelectAll = document.querySelector('#bulkSelectAll');
  const checkboxes = document.querySelectorAll('.bulk-checkbox');
  const bulkDeleteForm = document.querySelector('#bulkDeleteForm');
  const bulkDeleteBtn = document.querySelector('#bulkDeleteBtn');
  const bulkReasonIds = document.querySelector('#bulk_reason_ids');

  if (bulkSelectAll) {
    bulkSelectAll.addEventListener('change', function () {
      checkboxes.forEach(chk => {
        chk.checked = bulkSelectAll.checked;
      });
      toggleBulkDeleteBtn();
    });
  }

  checkboxes.forEach(chk => {
    chk.addEventListener('change', function () {
      toggleBulkDeleteBtn();
    });
  });

  function toggleBulkDeleteBtn() {
    const anyChecked = [...checkboxes].some(c => c.checked);
    bulkDeleteBtn.classList.toggle('d-none', !anyChecked);
  }

  bulkDeleteForm.addEventListener('submit', function (e) {
    const selectedIds = [];
    checkboxes.forEach(chk => {
      if (chk.checked) {
        selectedIds.push(chk.value);
      }
    });
    bulkReasonIds.value = selectedIds;
  });

  const editBtns = document.querySelectorAll('.edit_reason_btn');
  editBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      const reasonId = this.getAttribute('data-id');
      const reasonTitle = this.getAttribute('data-title');
      document.querySelector('#edit_reason_id').value = reasonId;
      document.querySelector('#edit_reason_title').value = reasonTitle;
    });
  });

  const searchBtn = document.querySelector('#searchBtn');
  const searchString = document.querySelector('#search_string');
  const reasonTableBody = document.querySelector('#reasonTableBody');

  if (searchBtn && searchString) {
    searchBtn.addEventListener('click', function () {
      fetch(`{{ route('admin.report.reason.search') }}?string_search=` + searchString.value)
        .then(response => response.json())
        .then(data => {
          if (data.status === 'nothing') {
            reasonTableBody.innerHTML = '<tr><td colspan="4" class="text-center">{{ __('No reasons found!') }}</td></tr>';
          } else {
            reasonTableBody.innerHTML = data;
          }
        })
        .catch(error => console.error(error));
    });
  }

});
</script>
@endsection