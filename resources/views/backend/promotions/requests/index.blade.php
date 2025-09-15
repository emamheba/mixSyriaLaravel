@extends('layouts/layoutMaster')

@section('title', 'Promotion Requests Management')

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

<!-- Requests Stats Widget -->
<div class="card mb-6">
  <div class="card-widget-separator-wrapper">
    <div class="card-body card-widget-separator">
      <div class="row gy-4 gy-sm-1">
        <div class="col-sm-6 col-lg-2">
          <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
              <p class="mb-0">{{ __('Total Requests') }}</p>
            </div>
            <span class="avatar me-sm-6">
              <span class="avatar-initial bg-label-primary rounded text-heading">
                <i class="ti-26px ti ti-file-invoice text-heading"></i>
              </span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none me-6">
        </div>
        <div class="col-sm-6 col-lg-2">
          <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $stats['pending'] ?? 0 }}</h4>
              <p class="mb-0">{{ __('Pending') }}</p>
            </div>
            <span class="avatar p-2 me-lg-6">
              <span class="avatar-initial bg-label-warning rounded"><i class="ti-26px ti ti-clock text-heading"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none">
        </div>
        <div class="col-sm-6 col-lg-2">
          <div class="d-flex justify-content-between align-items-start card-widget-3 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $stats['paid'] ?? 0 }}</h4>
              <p class="mb-0">{{ __('Paid') }}</p>
            </div>
            <span class="avatar p-2 me-lg-6">
              <span class="avatar-initial bg-label-success rounded"><i class="ti-26px ti ti-checks text-heading"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none">
        </div>
        <div class="col-sm-6 col-lg-2">
          <div class="d-flex justify-content-between align-items-start card-widget-4 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $stats['failed'] ?? 0 }}</h4>
              <p class="mb-0">{{ __('Failed') }}</p>
            </div>
            <span class="avatar p-2 me-lg-6">
              <span class="avatar-initial bg-label-danger rounded"><i class="ti-26px ti ti-x text-heading"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none">
        </div>
        <div class="col-sm-6 col-lg-2">
          <div class="d-flex justify-content-between align-items-start card-widget-5 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $stats['bank_transfer'] ?? 0 }}</h4>
              <p class="mb-0">{{ __('Bank Transfer') }}</p>
            </div>
            <span class="avatar p-2 me-lg-6">
              <span class="avatar-initial bg-label-info rounded"><i class="ti-26px ti ti-building-bank text-heading"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none">
        </div>
        <div class="col-sm-6 col-lg-2">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h4 class="mb-0">{{ $stats['stripe'] ?? 0 }}</h4>
              <p class="mb-0">{{ __('Stripe') }}</p>
            </div>
            <span class="avatar p-2">
              <span class="avatar-initial bg-label-secondary rounded"><i class="ti-26px ti ti-credit-card text-heading"></i></span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="card-title mb-0">{{ __('Search & Filters') }}</h5>
  </div>
  <div class="card-body">
    <form method="GET" action="{{ route('promotions.requests.index') }}" id="search-form">
      <div class="row g-3">
        <!-- البحث الأساسي -->
        <div class="col-md-4">
          <label class="form-label">{{ __('Search') }}</label>
          <div class="input-group">
            <input type="text" class="form-control" name="search"
                   value="{{ request('search') }}"
                   placeholder="{{ __('Search by user, listing, package...') }}">
            <button class="btn btn-primary" type="submit">
              <i class="ti ti-search"></i>
            </button>
          </div>
        </div>

        <!-- فلتر الحالة -->
        <div class="col-md-2">
          <label class="form-label">{{ __('Status') }}</label>
          <select class="form-select" name="status">
            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
          </select>
        </div>

        <!-- فلتر طريقة الدفع -->
        <div class="col-md-2">
          <label class="form-label">{{ __('Payment Method') }}</label>
          <select class="form-select" name="payment_method">
            <option value="all" {{ request('payment_method') == 'all' ? 'selected' : '' }}>{{ __('All Methods') }}</option>
            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>{{ __('Bank Transfer') }}</option>
            <option value="stripe" {{ request('payment_method') == 'stripe' ? 'selected' : '' }}>{{ __('Stripe') }}</option>
          </select>
        </div>

        <!-- فلتر التاريخ من -->
        <div class="col-md-2">
          <label class="form-label">{{ __('Date From') }}</label>
          <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
        </div>

        <!-- فلتر التاريخ إلى -->
        <div class="col-md-2">
          <label class="form-label">{{ __('Date To') }}</label>
          <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-12">
          <div class="btn-group" role="group">
            <button type="submit" class="btn btn-primary">
              <i class="ti ti-search me-1"></i>{{ __('Search') }}
            </button>
            <a href="{{ route('promotions.requests.index') }}" class="btn btn-outline-secondary">
              <i class="ti ti-refresh me-1"></i>{{ __('Reset') }}
            </a>
            @if($stats['pending'] > 0)
            <form action="{{ route('promotions.requests.bulk-approve-bank-transfers') }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-success" onclick="return confirm('{{ __('Are you sure you want to approve all pending bank transfers?') }}')">
                <i class="ti ti-checks me-1"></i>{{ __('Bulk Approve Bank Transfers') }}
              </button>
            </form>
            @endif
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Requests Table -->
<div class="card">
  <div class="card-header border-bottom">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h5 class="card-title mb-0">{{ __('Promotion Requests') }}</h5>
        @if(request()->hasAny(['search', 'status', 'payment_method', 'date_from', 'date_to']))
          <small class="text-muted">
            {{ __('Showing :count results', ['count' => $requests->total()]) }}
            @if(request('search'))
              {{ __('for') }} "<strong>{{ request('search') }}</strong>"
            @endif
          </small>
        @endif
      </div>
    </div>
  </div>

  <div class="card-datatable table-responsive">
    <table id="requests-table" class="table border-top">
      <thead>
        <tr>
          <th>{{ __('ID') }}</th>
          <th>{{ __('User') }}</th>
          <th>{{ __('Listing') }}</th>
          <th>{{ __('Package') }}</th>
          <th>{{ __('Amount') }}</th>
          <th>{{ __('Payment Method') }}</th>
          <th>{{ __('Status') }}</th>
          <th>{{ __('Date') }}</th>
          <th>{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($requests as $request)
        <tr>
          <td>{{ $request->id }}</td>
          <td>
            <div class="d-flex align-items-center">
              <div class="avatar avatar-sm me-3">
                <span class="avatar-initial bg-label-primary rounded">
                  <i class="ti ti-user"></i>
                </span>
              </div>
              <div>
                <strong>{{ $request->user->name }}</strong>
                <br><small class="text-muted">{{ $request->user->email }}</small>
              </div>
            </div>
          </td>
          <td>
            <div class="d-flex align-items-center">
              <div class="avatar avatar-sm me-3">
                <span class="avatar-initial bg-label-secondary rounded">
                  <i class="ti ti-home"></i>
                </span>
              </div>
              <div>
                <strong class="text-truncate d-block" style="max-width: 150px;" title="{{ $request->listing?->title }}">
                  {{ $request->listing?->title }}
                </strong>
                <small class="text-muted">ID: {{ $request->listing?->id }}</small>
              </div>
            </div>
          </td>
          <td>
            <span class="badge bg-label-info rounded-pill">
              {{ $request->promotionPackage->name }}
            </span>
            <br><small class="text-muted">{{ $request->promotionPackage->duration_days }} {{ __('days') }}</small>
          </td>
          <td>
            <span class="badge bg-label-success rounded-pill">
              ${{ number_format($request->amount, 2) }}
            </span>
          </td>
          <td>
            @if($request->payment_method == 'bank_transfer')
              <span class="badge bg-label-info">
                <i class="ti ti-building-bank me-1"></i>{{ __('Bank Transfer') }}
              </span>
            @elseif($request->payment_method == 'stripe')
              <span class="badge bg-label-secondary">
                <i class="ti ti-credit-card me-1"></i>{{ __('Stripe') }}
              </span>
            @endif
          </td>
          <td>
            @if($request->payment_status == 'pending')
              <span class="badge bg-label-warning">
                <i class="ti ti-clock me-1"></i>{{ __('Pending') }}
              </span>
            @elseif($request->payment_status == 'paid')
              <span class="badge bg-label-success">
                <i class="ti ti-checks me-1"></i>{{ __('Paid') }}
              </span>
            @elseif($request->payment_status == 'failed')
              <span class="badge bg-label-danger">
                <i class="ti ti-x me-1"></i>{{ __('Failed') }}
              </span>
            @endif
          </td>
          <td>
            <span class="text-muted">{{ $request->created_at->format('M d, Y') }}</span>
            <br><small class="text-muted">{{ $request->created_at->format('H:i') }}</small>
          </td>
          <td>
            <div class="dropdown">
              <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                <i class="ti ti-dots-vertical"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="{{ route('promotions.requests.show', $request->id) }}">
                  <i class="ti ti-eye me-1"></i> {{ __('View Details') }}
                </a>

                @if($request->payment_method == 'bank_transfer' && $request->payment_status == 'pending')
                  <div class="dropdown-divider"></div>
                  <form action="{{ route('promotions.requests.approve-bank-transfer', $request->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="dropdown-item text-success" onclick="return confirm('{{ __('Are you sure you want to approve this bank transfer?') }}')">
                      <i class="ti ti-check me-1"></i> {{ __('Approve') }}
                    </button>
                  </form>
                  <a class="dropdown-item text-warning" href="#" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                    <i class="ti ti-x me-1"></i> {{ __('Reject') }}
                  </a>
                @endif

                <div class="dropdown-divider"></div>
                <form id="delete-form-{{ $request->id }}" action="{{ route('promotions.requests.delete', $request->id) }}" method="POST" class="d-none">
                  @csrf
                  @method('DELETE')
                </form>
                <a class="dropdown-item text-danger delete-item" href="#" data-id="{{ $request->id }}">
                  <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
                </a>
              </div>
            </div>
          </td>
        </tr>

        <!-- Reject Modal -->
        @if($request->payment_method == 'bank_transfer' && $request->payment_status == 'pending')
        <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">{{ __('Reject Bank Transfer') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form action="{{ route('promotions.requests.reject-bank-transfer', $request->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">{{ __('Rejection Reason') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="admin_notes" rows="4" placeholder="{{ __('Please provide a reason for rejection...') }}" required></textarea>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                  <button type="submit" class="btn btn-danger">{{ __('Reject Request') }}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        @endif

        @empty
        <tr>
          <td colspan="9" class="text-center">
            <div class="my-4">
              <i class="ti ti-file-invoice-off display-4 text-muted"></i>
              <h5 class="mt-2">{{ __('No promotion requests found') }}</h5>
              @if(request()->hasAny(['search', 'status', 'payment_method', 'date_from', 'date_to']))
                <p class="text-muted">{{ __('Try adjusting your search criteria') }}</p>
                <a href="{{ route('promotions.requests.index') }}" class="btn btn-primary">
                  <i class="ti ti-refresh me-1"></i>{{ __('Reset Filters') }}
                </a>
              @else
                <p class="text-muted">{{ __('No promotion requests have been submitted yet') }}</p>
              @endif
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>

    <!-- Pagination -->
    @if($requests->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3">
      <div>
        <small class="text-muted">
          {{ __('Showing :from to :to of :total results', [
            'from' => $requests->firstItem(),
            'to' => $requests->lastItem(),
            'total' => $requests->total()
          ]) }}
        </small>
      </div>
      <div>
        {{ $requests->links() }}
      </div>
    </div>
    @endif
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // البحث السريع عند الكتابة
    const searchInput = document.querySelector('input[name="search"]');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3 || this.value.length === 0) {
                    document.getElementById('search-form').submit();
                }
            }, 500);
        });
    }

    // تغيير الفلاتر تلقائياً
    const filterSelects = document.querySelectorAll('select[name="status"], select[name="payment_method"]');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('search-form').submit();
        });
    });

    // تغيير التاريخ تلقائياً
    const dateInputs = document.querySelectorAll('input[name="date_from"], input[name="date_to"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            document.getElementById('search-form').submit();
        });
    });

    // حذف العنصر
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-item')) {
            e.preventDefault();
            const id = e.target.getAttribute('data-id');
            if (confirm('{{ __("Are you sure you want to delete this promotion request?") }}')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    });
});
</script>

@endsection
