@extends('layouts/layoutMaster')

@section('title', __('User Memberships - Pages'))

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
        'resources/assets/vendor/libs/select2/select2.scss',
        'resources/assets/vendor/libs/@form-validation/form-validation.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/moment/moment.js',
        'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
        'resources/assets/vendor/libs/select2/select2.js',
        'resources/assets/vendor/libs/@form-validation/popular.js',
        'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
        'resources/assets/vendor/libs/@form-validation/auto-focus.js',
        'resources/assets/vendor/libs/cleavejs/cleave.js',
        'resources/assets/vendor/libs/cleavejs/cleave-phone.js'
    ])
@endsection

@section('page-script')
    @include('membership::backend.user-membership.membership-js')
@endsection

@section('content')

    <div class="row g-6 mb-6">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ __('Total Memberships') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $all_memberships->total() ?? 0 }}</h4>
                            </div>
                            <small class="mb-0">{{ __('All registered memberships') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ti ti-id-badge ti-26px"></i>
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
                            <span class="text-heading">{{ __('Active Memberships') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $active_membership ?? 0 }}</h4>
                            </div>
                            <small class="mb-0">{{ __('Currently active memberships') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ti ti-check-circle ti-26px"></i>
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
                            <span class="text-heading">{{ __('Inactive Memberships') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $inactive_membership ?? 0 }}</h4>
                            </div>
                            <small class="mb-0">{{ __('Currently inactive memberships') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ti ti-x-circle ti-26px"></i>
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
                            <span class="text-heading">{{ __('Manual Payments') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $manual_membership ?? 0 }}</h4>
                            </div>
                            <small class="mb-0">{{ __('Memberships with manual payments') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ti ti-cash ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Memberships List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">{{ __('User Memberships') }}</h5>
            <div class="d-flex justify-content-between align-items-center row pt-3 gap-4 gap-md-0">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" id="string_search" class="form-control" placeholder="{{ __('Search memberships...') }}">
                        <input type="hidden" id="get_selected_value">
                    </div>
                </div>
                <div class="col-md-8 text-end">
                    <div class="d-flex justify-content-end">
                        <button id="active_membership" data-val="active-sub" class="btn btn-outline-success me-2">
                            <i class="ti ti-check me-1"></i>{{ __('Active') }}
                        </button>
                        <button id="inactive_membership" data-val="inactive-sub" class="btn btn-outline-danger me-2">
                            <i class="ti ti-x-circle me-1"></i>{{ __('Inactive') }}
                        </button>
                        <button id="manual_membership" data-val="manual-sub" class="btn btn-outline-warning">
                            <i class="ti ti-cash me-1"></i>{{ __('Manual Payment') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-datatable table-responsive search_result">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Membership Details') }}</th>
                        <th>{{ __('User Details') }}</th>
                        <th>{{ __('Payment Gateway') }}</th>
                        <th>{{ __('Payment Status') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Purchase Date') }}</th>
                        <th>{{ __('Expire Date') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($all_memberships as $membership)
                    <tr>
                        <td>{{ $membership->id }}</td>
                        <td>
                            <div class="membership-details">
                                <span class="fw-semibold d-block">{{ optional($membership->membership)->title }}</span>
                                 
                                <span class="badge bg-label-primary mb-1">
                                    {{ $membership->price == 0 ? float_amount_with_currency_symbol($membership->initial_price) : float_amount_with_currency_symbol($membership->price) }}
                                </span>
                                 
                                <div class="text-muted small">
                                    <div><span class="fw-semibold">{{ __('Type') }}:</span> {{ $membership->membership?->membership_type?->type }}</div>
                                    <div><span class="fw-semibold">{{ __('Listings') }}:</span> {{ $membership->listing_limit }}</div>
                                    <div><span class="fw-semibold">{{ __('Images/Listing') }}:</span> {{ $membership->gallery_images }}</div>
                                    <div><span class="fw-semibold">{{ __('Featured Listings') }}:</span> {{ $membership->featured_listing }}</div>
                                </div>
                                 
                                <div class="mt-1">
                                    <span class="me-2"><i class="ti {{ $membership->business_hour == 1 ? 'ti-clock text-success' : 'ti-clock-off text-muted' }}"></i> {{ __('Business Hours') }}</span>
                                    <span class="me-2"><i class="ti {{ $membership->enquiry_form == 1 ? 'ti-forms text-success' : 'ti-forms text-muted' }}"></i> {{ __('Enquiry Form') }}</span>
                                    <span><i class="ti {{ $membership->membership_badge == 1 ? 'ti-badge text-success' : 'ti-badge text-muted' }}"></i> {{ __('Badge') }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-info">
                                            {{ substr(optional($membership->user)->first_name, 0, 1) }}{{ substr(optional($membership->user)->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <span class="fw-semibold">{{ optional($membership->user)->fullname }}</span>
                                </div>
                                <small class="text-muted">{{ optional($membership->user)->email }}</small>
                                <small class="text-muted">{{ optional($membership->user)->phone }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-info">
                                @if($membership->payment_gateway == 'manual_payment')
                                    {{ ucfirst(str_replace('_',' ',$membership->payment_gateway)) }}
                                @else
                                    {{ $membership->payment_gateway == 'authorize_dot_net' ? __('Authorize.Net') : ucfirst($membership->payment_gateway) }}
                                @endif
                            </span>
                        </td>
                        <td>
                            @if($membership->payment_status == '' || $membership->payment_status == 'cancel')
                                <span class="badge bg-danger">{{ __('Cancel') }}</span>
                            @elseif($membership->payment_status == 'pending')
                                <div>
                                    <span class="badge bg-warning mb-1">{{ ucfirst($membership->payment_status) }}</span>
                                    @can('user-membership-manual-payment-status-change')
                                        <a class="btn btn-sm btn-success edit_payment_gateway_modal"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editPaymentGatewayModal"
                                            data-membership_id="{{ $membership->id }}"
                                            data-user_firstname="{{ $membership->user?->fullname }}"
                                            data-user_email="{{ $membership->user?->email }}"
                                            data-img_url="{{ $membership->manual_payment_image }}">
                                            <i class="ti ti-pencil me-1"></i>{{ __('Update') }}
                                        </a>
                                    @endcan
                                </div>
                            @else
                                <span class="badge bg-success">{{ ucfirst($membership->payment_status) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($membership->status == 0)
                                <span class="badge bg-danger">{{ __('Inactive') }}</span>
                            @else
                                <span class="badge bg-success">{{ __('Active') }}</span>
                            @endif
                        </td>
                        <td>{{ $membership->created_at->format('Y-m-d') ?? '' }}</td>
                        <td>{{ Carbon\Carbon::parse($membership->expire_date)->format('Y-m-d') }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    @can('user-membership-status-change')
                                        <a class="dropdown-item swal_status_change" href="javascript:void(0)">
                                            <i class="ti ti-exchange me-1"></i> {{ __('Change Status') }}
                                        </a>
                                        <form action="{{ route('admin.user.membership.status', $membership->id) }}" method="post" style="display:none;">
                                            @csrf
                                            <button type="submit" class="swal_form_submit_btn"></button>
                                        </form>
                                    @endcan
                                    
                                    <a class="dropdown-item" href="{{ route('admin.user.membership.history', $membership->user_id) }}">
                                        <i class="ti ti-history me-1"></i> {{ __('History') }}
                                    </a>
                                    
                                    <a class="dropdown-item" href="{{ route('admin.user.membership.email.sent', $membership->id) }}">
                                        <i class="ti ti-mail me-1"></i> {{ __('Send Email') }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-3 custom_pagination" data-route="{{ $route }}">
                {{ $all_memberships->links() }}
            </div>
        </div>
    </div>

    <!-- Edit Payment Modal -->
    <div class="modal fade" id="editPaymentGatewayModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Update Payment Status') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form action="{{ route('admin.user.membership.update.manual.payment') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <h6 class="mb-0">{{ __('User Information') }}</h6>
                                <hr class="my-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Name') }}</label>
                                            <input type="text" class="form-control user_firstname" readonly>
                                            <input type="hidden" name="user_firstname" id="user_firstname">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Email') }}</label>
                                            <input type="text" class="form-control user_email" readonly>
                                            <input type="hidden" name="user_email" id="user_email">
                                            <input type="hidden" name="membership_id" id="membership_id">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <h6 class="mb-0">{{ __('Payment Image') }}</h6>
                                <hr class="my-3">
                                <div class="text-center">
                                    <img class="img-fluid manual_payment_img" style="max-height: 300px;" src="" alt="{{ __('Payment Screenshot') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update Payment Status') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
