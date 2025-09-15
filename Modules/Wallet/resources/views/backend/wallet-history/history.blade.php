@extends('layouts/layoutMaster')

@section('title', __('Wallet Transaction History'))

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

@section('content')
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">{{ __('Total Transactions') }}</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ count($wallet_history_lists) }}</h4>
            </div>
            <small class="mb-0">{{ __('All recorded transactions') }}</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="ti ti-history ti-26px"></i>
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
            <span class="text-heading">{{ __('Completed Transactions') }}</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $wallet_history_lists->where('payment_status', 'complete')->count() }}</h4>
            </div>
            <small class="mb-0">{{ __('Successfully completed deposits') }}</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="ti ti-check ti-26px"></i>
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
            <span class="text-heading">{{ __('Pending Transactions') }}</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $wallet_history_lists->where('payment_status', 'pending')->count() }}</h4>
            </div>
            <small class="mb-0">{{ __('Deposits awaiting approval') }}</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="ti ti-clock ti-26px"></i>
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
            <span class="text-heading">{{ __('Total Amount') }}</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $wallet_history_lists->sum('amount') }}</h4>
            </div>
            <small class="mb-0">{{ __('Sum of all transactions') }}</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-info">
              <i class="ti ti-currency-dollar ti-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Transaction History Table -->
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-3">{{ __('Search Filter') }}</h5>
    <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
      <div class="col-md-4">
        <input type="text" id="search_wallet_history" class="form-control" placeholder="{{ __('Search by amount') }}">
      </div>
      <div class="col-md-4">
        <select id="filter_user" class="form-select">
          <option value="">{{ __('All Users') }}</option>
          @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <select id="filter_status" class="form-select">
          <option value="">{{ __('All Status') }}</option>
          <option value="complete">{{ __('Complete') }}</option>
          <option value="pending">{{ __('Pending') }}</option>
        </select>
      </div>
    </div>
  </div>
  <div class="card-datatable table-responsive" id="all_wallet_history_table_wrapper">
    @include('wallet::backend.wallet-history.search-result')
  </div>
</div>


@section('page-script')
<script>
  $(document).ready(function () {
    // Initialize Select2
    $('#filter_user, #filter_status').select2();
    
    // Search by amount
    $(document).on('keyup', '#search_wallet_history', function (e) {
      e.preventDefault();
      let search_string = $(this).val();
      
      $.ajax({
        url: "{{ route('admin.wallet.history.search') }}",
        type: "GET",
        data: {
          string_search: search_string
        },
        success: function (data) {
          if (data.status == 'nothing') {
            $('#all_wallet_history_table_wrapper').html('<p class="text-center">No transaction found</p>');
          } else {
            $('#all_wallet_history_table_wrapper').html(data);
          }
        }
      });
    });

    // Filter by user and status
    $('#filter_user, #filter_status').on('change', function() {
      let user_id = $('#filter_user').val();
      let status = $('#filter_status').val();
      
      // You'll need to implement this endpoint in your controller
      $.ajax({
        url: "{{ route('admin.wallet.history.filter') }}",
        type: "GET",
        data: {
          user_id: user_id,
          status: status
        },
        success: function (data) {
          $('#all_wallet_history_table_wrapper').html(data);
        }
      });
    });

    // For pagination
    $(document).on('click', '.pagination-history a', function (e) {
      e.preventDefault();
      let page = $(this).attr('href').split('page=')[1];
      
      $.ajax({
        url: "{{ route('admin.wallet.history.paginate.data') }}?page=" + page,
        success: function (data) {
          $('#all_wallet_history_table_wrapper').html(data);
        }
      });
    });
  });
</script>
@endsection

@endsection