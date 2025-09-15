{{-- resources/views/membership/backend/type/all-type.blade.php --}}
@extends('layouts/layoutMaster')

@section('title', __('Membership Types'))

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
        'resources/assets/vendor/libs/select2/select2.scss',
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/moment/moment.js',
        'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
        'resources/assets/vendor/libs/select2/select2.js',
    ])
@endsection

@section('page-script')
    @include('membership::backend.type.type-js')
@endsection

@section('content')

    {{-- Header cards --}}
    <div class="row g-6 mb-6">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ __('Total Types') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $total_types ?? $all_types->total() }}</h4>
                            </div>
                            <small class="mb-0">{{ __('All membership types') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ti ti-list-details ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Types List Table --}}
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">{{ __('Membership Types') }}</h5>
            <div class="d-flex justify-content-between align-items-center row pt-3 gap-4 gap-md-0">
                <div class="col-md-4">
                    <form id="searchForm" action="{{ route('admin.membership.type.search') }}" method="GET" class="d-flex">
                        <input type="text" name="string_search" id="string_search" class="form-control me-2" placeholder="{{ __('Search type...') }}">
                        <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                    </form>
                </div>
                <div class="col-md-4 text-end">
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTypeModal">
                        <i class="ti ti-plus me-1"></i>{{ __('Add New Type') }}
                    </a>
                </div>
            </div>
        </div>

        <div id="typesTable" class="card-datatable table-responsive">
            @include('membership::backend.type.search-result', ['all_types' => $all_types])
        </div>
    </div>

    {{-- Add Type Modal --}}
    <div class="modal fade" id="addTypeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.membership.type.all') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Add Membership Type') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="type" class="form-label">{{ __('Type Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="type" id="type" class="form-control" required maxlength="191">
                        </div>
                        <div class="mb-3">
                            <label for="validity" class="form-label">{{ __('Validity (days)') }} <span class="text-danger">*</span></label>
                            <input type="number" name="validity" id="validity" class="form-control" required min="7" max="365">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Type Modals --}}
    @foreach($all_types as $type)
        <div class="modal fade" id="editTypeModal{{ $type->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('admin.membership.type.edit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type_id" value="{{ $type->id }}">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Edit Membership Type') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="type_{{ $type->id }}" class="form-label">{{ __('Type Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="type" id="type_{{ $type->id }}" value="{{ $type->type }}" class="form-control" required maxlength="191">
                            </div>
                            <div class="mb-3">
                                <label for="validity_{{ $type->id }}" class="form-label">{{ __('Validity (days)') }} <span class="text-danger">*</span></label>
                                <input type="number" name="validity" id="validity_{{ $type->id }}" value="{{ $type->validity }}" class="form-control" required min="7" max="365">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

@endsection
