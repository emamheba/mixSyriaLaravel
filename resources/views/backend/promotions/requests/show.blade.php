@extends('layouts/layoutMaster')

@section('title', 'Promotion Request Details')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/bs-stepper/bs-stepper.js'
])
@endsection

@section('content')

<!-- Header -->
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-1">{{ __('Promotion Request Details') }}</h4>
        <p class="mb-0 text-muted">{{ __('Request ID') }}: #{{ $request->id }}</p>
      </div>
      <div>
        <a href="{{ route('promotions.requests.index') }}" class="btn btn-outline-secondary">
          <i class="ti ti-arrow-left me-1"></i>{{ __('Back to Requests') }}
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Status Overview -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-3">
            <div class="d-flex align-items-center">
              <div class="avatar avatar-lg me-3">
                @if($request->payment_status == 'pending')
                  <span class="avatar-initial bg-label-warning rounded">
                    <i class="ti-lg ti ti-clock"></i>
                  </span>
                @elseif($request->payment_status == 'paid')
                  <span class="avatar-initial bg-label-success rounded">
                    <i class="ti-lg ti ti-checks"></i>
                  </span>
                @elseif($request->payment_status == 'failed')
                  <span class="avatar-initial bg-label-danger rounded">
                    <i class="ti-lg ti ti-x"></i>
                  </span>
                @endif
              </div>
              <div>
                <h5 class="mb-1">{{ __('Status') }}</h5>
                @if($request->payment_status == 'pending')
                  <span class="badge bg-label-warning rounded-pill">
                    <i class="ti ti-clock me-1"></i>{{ __('Pending') }}
                  </span>
                @elseif($request->payment_status == 'paid')
                  <span class="badge bg-label-success rounded-pill">
                    <i class="ti ti-checks me-1"></i>{{ __('Paid') }}
                  </span>
                @elseif($request->payment_status == 'failed')
                  <span class="badge bg-label-danger rounded-pill">
                    <i class="ti ti-x me-1"></i>{{ __('Failed') }}
                  </span>
                @endif
              </div>
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="d-flex align-items-center">
              <div class="avatar avatar-lg me-3">
                @if($request->payment_method == 'bank_transfer')
                  <span class="avatar-initial bg-label-info rounded">
                    <i class="ti-lg ti ti-building-bank"></i>
                  </span>
                @else
                  <span class="avatar-initial bg-label-secondary rounded">
                    <i class="ti-lg ti ti-credit-card"></i>
                  </span>
                @endif
              </div>
              <div>
                <h5 class="mb-1">{{ __('Payment Method') }}</h5>
                @if($request->payment_method == 'bank_transfer')
                  <span class="badge bg-label-info rounded-pill">
                    <i class="ti ti-building-bank me-1"></i>{{ __('Bank Transfer') }}
                  </span>
                @else
                  <span class="badge bg-label-secondary rounded-pill">
                    <i class="ti ti-credit-card me-1"></i>{{ __('Stripe') }}
                  </span>
                @endif
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="d-flex align-items-center">
              <div class="avatar avatar-lg me-3">
                <span class="avatar-initial bg-label-success rounded">
                  <i class="ti-lg ti ti-currency-dollar"></i>
                </span>
              </div>
              <div>
                <h5 class="mb-1">{{ __('Amount') }}</h5>
                <h4 class="text-success mb-0">${{ number_format($request->amount, 2) }}</h4>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="d-flex align-items-center">
              <div class="avatar avatar-lg me-3">
                <span class="avatar-initial bg-label-primary rounded">
                  <i class="ti-lg ti ti-calendar"></i>
                </span>
              </div>
              <div>
                <h5 class="mb-1">{{ __('Date Created') }}</h5>
                <p class="mb-0">{{ $request->created_at->format('M d, Y H:i') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="row">
  <!-- Left Column -->
  <div class="col-lg-8">
    <!-- User Information -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0">{{ __('User Information') }}</h5>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="avatar avatar-lg me-3">
            <span class="avatar-initial bg-label-primary rounded">
              <i class="ti-lg ti ti-user"></i>
            </span>
          </div>
          <div>
            <h5 class="mb-1">{{ $request->user->name }}</h5>
            <p class="mb-0 text-muted">{{ $request->user->email }}</p>
          </div>
        </div>
        
        <div class="row">
          <div class="col-sm-6">
            <small class="text-muted d-block">{{ __('User ID') }}</small>
            <p class="mb-2">#{{ $request->user->id }}</p>
          </div>
          <div class="col-sm-6">
            <small class="text-muted d-block">{{ __('Join Date') }}</small>
            <p class="mb-2">{{ $request->user->created_at->format('M d, Y') }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Listing Information -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Listing Information') }}</h5>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-start mb-3">
          <div class="avatar avatar-lg me-3">
            <span class="avatar-initial bg-label-secondary rounded">
              <i class="ti-lg ti ti-home"></i>
            </span>
          </div>
          <div class="flex-grow-1">
            <h5 class="mb-1">{{ $request->listing->title }}</h5>
            <p class="mb-2 text-muted">{{ __('Listing ID') }}: #{{ $request->listing->id }}</p>
            @if($request->listing->description)
              <p class="mb-0 text-truncate" style="max-width: 400px;">{{ $request->listing->description }}</p>
            @endif
          </div>
        </div>
        
        <div class="row">
          <div class="col-sm-4">
            <small class="text-muted d-block">{{ __('Category') }}</small>
            <p class="mb-2">{{ $request->listing->category->name ?? __('N/A') }}</p>
          </div>
          <div class="col-sm-4">
            <small class="text-muted d-block">{{ __('Status') }}</small>
            <p class="mb-2">
              @if($request->listing->is_active)
                <span class="badge bg-label-success">{{ __('Active') }}</span>
              @else
                <span class="badge bg-label-secondary">{{ __('Inactive') }}</span>
              @endif
            </p>
          </div>
          <div class="col-sm-4">
            <small class="text-muted d-block">{{ __('Featured Until') }}</small>
            <p class="mb-2">
              @if($request->listing->promoted_until)
                {{ $request->listing->promoted_until->format('M d, Y') }}
                @if($request->listing->promoted_until->isFuture())
                  <span class="badge bg-label-success">{{ __('Active') }}</span>
                @else
                  <span class="badge bg-label-warning">{{ __('Expired') }}</span>
                @endif
              @else
                {{ __('Not Promoted') }}
              @endif
            </p>
          </div>
        </div>
        
        <div class="mt-3">
          <a href="{{ route('listings.show', $request->listing->id) }}" class="btn btn-outline-primary btn-sm" target="_blank">
            <i class="ti ti-external-link me-1"></i>{{ __('View Listing') }}
          </a>
        </div>
      </div>
    </div>

    <!-- Package Information -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Promotion Package') }}</h5>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-start mb-3">
          <div class="avatar avatar-lg me-3">
            <span class="avatar-initial bg-label-info rounded">
              <i class="ti-lg ti ti-package"></i>
            </span>
          </div>
          <div>
            <h5 class="mb-1">{{ $request->promotionPackage->name }}</h5>
            @if($request->promotionPackage->description)
              <p class="mb-2 text-muted">{{ $request->promotionPackage->description }}</p>
            @endif
          </div>
        </div>
        
        <div class="row">
          <div class="col-sm-4">
            <small class="text-muted d-block">{{ __('Duration') }}</small>
            <p class="mb-2">{{ $request->promotionPackage->duration_days }} {{ __('days') }}</p>
          </div>
          <div class="col-sm-4">
            <small class="text-muted d-block">{{ __('Package Price') }}</small>
            <p class="mb-2">${{ number_format($request->promotionPackage->price, 2) }}</p>
          </div>
          <div class="col-sm-4">
            <small class="text-muted d-block">{{ __('Paid Amount') }}</small>
            <p class="mb-2 text-success fw-medium">${{ number_format($request->amount, 2) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Details -->
    @if($request->payment_method == 'bank_transfer')
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Bank Transfer Details') }}</h5>
      </div>
      <div class="card-body">
        @if($request->bank_transfer_proof_path)
        <div class="mb-3">
          <small class="text-muted d-block mb-2">{{ __('Payment Proof') }}</small>
          <div class="border rounded p-2">
            <img src="{{ Storage::url($request->bank_transfer_proof_path) }}" 
                 alt="{{ __('Payment Proof') }}" 
                 class="img-fluid rounded" 
                 style="max-height: 300px; cursor: pointer;"
                 data-bs-toggle="modal" 
                 data-bs-target="#proofModal">
          </div>
        </div>
        @endif
        
        @if($request->bank_transfer_notes)
        <div class="mb-3">
          <small class="text-muted d-block mb-2">{{ __('Transfer Notes') }}</small>
          <div class="bg-light p-3 rounded">
            <p class="mb-0">{{ $request->bank_transfer_notes }}</p>
          </div>
        </div>
        @endif
        
        @if($request->admin_notes)
        <div class="mb-3">
          <small class="text-muted d-block mb-2">{{ __('Admin Notes') }}</small>
          <div class="bg-light p-3 rounded">
            <p class="mb-0">{{ $request->admin_notes }}</p>
          </div>
        </div>
        @endif
      </div>
    </div>
    @endif
  </div>

  <!-- Right Column -->
  <div class="col-lg-4">
    <!-- Actions Card -->
    @if($request->payment_method == 'bank_transfer' && $request->payment_status == 'pending')
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Actions') }}</h5>
      </div>
      <div class="card-body">
        <div class="d-grid gap-2">
          <form action="{{ route('promotions.requests.approve-bank-transfer', $request->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success w-100" onclick="return confirm('{{ __('Are you sure you want to approve this bank transfer?') }}')">
              <i class="ti ti-check me-1"></i>{{ __('Approve Transfer') }}
            </button>
          </form>
          
          <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
            <i class="ti ti-x me-1"></i>{{ __('Reject Transfer') }}
          </button>
        </div>
      </div>
    </div>
    @endif

    <!-- Timeline -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Timeline') }}</h5>
      </div>
      <div class="card-body">
        <ul class="timeline">
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-primary"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">{{ __('Request Created') }}</h6>
                <small class="text-muted">{{ $request->created_at->format('M d, Y H:i') }}</small>
              </div>
              <p class="mb-0">{{ __('Promotion request submitted') }}</p>
            </div>
          </li>
          
          @if($request->payment_confirmed_at)
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-success"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">{{ __('Payment Confirmed') }}</h6>
                <small class="text-muted">{{ $request->payment_confirmed_at->format('M d, Y H:i') }}</small>
              </div>
              <p class="mb-0">{{ __('Payment verified and approved') }}</p>
            </div>
          </li>
          @endif
          
          @if($request->starts_at)
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-info"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">{{ __('Promotion Started') }}</h6>
                <small class="text-muted">{{ $request->starts_at->format('M d, Y H:i') }}</small>
              </div>
              <p class="mb-0">{{ __('Listing promotion activated') }}</p>
            </div>
          </li>
          @endif
          
          @if($request->expires_at)
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point {{ $request->expires_at->isFuture() ? 'timeline-point-warning' : 'timeline-point-secondary' }}"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">{{ $request->expires_at->isFuture() ? __('Promotion Expires') : __('Promotion Expired') }}</h6>
                <small class="text-muted">{{ $request->expires_at->format('M d, Y H:i') }}</small>
              </div>
              <p class="mb-0">{{ __('End of promotion period') }}</p>
            </div>
          </li>
          @endif
        </ul>
      </div>
    </div>

    <!-- Request Info -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Request Information') }}</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <small class="text-muted d-block">{{ __('Request ID') }}</small>
          <p class="mb-0">#{{ $request->id }}</p>
        </div>
        
        <div class="mb-3">
          <small class="text-muted d-block">{{ __('Stripe Session ID') }}</small>
          <p class="mb-0">{{ $request->stripe_session_id ?: __('N/A') }}</p>
        </div>
        
        <div class="mb-3">
          <small class="text-muted d-block">{{ __('Created At') }}</small>
          <p class="mb-0">{{ $request->created_at->format('M d, Y H:i:s') }}</p>
        </div>
        
        <div class="mb-0">
          <small class="text-muted d-block">{{ __('Updated At') }}</small>
          <p class="mb-0">{{ $request->updated_at->format('M d, Y H:i:s') }}</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Reject Modal -->
@if($request->payment_method == 'bank_transfer' && $request->payment_status == 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
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
            <textarea class="form-control" name="admin_notes" rows="4" placeholder="{{ __('Please provide a detailed reason for rejection...') }}" required></textarea>
            <div class="form-text">{{ __('This message will be saved as admin notes for this request.') }}</div>
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

<!-- Proof Modal -->
@if($request->payment_method == 'bank_transfer' && $request->bank_transfer_proof_path)
<div class="modal fade" id="proofModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Payment Proof') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img src="{{ Storage::url($request->bank_transfer_proof_path) }}" 
             alt="{{ __('Payment Proof') }}" 
             class="img-fluid rounded">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <a href="{{ Storage::url($request->bank_transfer_proof_path) }}" class="btn btn-primary" download>
          <i class="ti ti-download me-1"></i>{{ __('Download') }}
        </a>
      </div>
    </div>
  </div>
</div>
@endif

@endsection