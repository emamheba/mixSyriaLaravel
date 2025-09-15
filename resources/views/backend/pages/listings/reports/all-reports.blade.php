@extends('layouts/layoutMaster')

@section('title', __('Listing Reports'))

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

@section('content')
<!-- Reports List Widget -->
<div class="card mb-6">
  <div class="card-widget-separator-wrapper">
    <div class="card-body card-widget-separator">
      <div class="row gy-4 gy-sm-1">
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $all_reports->total() }}</h4>
              <p class="mb-0">{{ __('Total Reports') }}</p>
            </div>
            <span class="avatar me-sm-6">
              <span class="avatar-initial bg-label-secondary rounded text-heading">
                <i class="ti-26px ti ti-flag text-heading"></i>
              </span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none me-6">
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $all_reports->where('status', 1)->count() }}</h4>
              <p class="mb-0">{{ __('Pending') }}</p>
            </div>
            <span class="avatar p-2 me-lg-6">
              <span class="avatar-initial bg-label-secondary rounded">
                <i class="ti-26px ti ti-clock text-heading"></i>
              </span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none">
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start border-end pb-4 pb-sm-0 card-widget-3">
            <div>
              <h4 class="mb-0">{{ $all_reports->where('status', 2)->count() }}</h4>
              <p class="mb-0">{{ __('Resolved') }}</p>
            </div>
            <span class="avatar p-2 me-sm-6">
              <span class="avatar-initial bg-label-secondary rounded">
                <i class="ti-26px ti ti-check text-heading"></i>
              </span>
            </span>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h4 class="mb-0">{{ $all_reports->where('status', 3)->count() }}</h4>
              <p class="mb-0">{{ __('Dismissed') }}</p>
            </div>
            <span class="avatar p-2">
              <span class="avatar-initial bg-label-secondary rounded">
                <i class="ti-26px ti ti-x text-heading"></i>
              </span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Reports List Table -->
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-3">{{ __('Search Filter') }}</h5>
    <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
      <div class="col-md-4 user_status">
        <input type="text" id="search_by_title" class="form-control" placeholder="{{ __('Search by title...') }}">
      </div>
      <div class="col-md-4 d-flex justify-content-start justify-content-md-end">
        <button id="btn_clear_search" class="btn btn-secondary me-2">
          <i class="ti ti-refresh ti-xs"></i>
          <span class="align-middle">{{ __('Clear') }}</span>
        </button>
        <div>
          <div class="btn-group">
            <button class="btn btn-danger" type="button" id="bulk_delete_btn">{{ __('Delete') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card-datatable table-responsive">
    <div id="all_reports_table">
      <table class="table border-top">
        <thead>
          <tr>
            <th class="text-center">
              <div class="form-check form-check-sm">
                <input class="form-check-input" type="checkbox" value="" id="check_all">
              </div>
            </th>
            <th>{{ __('ID') }}</th>
            <th>{{ __('Listing') }}</th>
            <th>{{ __('User') }}</th>
            <th>{{ __('Reason') }}</th>
            <th>{{ __('Description') }}</th>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @include('backend.pages.listings.reports.search-result')
        </tbody>
      </table>
      <div class="text-center pt-3 pb-1">
        <div class="d-inline-block">
          {!! $all_reports->links() !!}
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bulk Action Form -->
<form action="{{ route('admin.listing.report.delete.bulk.action') }}" method="post" id="bulk_delete_form">
  @csrf
  <input type="hidden" name="ids" id="bulk_delete_ids">
</form>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('search_by_title');
  const clearButton = document.getElementById('btn_clear_search');
  const reportsTable = document.getElementById('all_reports_table');
  const checkAll = document.getElementById('check_all');
  const bulkDeleteBtn = document.getElementById('bulk_delete_btn');
  const bulkDeleteForm = document.getElementById('bulk_delete_form');
  const bulkDeleteIds = document.getElementById('bulk_delete_ids');

  // Search functionality
  searchInput.addEventListener('keyup', function() {
    const searchTerm = this.value;
    if (searchTerm.length > 0) {
      $.ajax({
        url: "{{ route('admin.listing.report.search') }}",
        method: "GET",
        data: {
          string_search: searchTerm
        },
        success: function(data) {
          if (data == 'nothing') {
            reportsTable.innerHTML = '<p class="text-center">{{ __('No reports found.') }}</p>';
          } else {
            reportsTable.innerHTML = data;
          }
        }
      });
    } else {
      loadPaginatedData(1);
    }
  });

  // Clear search
  clearButton.addEventListener('click', function() {
    searchInput.value = '';
    loadPaginatedData(1);
  });

  // Pagination
  function loadPaginatedData(page) {
    $.ajax({
      url: "{{ route('admin.listing.report.paginate.data') }}",
      method: "GET",
      data: {
        page: page,
        string_search: searchInput.value
      },
      success: function(data) {
        reportsTable.innerHTML = data;
      }
    });
  }

  // Handle pagination links
  $(document).on('click', '.pagination a', function(e) {
    e.preventDefault();
    const page = $(this).attr('href').split('page=')[1];
    loadPaginatedData(page);
  });

  // Bulk selection
  if (checkAll) {
    checkAll.addEventListener('change', function() {
      const isChecked = this.checked;
      document.querySelectorAll('.report-checkbox').forEach(function(checkbox) {
        checkbox.checked = isChecked;
      });
    });
  }

  // Bulk delete
  if (bulkDeleteBtn) {
    bulkDeleteBtn.addEventListener('click', function() {
      const selectedReports = document.querySelectorAll('.report-checkbox:checked');

      if (selectedReports.length === 0) {
        alert('{{ __('Please select at least one report to delete.') }}');
        return;
      }

      if (confirm('{{ __('Are you sure you want to delete the selected reports?') }}')) {
        const ids = Array.from(selectedReports).map(el => el.value);
        bulkDeleteIds.value = ids.join(',');
        bulkDeleteForm.submit();
      }
    });
  }
});
</script>
@endsection
