@extends('layouts/layoutMaster')

@section('title', __('Trashed Users - User Management'))

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/moment/moment.js',
        'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'
    ])
@endsection

@section('page-script')
<script>
    // Add CSRF token to all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Search functionality
    $(document).ready(function() {
        $(document).on('keyup', '#search_trashed_user', function(e) {
            e.preventDefault();
            let search_string = $(this).val();

            $.ajax({
                url: "{{ route('admin.users.trash.search') }}",
                method: "GET",
                data: {
                    string_search: search_string
                },
                success: function(data) {
                    if (data.status == 'nothing') {
                        $('#all_deleted_users').html('<tr><td colspan="6" class="text-center">{{ __('No users found') }}</td></tr>');
                    } else {
                        $('#all_deleted_users').html(data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                }
            });
        });
    });

    // Pagination
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        pagination_deleted_users(page);
    });

    function pagination_deleted_users(page) {
        $.ajax({
            url: "{{ route('admin.users.trash.pagination') }}?page=" + page,
            success: function(data) {
                $('#all_deleted_users').html(data);
            },
            error: function(xhr) {
                console.error('Pagination error:', xhr.responseText);
            }
        });
    }
</script>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">{{ __('Deleted Users') }}</h5>
                <div class="d-flex justify-content-between align-items-center row pt-3 gap-4 gap-md-0">
                    <div class="col-md-4">
                        <div class="d-flex">
                            <input type="text" id="search_trashed_user" class="form-control me-2" placeholder="{{ __('Search users...') }}">
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('admin.users.page') }}" class="btn btn-primary">
                            <i class="ti ti-users me-1"></i> {{ __('All Users') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="table border-top">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Deleted At') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="all_deleted_users">
                        @forelse($all_users as $index => $user)
                            <tr>
                                <td>{{ $all_users->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center">
                                        <div class="avatar-wrapper">
                                            <div class="avatar me-2">
                                                @if (!empty($user->image))
                                                    <img src="{{ asset('storage/media/user/' . $user->image) }}" alt="{{ __('Avatar') }}" class="rounded-circle">
                                                @else
                                                    <span class="avatar-initial rounded-circle bg-label-info">
                                                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-truncate fw-semibold">{{ $user->first_name }} {{ $user->last_name }}</span>
                                            <small class="text-muted">@{{ $user->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ $user->deleted_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="#" class="btn btn-sm btn-icon btn-success me-2" onclick="if(confirm('{{ __('Are you sure to restore this user?') }}')) { document.getElementById('restore-form-{{ $user->id }}').submit(); }">
                                            <i class="ti ti-refresh"></i>
                                        </a>
                                        <form id="restore-form-{{ $user->id }}" action="{{ route('admin.users.restore', $user->id) }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>

                                        <a href="#" class="btn btn-sm btn-icon btn-danger" onclick="if(confirm('{{ __('Are you sure to permanently delete this user? This action cannot be undone!') }}')) { document.getElementById('permanent-delete-form-{{ $user->id }}').submit(); }">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                        <form id="permanent-delete-form-{{ $user->id }}" action="{{ route('admin.users.permanent.delete', $user->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    {{ __('No deleted users found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $all_users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection