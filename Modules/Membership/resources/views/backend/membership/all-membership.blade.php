{{-- resources/views/membership/backend/type/all-type.blade.php --}}
@extends('layouts/layoutMaster')

@section('title', __('All Memberships'))

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
    <script>
        $(document).ready(function () {
            // Search functionality
            $(document).on('keyup', '#string_search', function () {
                let string_search = $(this).val();
                $.ajax({
                    url: "{{ route('admin.membership.search') }}",
                    method: 'GET',
                    data: {string_search: string_search},
                    success: function (data) {
                        if (data.status == 'nothing') {
                            $('#all_membership_table').html('<tr><td colspan="8" class="text-center">{{ __('No memberships found') }}</td></tr>');
                        } else {
                            $('#all_membership_table').html(data);
                        }
                    }
                });
            });

            // Bulk action
            $(document).on('click', '.bulk_delete_btn', function (e) {
                e.preventDefault();
                let bulkOption = $('#bulk_option').val();
                if (bulkOption == 'delete') {
                    let allIds = [];
                    $("input:checkbox[name=ids]:checked").each(function () {
                        allIds.push($(this).val());
                    });
                    if (allIds.length > 0) {
                        $(this).text('{{ __('Processing...') }}');
                        $.ajax({
                            url: "{{ route('admin.membership.delete.bulk.action') }}",
                            method: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                ids: allIds
                            },
                            success: function (data) {
                                location.reload();
                            }
                        });
                    }
                }
            });

            // Check all
            $(document).on('click', '.check_all', function () {
                $('.check_item').prop('checked', $(this).is(':checked'));
            });
        });
    </script>
@endsection

@section('content')
    <!-- Membership Stats -->
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
                                <i class="ti ti-discount ti-26px"></i>
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
                                <h4 class="mb-0 me-2">{{ $all_memberships->where('status', 1)->count() ?? 0 }}</h4>
                            </div>
                            <small class="mb-0">{{ __('Active membership plans') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ti ti-discount-check ti-26px"></i>
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
                                <h4 class="mb-0 me-2">{{ $all_memberships->where('status', 0)->count() ?? 0 }}</h4>
                            </div>
                            <small class="mb-0">{{ __('Inactive membership plans') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ti ti-discount-off ti-26px"></i>
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
                            <span class="text-heading">{{ __('Membership Types') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $all_memberships->pluck('membership_type_id')->unique()->count() ?? 0 }}</h4>
                            </div>
                            <small class="mb-0">{{ __('Different membership types') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ti ti-category ti-26px"></i>
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
            <h5 class="card-title mb-0">{{ __('Memberships') }}</h5>
            <div class="d-flex justify-content-between align-items-center row pt-3 gap-4 gap-md-0">
                <div class="col-md-4">
                    <div class="d-flex">
                        <input type="text" id="string_search" class="form-control me-2" placeholder="{{ __('Search memberships...') }}">
                    </div>
                </div>
                <div class="col-md-8 text-end">
                    <a href="{{ route('admin.membership.add') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>{{ __('Add New Membership') }}
                    </a>
                    <div class="d-inline-block">
                        <select name="bulk_option" id="bulk_option" class="form-select d-inline-block w-auto me-2">
                            <option value="">{{ __('Bulk Action') }}</option>
                            <option value="delete">{{ __('Delete') }}</option>
                        </select>
                        <button class="btn btn-primary bulk_delete_btn">{{ __('Apply') }}</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th><div class="form-check"><input class="form-check-input check_all" type="checkbox"></div></th>
                        <th>#</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Listing Limit') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="all_membership_table">
                    @forelse($all_memberships as $index => $membership)
                        <tr>
                            <td><div class="form-check"><input class="form-check-input check_item" name="ids" type="checkbox" value="{{ $membership->id }}"></div></td>
                            <td>{{ $all_memberships->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="avatar-wrapper">
                                        <div class="avatar me-2">
                                            @if (!empty($membership->image))
                                                <img src="{{ asset('storage/media/membership/' . $membership->image) }}" alt="{{ __('Avatar') }}" class="rounded-circle">
                                            @else
                                                <span class="avatar-initial rounded-circle bg-label-info">
                                                    {{ substr($membership->title, 0, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-truncate fw-semibold">{{ $membership->title }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $membership->membership_type->type ?? __('N/A') }}</td>
                            <td>{{ number_format($membership->price, 2) }}</td>
                            <td>{{ $membership->listing_limit }}</td>
                            <td>
                                @if ($membership->status == 1)
                                    <span class="badge bg-label-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-label-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.membership.edit', $membership->id) }}">
                                            <i class="ti ti-pencil me-1"></i> {{ __('Edit') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.membership.status', $membership->id) }}">
                                            @if ($membership->status == 1)
                                                <i class="ti ti-ban me-1"></i> {{ __('Deactivate') }}
                                            @else
                                                <i class="ti ti-check me-1"></i> {{ __('Activate') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item text-danger" href="#" onclick="if(confirm('{{ __('Are you sure to delete this membership?') }}')) { document.getElementById('delete-form-{{ $membership->id }}').submit(); }">
                                            <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
                                        </a>
                                        <form id="delete-form-{{ $membership->id }}" action="{{ route('admin.membership.delete', $membership->id) }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">{{ __('No memberships found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $all_memberships->links() }}
            </div>
        </div>
    </div>
@endsection