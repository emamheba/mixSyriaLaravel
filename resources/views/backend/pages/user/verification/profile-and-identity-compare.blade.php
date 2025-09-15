@extends('layouts/layoutMaster')

@section('title', 'User Identity Details')

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">User Identity Verification Details</h5>
                    <a href="{{ route('admin.users.verification') }}" class="btn btn-primary">
                        <i class="ti ti-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- User Profile Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">User Profile Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-4">
<div class="avatar avatar-lg me-3">
  @if($userImageUrl)
    <img src="{{ $userImageUrl }}" alt="User" class="rounded-circle">
  @else
    <span class="avatar-initial rounded-circle bg-label-primary">
      {{ substr($user_details->first_name, 0, 1) }}{{ substr($user_details->last_name, 0, 1) }}
    </span>
  @endif
</div>
                                        <div>
                                            <h5 class="mb-0">{{ $user_details->first_name }}
                                                {{ $user_details->last_name }}</h5>
                                            <small class="text-muted">{{ $user_details->username }}</small>
                                        </div>
                                    </div>

                                    <div class="info-container">
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <span class="fw-medium me-1">Email:</span>
                                                <span>{{ $user_details->email }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium me-1">Phone:</span>
                                                <span>{{ $user_details->phone }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium me-1">Country:</span>
                                                <span>{{ $user_details->user_country->country ?? 'N/A' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium me-1">State:</span>
                                                <span>{{ $user_details->user_state->state ?? 'N/A' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium me-1">City:</span>
                                                <span>{{ $user_details->user_city->city ?? 'N/A' }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Verification Documents -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Verification Documents</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-container">
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <span class="fw-medium me-1">ID Type:</span>
                                                <span>{{ $user_identity_details->identification_type }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium me-1">ID Number:</span>
                                                <span>{{ $user_identity_details->identification_number }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium me-1">Address:</span>
                                                <span>{{ $user_identity_details->address }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium me-1">Zip Code:</span>
                                                <span>{{ $user_identity_details->zip_code }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium me-1">Status:</span>
                                                @if ($user_identity_details->status == 0)
                                                    <span class="badge bg-label-warning">Pending</span>
                                                @elseif($user_identity_details->status == 1)
                                                    <span class="badge bg-label-success">Approved</span>
                                                @else
                                                    <span class="badge bg-label-danger">Rejected</span>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Images -->
<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">Front Document</label>
    @if($frontDocumentUrl)
      <div class="border rounded p-2">
        <img src="{{ $frontDocumentUrl }}" 
             alt="Front Document" class="img-fluid">
      </div>
      <div class="mt-2">
        <a href="{{ $frontDocumentUrl }}" 
           class="btn btn-sm btn-primary" target="_blank">View Full Size</a>
      </div>
    @else
      <p class="text-muted">No front document uploaded</p>
    @endif
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label">Back Document</label>
    @if($backDocumentUrl)
      <div class="border rounded p-2">
        <img src="{{ $backDocumentUrl }}" 
             alt="Back Document" class="img-fluid">
      </div>
      <div class="mt-2">
        <a href="{{ $backDocumentUrl }}" 
           class="btn btn-sm btn-primary" target="_blank">View Full Size</a>
      </div>
    @else
      <p class="text-muted">No back document uploaded</p>
    @endif
  </div>
</div>
                            <!-- Action Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-center gap-3">
                                                <form action="{{ route('admin.users.verify.status') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $user_details->id }}">
                                                    @if ($user_details->verified_status == 0)
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="ti ti-check me-1"></i> Approve Verification
                                                        </button>
                                                    @else
                                                        <button type="submit" class="btn btn-warning">
                                                            <i class="ti ti-refresh me-1"></i> Request Re-verification
                                                        </button>
                                                    @endif
                                                </form>

                                                <form action="{{ route('admin.users.verify.decline') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="user_id"
                                                        value="{{ $user_details->id }}">
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="ti ti-x me-1"></i> Decline Verification
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
