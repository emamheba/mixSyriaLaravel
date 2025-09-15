@extends('layouts/layoutMaster')

@section('title', 'User Management - Dashboard')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
           'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
           'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
           'resources/assets/vendor/libs/select2/select2.scss',
           'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js',
           'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
           'resources/assets/vendor/libs/select2/select2.js',
           'resources/assets/vendor/libs/@form-validation/popular.js',
           'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
           'resources/assets/vendor/libs/@form-validation/auto-focus.js',
           'resources/assets/vendor/libs/cleavejs/cleave.js',
           'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
@endsection

@section('page-script')
    @include('backend.pages.user.users.user-js')
@endsection

@section('content')
    <!-- Dashboard Stats -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium d-block mb-1 text-primary">{{ __('Total Users') }}</span>
                            <div class="d-flex align-items-baseline mt-2">
                                <h4 class="mb-0 me-2">{{ $total_users ?? 0 }}</h4>
                                @if(isset($user_growth_rate) && $user_growth_rate > 0)
                                    <span class="badge bg-label-success rounded-pill">
                                        <i class="ti ti-chevron-up ti-xs"></i> {{ $user_growth_rate }}%
                                    </span>
                                @endif
                            </div>
                            <small class="d-block mt-1">{{ __('All registered accounts') }}
                            </small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial bg-label-primary rounded">
                                <i class="ti ti-users"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium d-block mb-1 text-success">{{ __('Verified Users') }}</span>
                            <div class="d-flex align-items-baseline mt-2">
                                <h4 class="mb-0 me-2">{{ $verified_users ?? 0 }}</h4>
                                @if(isset($verified_percentage))
                                    <span class="badge bg-label-info rounded-pill">
                                        {{ $verified_percentage }}%
                                    </span>
                                @endif
                            </div>
                            <small class="d-block mt-1">{{ __('Email verified accounts') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial bg-label-success rounded">
                                <i class="ti ti-user-check"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium d-block mb-1 text-info">{{ __('Active Users') }}</span>
                            <div class="d-flex align-items-center mt-2">
                                <h4 class="mb-0 me-2">{{ $active_users ?? 0 }}</h4>
                                @if(isset($active_percentage))
                                    <span class="badge bg-label-success rounded-pill">
                                        {{ $active_percentage }}%
                                    </span>
                                @endif
                            </div>
                            <small class="d-block mt-1">{{ __('Currently active users') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial bg-label-info rounded">
                                <i class="ti ti-user-plus"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card card-border-shadow-warning h-100">
              <div class="card-body">
                  <div class="d-flex align-items-start justify-content-between">
                      <div class="content-left">
                          <span class="fw-medium d-block mb-1 text-warning">{{ __('Inactive Users') }}</span>
                          <div class="d-flex align-items-center mt-2">
                              <h4 class="mb-0 me-2">{{ $inactive_users ?? 0 }}</h4>
                              @if(isset($inactive_percentage))
                                  <span class="badge bg-label-warning rounded-pill">
                                      {{ $inactive_percentage }}%
                                  </span>
                              @endif
                          </div>
                          <small class="d-block mt-1">{{ __('Users requiring attention') }}</small>
                      </div>
                      <div class="avatar">
                          <span class="avatar-initial bg-label-warning rounded">
                              <i class="ti ti-user-exclamation"></i>
                          </span>
                      </div>
                  </div>
              </div>
          </div>
      </div>


  <!-- Users List Card -->
<div class="card">
  <div class="card-header border-bottom">
      <div class="card-title mb-0">
          <h5 class="m-0">{{ __('Users Management') }}</h5>
          <small class="text-muted">{{ __('Manage your system users') }}</small>
      </div>
      <div class="card-actions">
          <div class="d-flex align-items-center gap-3">
              <div class="dropdown">
                  <button
                      class="btn btn-label-primary dropdown-toggle"
                      type="button"
                      id="userFilterDropdown"
                      data-bs-toggle="dropdown"
                      aria-expanded="false"
                  >
                      <i class="ti ti-filter me-1"></i> {{ __('Filter') }}
                  </button>
                  <ul class="dropdown-menu" aria-labelledby="userFilterDropdown">
                      {{-- All Users: remove both status & verified --}}
                      <li>
                          <a
                              class="dropdown-item"
                              href="{{ route('admin.users.page', request()->except(['status','verified'])) }}"
                          >
                              {{ __('All Users') }}
                          </a>
                      </li>

                      {{-- Status-only filters --}}
                      <li>
                          <a
                              class="dropdown-item"
                              href="{{ route('admin.users.page', array_merge(request()->except('status'), ['status' => 1])) }}"
                          >
                              {{ __('Active Users') }}
                          </a>
                      </li>
                      <li>
                          <a
                              class="dropdown-item"
                              href="{{ route('admin.users.page', array_merge(request()->except('status'), ['status' => 0])) }}"
                          >
                              {{ __('Inactive Users') }}
                          </a>
                      </li>

                      <li><hr class="dropdown-divider"></li>

                      {{-- Verified-only filters --}}
                      <li>
                          <a
                              class="dropdown-item"
                              href="{{ route('admin.users.page', array_merge(request()->except('verified'), ['verified' => 1])) }}"
                          >
                              {{ __('Verified Users') }}
                          </a>
                      </li>
                      <li>
                          <a
                              class="dropdown-item"
                              href="{{ route('admin.users.page', array_merge(request()->except('verified'), ['verified' => 0])) }}"
                          >
                              {{ __('Unverified Users') }}
                          </a>
                      </li>
                  </ul>
              </div>

              <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                  <i class="ti ti-plus me-1"></i> {{ __('Add New User') }}
              </a>
          </div>
      </div>
  </div>


<div class="card-body border-bottom">
  <form id="userFilterForm" action="{{ route('admin.users.page') }}" method="GET" class="row g-3 align-items-end">
    <div class="col-md-4">
      <div class="input-group">
        <span class="input-group-text"><i class="ti ti-search"></i></span>
        <input
          id="userSearch"
          type="text"
          name="string_search"
          class="form-control"
          placeholder="{{ __('Search by name, email or phone...') }}"
          value="{{ request('string_search') }}"
          autocomplete="off"
        >
      </div>
    </div>

    <div class="col-md-2">
      <select name="status" class="form-select">
        <option value="">{{ __('-- Select Status --') }}</option>
        <option value="1" {{ request('status')=='1'?'selected':'' }}>{{ __('Active') }}</option>
        <option value="0" {{ request('status')=='0'?'selected':'' }}>{{ __('Inactive') }}</option>
      </select>
    </div>

    <div class="col-md-2">
      <select name="verified" class="form-select">
        <option value="">{{ __('-- Select Verification --') }}</option>
        <option value="1" {{ request('verified')=='1'?'selected':'' }}>{{ __('Verified') }}</option>
        <option value="0" {{ request('verified')=='0'?'selected':'' }}>{{ __('Unverified') }}</option>
      </select>
    </div>

    <div class="col-md-4 d-flex gap-2">
      <button type="submit" class="btn btn-primary w-50">{{ __('Search') }}</button>
      <a href="{{ route('admin.users.page') }}" class="btn btn-outline-secondary w-50">{{ __('Reset') }}</a>
    </div>
  </form>
</div>


        <div class="card-datatable table-responsive">
            <table class="table border-top dataTable">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('Contact Information') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Verification') }}</th>
                    <th>{{ __('Registered On') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                    @forelse($all_users as $index => $user)
                        <tr>
                            <td>{{ $all_users->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center user-name">
                                    <div class="avatar-wrapper">
                                        <div class="avatar me-2">
                                            @if (!empty($user->image))
                                                <img src="{{ asset('storage/media/user/' . $user->image) }}" alt="Avatar" class="rounded-circle">
                                            @else
                                                <span class="avatar-initial rounded-circle bg-label-{{ ['primary', 'success', 'danger', 'warning', 'info'][array_rand(['primary', 'success', 'danger', 'warning', 'info'])] }}">
                                                    {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">{{ $user->first_name }} {{ $user->last_name }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-truncate">
                                        <i class="ti ti-mail text-muted me-1"></i> {{ $user->email }}
                                    </span>
                                    @if($user->phone)
                                        <span class="text-truncate">
                                            <i class="ti ti-phone text-muted me-1"></i> {{ $user->phone }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                              @if ($user->status == 1)
                              <span class="badge bg-label-success">{{ __('Active') }}</span>
                          @else
                              <span class="badge bg-label-danger">{{ __('Inactive') }}</span>
                          @endif
                            </td>
                            <td>
                              @if ($user->email_verified == 1)
                                  <span class="badge bg-label-success">
                                      {{ __('Verified') }} <i class="ti ti-check-circle ms-1"></i>
                                  </span>
                              @else
                                  <span class="badge bg-label-warning">
                                      {{ __('Pending') }} <i class="ti ti-clock ms-1"></i>
                                  </span>
                              @endif
                          </td>
                            <td>
                                <span>{{ $user->created_at->format('M d, Y') }}</span>
                                <small class="d-block text-muted">{{ $user->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-icon dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                          <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewUserModal{{ $user->id }}">
                                            <i class="ti ti-eye me-1"></i> {{ __('View Details') }}
                                        </a>
                                        </li>
                                        <li>
                                          <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                            <i class="ti ti-pencil me-1"></i> {{ __('Edit') }}
                                        </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.users.change.status', $user->id) }}">
                                              @if ($user->status == 1)
                                              <i class="ti ti-ban me-1"></i> {{ __('Deactivate') }}
                                          @else
                                              <i class="ti ti-check me-1"></i> {{ __('Activate') }}
                                          @endif
                                            </a>
                                        </li>
                                        @if ($user->email_verified == 0)
                                            <li>
                                              <a class="dropdown-item" href="{{ route('admin.users.verify.email', $user->id) }}">
                                                <i class="ti ti-mail-check me-1"></i> {{ __('Verify Email') }}
                                            </a>
                                            </li>
                                        @endif
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                          <a class="dropdown-item text-danger delete-user" href="javascript:void(0);" data-id="{{ $user->id }}">
                                            <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
                                        </a>
                                            <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.delete', $user->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>

                      <!-- View User Modal -->
<div class="modal fade" id="viewUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">{{ __('User Details') }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-4 text-center mb-3">
                      <div class="avatar avatar-xl mb-3 mx-auto">
                          @if (!empty($user->image))
                              <img src="{{ asset('storage/media/user/' . $user->image) }}" alt="Avatar" class="rounded-circle">
                          @else
                              <span class="avatar-initial rounded-circle bg-label-primary">
                                  {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                              </span>
                          @endif
                      </div>
                      <h5 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h5>
                      <p class="text-muted">@{{ $user->username }}</p>

                      <div class="d-flex justify-content-center mt-3">
                          @if ($user->status == 1)
                              <span class="badge bg-label-success me-2">{{ __('Active') }}</span>
                          @else
                              <span class="badge bg-label-danger me-2">{{ __('Inactive') }}</span>
                          @endif

                          @if ($user->email_verified == 1)
                              <span class="badge bg-label-success">{{ __('Verified') }}</span>
                          @else
                              <span class="badge bg-label-warning">{{ __('Pending') }}</span>
                          @endif
                      </div>
                  </div>


                                            <div class="col-md-8">
                                                <div class="card">
                                                    <div class="card-body p-0">
                                                        <ul class="list-group list-group-flush">
                                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                              <div>
                                                                <i class="ti ti-mail text-primary me-2"></i>
                                                                <span>{{ __('Email') }}</span>
                                                            </div>
                                                                <span>{{ $user->email }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                              <div>
                                                                <i class="ti ti-phone text-primary me-2"></i>
                                                                <span>{{ __('Phone') }}</span>
                                                            </div>
                                                            <span>{{ $user->phone ?? __('Not provided') }}</span>
                                                          </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                              <div>
                                                                <i class="ti ti-calendar text-primary me-2"></i>
                                                                <span>{{ __('Registration Date') }}</span>
                                                            </div>
                                                                <span>{{ $user->created_at->format('M d, Y h:i A') }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                              <div>
                                                                <i class="ti ti-login text-primary me-2"></i>
                                                                <span>{{ __('Last Login') }}</span>
                                                            </div>
                                                            <span>{{ $user->last_login ?? __('Never logged in') }}</span>
                                                          </li>
                                                            @if(isset($user->address))
                                                                <li class="list-group-item px-0">
                                                                    <div class="mb-2">
                                                                        <i class="ti ti-map-pin text-primary me-2"></i>
                                                                        <span>{{ __('Address') }}</span>                                                                    </div>
                                                                    <span>{{ $user->address }}</span>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                      <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" data-bs-dismiss="modal">
                                          <i class="ti ti-pencil me-1"></i> {{ __('Edit User') }}
                                      </a>
                                  </div>

                                </div>
                            </div>
                        </div>

                        <!-- Include Edit User Modal -->
                        @include('backend.pages.user.users.edit-user-modal')
                    @empty
                    <tr>
                      <td colspan="7" class="text-center">
                          <div class="d-flex flex-column align-items-center py-4">
                              <i class="ti ti-user-off text-secondary" style="font-size: 3rem;"></i>
                              <h5 class="mt-2">{{ __('No users found') }}</h5>
                              <p class="mb-0 text-muted">{{ __('Try adjusting your search criteria or add a new user') }}</p>
                              <a href="#" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                  <i class="ti ti-plus me-1"></i> {{ __('Add New User') }}
                              </a>
                          </div>
                      </td>
                  </tr>

                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4 mb-2">
                {{ $all_users->links() }}
            </div>
        </div>
    </div>

    <!-- Include Add User Modal -->
    @include('backend.pages.user.users.add-user-modal')

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header bg-danger">
                  <h5 class="modal-title text-white">{{ __('Confirm Delete') }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
              </div>
              <div class="modal-body">
                  <div class="text-center mb-4">
                      <i class="ti ti-alert-circle text-danger" style="font-size: 3rem;"></i>
                      <h4 class="mt-2">{{ __('Are you sure?') }}</h4>
                      <p class="text-muted">{{ __("You won't be able to revert this action") }}</p>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                  <button type="button" class="btn btn-danger" id="confirmDelete">{{ __('Yes, delete it!') }}</button>
              </div>
          </div>
      </div>
  </div>

@endsection
