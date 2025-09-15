@extends('layouts/layoutMaster')

@section('title', __('Support Tickets'))

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
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js'
])
@endsection

@section('page-script')
<script>
    var createRoute = "{{ route('admin.ticket') }}";
</script>
@vite([
  // 'resources/assets/js/app-ecommerce-product-list.js'
])
<script>
    document.addEventListener('DOMContentLoaded', function() {
      // Toggle Status
      document.querySelectorAll('.toggle-status').forEach(input => {
        input.addEventListener('change', function() {
          const ticketId = this.dataset.id;
          const newStatus = this.checked ? 'open' : 'close';

          fetch(`/admin/support-ticket/change-status/${ticketId}`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: newStatus })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              this.closest('.text-truncate').querySelector('.status-value').textContent = newStatus;
              toastr.success('Status updated successfully!');
            } else {
              toastr.error('Failed to update status');
              this.checked = !this.checked;
            }
          })
          .catch(error => {
            console.error('Error:', error);
            toastr.error('An error occurred');
            this.checked = !this.checked;
          });
        });
      });

      // Delete ticket
      document.querySelectorAll('.delete-ticket').forEach(button => {
        button.addEventListener('click', function() {
          const ticketId = this.dataset.id;
          if (confirm('{{ __("Are you sure you want to delete this ticket?") }}')) {
            fetch(`/admin/support-ticket/delete-ticket/${ticketId}`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              }
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                toastr.success('Ticket deleted successfully!');
                setTimeout(() => {
                  window.location.reload();
                }, 1000);
              } else {
                toastr.error('Failed to delete ticket');
              }
            })
            .catch(error => {
              console.error('Error:', error);
              toastr.error('An error occurred');
            });
          }
        });
      });

      // Bulk delete
      document.getElementById('bulk-action-btn').addEventListener('click', function() {
        const selectedIds = [];
        document.querySelectorAll('.form-check-input:checked').forEach(checkbox => {
          if (checkbox.value) {
            selectedIds.push(checkbox.value);
          }
        });

        if (selectedIds.length === 0) {
          toastr.warning('{{ __("Please select at least one ticket") }}');
          return;
        }

        if (confirm('{{ __("Are you sure you want to delete selected tickets?") }}')) {
          fetch('{{ route("admin.ticket.delete.bulk.action") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: selectedIds })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              toastr.success('Selected tickets deleted successfully!');
              setTimeout(() => {
                window.location.reload();
              }, 1000);
            } else {
              toastr.error('Failed to delete selected tickets');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            toastr.error('An error occurred');
          });
        }
      });
    });
</script>
@endsection

