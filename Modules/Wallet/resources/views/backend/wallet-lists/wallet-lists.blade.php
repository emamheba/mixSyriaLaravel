@extends('layouts/layoutMaster')

@section('title', __('User Wallets'))

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
            <span class="text-heading">{{ __('Total Wallets') }}</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ count($wallet_lists) }}</h4>
            </div>
            <small class="mb-0">{{ __('All registered wallets') }}</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="ti ti-wallet ti-26px"></i>
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
            <span class="text-heading">{{ __('Active Wallets') }}</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $wallet_lists->where('status', 1)->count() }}</h4>
            </div>
            <small class="mb-0">{{ __('Wallets with active status') }}</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="ti ti-wallet-off ti-26px"></i>
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
            <span class="text-heading">{{ __('Total Balance') }}</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $wallet_lists->sum('balance') }}</h4>
            </div>
            <small class="mb-0">{{ __('Sum of all wallet balances') }}</small>
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
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">{{ __('Average Balance') }}</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $wallet_lists->count() > 0 ? round($wallet_lists->sum('balance') / $wallet_lists->count(), 2) : 0 }}</h4>
            </div>
            <small class="mb-0">{{ __('Average wallet balance') }}</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="ti ti-calculator ti-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Wallets List Table -->
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-3">{{ __('Search Filter') }}</h5>
    <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
      <div class="col-md-4">
        <input type="text" id="search_wallet" class="form-control" placeholder="{{ __('Search by balance') }}">
      </div>
      <div class="col-md-8 text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepositModal">
          <i class="ti ti-plus me-1"></i> {{ __('Add Deposit') }}
        </button>
      </div>
    </div>
  </div>
  <div class="card-datatable table-responsive" id="all_wallets_table_wrapper">
    @include('wallet::backend.wallet-lists.search-result')
  </div>
</div>

<!-- Add Deposit Modal -->
<div class="modal fade" id="addDepositModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addDepositModalTitle">{{ __('Add Deposit to User Wallet') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
      </div>
      <form action="{{ route('admin.wallet.deposit.create') }}" method="post">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-12 mb-3">
              <label class="form-label" for="user_id">{{ __('Select User') }}</label>
              <select id="user_id" name="user_id" class="form-select" required>
                <option value="">{{ __('Select User') }}</option>
                @foreach(\App\Models\User::all() as $user)
                  <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label" for="amount">{{ __('Amount') }}</label>
              <input type="number" id="amount" name="amount" class="form-control" min="10" max="{{ get_static_option('deposit_amount_limitation_for_user') ?? 50000 }}" required>
              <small class="form-text text-muted">{{ __('Min: 10, Max: ') }}{{ get_static_option('deposit_amount_limitation_for_user') ?? 50000 }}</small>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label" for="selected_payment_gateway">{{ __('Payment Method') }}</label>
              <select id="selected_payment_gateway" name="selected_payment_gateway" class="form-select" required>
                <option value="added_by_admin">{{ __('Added By Admin') }}</option>
                <option value="manual_payment">{{ __('Manual Payment') }}</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Add Deposit') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>


@section('page-script')
<script>
  $(document).ready(function () {
    $(document).on('keyup', '#search_wallet', function (e) {
      e.preventDefault();
      let search_string = $(this).val();
      
      $.ajax({
        url: "{{ route('admin.wallet.search') }}",
        type: "GET",
        data: {
          string_search: search_string
        },
        success: function (data) {
          if (data.status == 'nothing') {
            $('#all_wallets_table_wrapper').html('<p class="text-center">No wallet found</p>');
          } else {
            $('#all_wallets_table_wrapper').html(data);
          }
        }
      });
    });

    // For pagination
    $(document).on('click', '.pagination-wallet a', function (e) {
      e.preventDefault();
      let page = $(this).attr('href').split('page=')[1];
      
      $.ajax({
        url: "{{ route('admin.wallet.paginate.data') }}?page=" + page,
        success: function (data) {
          $('#all_wallets_table_wrapper').html(data);
        }
      });
    });
  });
</script>
@endsection

@endsection