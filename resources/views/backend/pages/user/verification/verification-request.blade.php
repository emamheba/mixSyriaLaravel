@extends('layouts/layoutMaster')

@section('title', 'User Verification Requests')

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
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js'
])
@endsection

@section('page-script')
<script>
  $(document).ready(function() {
    $('.data-table').DataTable({
      responsive: true,
      dom: '<"card-header border-bottom p-4"<"head-label"><"dt-action-buttons text-end"B>><"d-flex justify-content-between align-items-center mx-4 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"table-responsive"t><"d-flex justify-content-between mx-4 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      buttons: [
        {
          extend: 'collection',
          className: 'btn btn-label-primary dropdown-toggle me-2',
          text: '<i class="ti ti-file-export me-1 ti-xs"></i>Export',
          buttons: [
            {
              extend: 'print',
              text: '<i class="ti ti-printer me-2" ></i>Print',
              className: 'dropdown-item',
              exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
            },
            {
              extend: 'csv',
              text: '<i class="ti ti-file-spreadsheet me-2"></i>Csv',
              className: 'dropdown-item',
              exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
            },
            {
              extend: 'excel',
              text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
              className: 'dropdown-item',
              exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
            },
            {
              extend: 'pdf',
              text: '<i class="ti ti-file-text me-2"></i>Pdf',
              className: 'dropdown-item',
              exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
            },
            {
              extend: 'copy',
              text: '<i class="ti ti-copy me-2"></i>Copy',
              className: 'dropdown-item',
              exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
            }
          ]
        }
      ]
    });

    $('div.head-label').html('<h5 class="card-title mb-0">User Verification Requests</h5>');
  });
</script>
@include('backend.pages.user.verification.user-verification-js')

@endsection
@section('content')

<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">{{ __('Total Users') }}</span>            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $totalUsers }}</h4>
            </div>
            <small class="mb-0">{{ __('Registered Users') }}</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="ti ti-users ti-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">{{ __('Verified Users') }}</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $verifiedUsers }}</h4>
            </div>
            <small class="mb-0">{{ __('Identity Verified') }}</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="ti ti-user-check ti-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">{{ __('Pending Verification') }}</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $pendingVerifications }}</h4>
            </div>
            <small class="mb-0">{{ __('Awaiting Review') }}</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="ti ti-user-search ti-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">{{ __('Rejected Verifications') }}</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $rejectedVerifications }}</h4>
            </div>
            <small class="mb-0">{{ __('Failed Verification') }}</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-danger">
              <i class="ti ti-user-x ti-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="card mb-4">
  <div class="card-header border-bottom d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">{{ __('Search & Filters') }}</h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <form action="{{ route('admin.users.verification.search') }}" method="GET" class="mb-3">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="{{ __('Search by name, email or username') }}" name="search" value="{{ isset($search) ? $search : '' }}">
            <button class="btn btn-outline-primary" type="submit"><i class="ti ti-search"></i> {{ __('Search') }}</button>
          </div>
        </form>
      </div>
      <div class="col-md-6">
        <form action="{{ route('admin.users.verification.filter') }}" method="GET">
          <div class="input-group">
            <select class="form-select" name="status">
              <option value="">{{ __('All Status') }}</option>
              <option value="0" {{ isset($status) && $status == '0' ? 'selected' : '' }}>{{ __('Pending') }}</option>
              <option value="1" {{ isset($status) && $status == '1' ? 'selected' : '' }}>{{ __('Approved') }}</option>
              <option value="2" {{ isset($status) && $status == '2' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
            </select>
            <button class="btn btn-outline-primary" type="submit"><i class="ti ti-filter"></i> {{ __('Filter') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Users Verification Requests Table -->
<div class="card">
  <div class="card-datatable table-responsive">
    <table class="data-table table border-top">
      <thead>
        <tr>
          <th>{{ __('ID') }}</th>
          <th>{{ __('User') }}</th>
          <th>{{ __('ID Type') }}</th>
          <th>{{ __('ID Number') }}</th>
          <th>{{ __('Location') }}</th>
          <th>{{ __('Status') }}</th>
          <th>{{ __('Submitted') }}</th>
          <th>{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($all_requests as $request)
        <tr>
          <td>{{ $request->id }}</td>
          <td>
            <div class="d-flex justify-content-start align-items-center user-name">
              <div class="avatar-wrapper">
                <div class="avatar avatar-sm me-3">
                  @if($request->user->image)
                    <img src="{{ asset('storage/' . $request->user->image) }}" alt="User" class="rounded-circle">
                  @else
                    <span class="avatar-initial rounded-circle bg-label-primary">
                      {{ substr($request->user->first_name, 0, 1) }}{{ substr($request->user->last_name, 0, 1) }}
                    </span>
                  @endif
                </div>
              </div>
              <div class="d-flex flex-column">
                <a href="{{ route('admin.users.identity.details', ['user_id' => $request->user_id]) }}" class="text-body text-truncate">
                  <span class="fw-medium">{{ $request->user->first_name }} {{ $request->user->last_name }}</span>
                </a>
                <small class="text-muted">{{ $request->user->email }}</small>
              </div>
            </div>
          </td>
          <td>{{ $request->identification_type }}</td>
          <td>{{ $request->identification_number }}</td>
          <td>
            {{ $request->user_country->country ?? '' }}
            {{ $request->user_state->state ?? '' }}
            {{ $request->user_city->city ?? '' }}
          </td>
          <td>
            @if($request->status == 0)
            <span class="badge bg-label-warning">{{ __('Pending') }}</span>
          @elseif($request->status == 1)
            <span class="badge bg-label-success">{{ __('Approved') }}</span>
          @else
            <span class="badge bg-label-danger">{{ __('Rejected') }}</span>
          @endif
          </td>
          <td>{{ $request->created_at->format('d M Y') }}</td>
          <td>
            <div class="d-flex align-items-center">
              <a href="{{ route('admin.users.identity.details', ['user_id' => $request->user_id]) }}" class="btn btn-sm btn-icon"><i class="ti ti-eye text-primary"></i></a>

              <form action="{{ route('admin.users.verify.status') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="user_id" value="{{ $request->user_id }}">
                @if($request->user->verified_status == 0)
                  <button type="submit" class="btn btn-sm btn-icon"><i class="ti ti-check text-success"></i></button>
                @else
                  <button type="submit" class="btn btn-sm btn-icon"><i class="ti ti-x text-warning"></i></button>
                @endif
              </form>

              <form action="{{ route('admin.users.verify.decline') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="user_id" value="{{ $request->user_id }}">
                <button type="submit" class="btn btn-sm btn-icon"><i class="ti ti-trash text-danger"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center">{{ __('No verification requests found') }}</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <!-- Pagination -->
  <div class="d-flex justify-content-center py-3">
    {{ $all_requests->links() }}
  </div>
</div>
@include('backend.pages.user.verification.user-details-modal')

@endsection