@section('content')
@if(session()->has('msg'))
    <div class="alert alert-{{session('type') ?? 'success'}}">
        {{ session('msg') }}
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="app-ecommerce-product">
  <!-- New Ticket Modal -->
  <div class="modal fade" id="newTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-simple">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3 class="mb-2">{{ __('Create New Support Ticket') }}</h3>
          </div>
          <form action="{{ route('admin.ticket') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-12">
              <label class="form-label" for="title">{{ __('Title') }}</label>
              <input type="text" id="title" name="title" class="form-control" placeholder="{{ __('Support ticket title') }}" required />
            </div>
            <div class="col-12">
              <label class="form-label" for="department">{{ __('Department') }}</label>
              <select id="department" name="department" class="select2 form-select" required>
                <option value="">{{ __('Select Department') }}</option>
                @foreach($departments as $department)
                  <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label" for="user">{{ __('User') }}</label>
              <select id="user" name="user" class="select2 form-select" required>
                <option value="">{{ __('Select User') }}</option>
                @foreach($users as $user)
                  <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->username }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label" for="priority">{{ __('Priority') }}</label>
              <select id="priority" name="priority" class="form-select">
                <option value="high">{{ __('High') }}</option>
                <option value="medium" selected>{{ __('Medium') }}</option>
                <option value="low">{{ __('Low') }}</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label" for="description">{{ __('Description') }}</label>
              <textarea id="description" name="description" class="form-control" rows="4" placeholder="{{ __('Ticket description') }}" required></textarea>
            </div>
            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ __('Submit') }}</button>
              <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">{{ __('Cancel') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!--/ New Ticket Modal -->

  <div class="card">
    <!-- Header -->
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
      <h5 class="m-0">{{ __('Support Tickets') }}</h5>
      <div class="d-flex">
        <button class="btn btn-outline-secondary me-2" id="bulk-action-btn">{{ __('Delete Selected') }}</button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTicketModal">
          <i class="ti ti-plus me-sm-1"></i>
          <span class="d-none d-sm-inline-block">{{ __('New Ticket') }}</span>
        </button>
      </div>
    </div>
    <!-- Search -->
    <div class="card-body border-bottom">
      <form action="{{ route('admin.ticket.search') }}" method="POST" id="search-form">
        @csrf
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
          <div class="col-md-4 user_status">
            <select id="status-filter" class="form-select text-capitalize">
              <option value="">{{ __('All Status') }}</option>
              <option value="open">{{ __('Open') }}</option>
              <option value="close">{{ __('Closed') }}</option>
            </select>
          </div>
          <div class="col-md-4 priority">
            <select id="priority-filter" class="form-select text-capitalize">
              <option value="">{{ __('All Priority') }}</option>
              <option value="high">{{ __('High') }}</option>
              <option value="medium">{{ __('Medium') }}</option>
              <option value="low">{{ __('Low') }}</option>
            </select>
          </div>
          <div class="col-md-4">
            <div class="input-group input-group-merge">
              <span class="input-group-text" id="search-icon"><i class="ti ti-search"></i></span>
              <input type="text" name="string_search" id="search-input" class="form-control" placeholder="{{ __('Search by ticket ID, title, or user') }}" aria-label="{{ __('Search...') }}" aria-describedby="search-icon">
            </div>
          </div>
        </div>
      </form>
    </div>
    
    <div class="card-datatable table-responsive">
      <table class="datatables-products table border-top" id="ticket-table">
        <thead>
          <tr>
            <th>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="select-all">
              </div>
            </th>
            <th>{{ __('ID') }}</th>
            <th>{{ __('Title') }}</th>
            <th>{{ __('User') }}</th>
            <th>{{ __('Department') }}</th>
            <th>{{ __('Priority') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Created At') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody id="ticket-list">
          @foreach ($tickets as $ticket)
          <tr>
            <td>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="{{ $ticket->id }}">
              </div>
            </td>
            <td>#{{ $ticket->id }}</td>
            <td>
              <div class="d-flex justify-content-start align-items-center product-name">
                <div class="d-flex flex-column">
                  <h6 class="text-nowrap mb-0">{{ $ticket->title }}</h6>
                </div>
              </div>
            </td>
            <td>
              <span class="text-truncate">
                @if ($ticket->user)
                  {{ $ticket->user->first_name }} {{ $ticket->user->last_name }}
                @else
                  {{ __('User Deleted') }}
                @endif
              </span>
            </td>
            <td>
              <span class="text-truncate">
                @php
                  $department = \Modules\SupportTicket\app\Models\Department::find($ticket->department_id);
                @endphp
                {{ $department ? $department->name : __('Not Found') }}
              </span>
            </td>
            <td>
              <span class="badge bg-label-{{ $ticket->priority == 'high' ? 'danger' : ($ticket->priority == 'medium' ? 'warning' : 'success') }} me-1">
                {{ ucfirst($ticket->priority) }}
              </span>
            </td>
            <td>
              <span class="text-truncate">
                <label class="switch switch-primary switch-sm">
                  <input
                    type="checkbox"
                    class="switch-input toggle-status"
                    data-id="{{ $ticket->id }}"
                    {{ $ticket->status == 'open' ? 'checked' : '' }}
                  >
                  <span class="switch-toggle-slider">
                    <span class="switch-on"></span>
                    <span class="switch-off"></span>
                  </span>
                </label>
                <span class="d-none status-value">{{ $ticket->status }}</span>
              </span>
            </td>
            <td>{{ $ticket->created_at->format('d M Y') }}</td>
            <td>
              <div class="d-inline-block text-nowrap">
                <a href="{{ route('admin.ticket.details', $ticket->id) }}" class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light">
                  <i class="ti ti-eye"></i>
                </a>
                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect waves-light dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="ti ti-dots-vertical ti-md"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end m-0">
                  <a href="{{ route('admin.ticket.details', $ticket->id) }}" class="dropdown-item">{{ __('View Details') }}</a>
                  <button class="dropdown-item text-danger delete-ticket" data-id="{{ $ticket->id }}">{{ __('Delete') }}</button>
                </div>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <!-- Pagination -->
    <div class="card-footer d-flex justify-content-center">
      {{ $tickets->links() }}
    </div>
  </div>
</div>
@endsection