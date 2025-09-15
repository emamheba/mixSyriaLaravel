@extends('layouts/layoutMaster')

@section('title', __('All Enquiries - Pages'))

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
    @include('membership::backend.enquiry.enquiry-js')
@endsection

@section('content')

<div class="row g-6 mb-6">
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="text-heading">{{ __('Total Enquiries') }}</span>
                        <div class="d-flex align-items-center my-1">
                            <h4 class="mb-0 me-2">{{ $all_enquiries->total() }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="ti ti-message-circle ti-26px"></i>
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
                        <span class="text-heading">{{ __('Todays Enquiries') }}</span>
                        <div class="d-flex align-items-center my-1">
                            <h4 class="mb-0 me-2">{{ $all_enquiries->where('created_at', '>=', \Carbon\Carbon::today())->count() }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="ti ti-calendar-event ti-26px"></i>
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
                        <span class="text-heading">{{ __('This Week') }}</span>
                        <div class="d-flex align-items-center my-1">
                            <h4 class="mb-0 me-2">{{ $all_enquiries->where('created_at', '>=', \Carbon\Carbon::now()->startOfWeek())->count() }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="ti ti-calendar-stats ti-26px"></i>
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
                        <span class="text-heading">{{ __('This Month') }}</span>
                        <div class="d-flex align-items-center my-1">
                            <h4 class="mb-0 me-2">{{ $all_enquiries->where('created_at', '>=', \Carbon\Carbon::now()->startOfMonth())->count() }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="ti ti-calendar ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('All Enquiries') }}</h5>
        <div class="d-flex justify-content-between align-items-center row pt-3 gap-4 gap-md-0">
            <div class="col-md-4">
                <div class="d-flex">
                    <input type="text" id="string_search" class="form-control me-2" placeholder="{{ __('Search enquiries...') }}">
                </div>
            </div>
            <div class="col-md-4 text-end">
                @can('enquiry-form-bulk-delete')
                    <div class="bulk-delete-wrapper">
                        <div class="select-all-wrapper">
                            <input type="checkbox" class="select-all" id="select_all">
                            <label for="select_all">{{ __('Select All') }}</label>
                        </div>
                        <button class="btn btn-danger btn-sm bulk-delete-btn">{{ __('Bulk Delete') }}</button>
                    </div>
                @endcan
            </div>
        </div>
    </div>
    <div class="card-datatable table-responsive search_result">
        @include('membership::backend.enquiry.search-result')
    </div>
</div>

<form action="{{ route('admin.enquiry.form.delete.bulk.action') }}" method="post" class="bulk_delete_form">
    @csrf
    <input type="hidden" name="ids" id="bulk_delete_ids">
</form>

@endsection
