{{-- resources/views/modules/countrymanage/state/search-result.blade.php --}}

<table id="states-table" class="table border-top">
  <thead>
    <tr>
      <th width="50">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="select-all">
          <label class="form-check-label" for="select-all"></label>
        </div>
      </th>
      <th width="80">{{ __('ID') }}</th>
      <th>{{ __('State Name') }}</th>
      <th width="150">{{ __('Timezone') }}</th>
      <th width="100">{{ __('Status') }}</th>
      <th width="120">{{ __('Actions') }}</th>
    </tr>
  </thead>
  <tbody>
    @forelse($all_states as $state)
    <tr>
      <td>
        <div class="form-check">
          <input class="form-check-input state-checkbox" type="checkbox" value="{{ $state->id }}">
        </div>
      </td>
      <td>
        <span class="badge bg-label-secondary">{{ $state->id }}</span>
      </td>
      <td>
        <div class="d-flex align-items-center">
          <div class="avatar avatar-sm me-3">
            <span class="avatar-initial bg-label-primary rounded">
              <i class="ti ti-map-pin"></i>
            </span>
          </div>
          <div>
            <h6 class="mb-0">{{ $state->state }}</h6>
            <small class="text-muted">{{ __('Created') }}: {{ $state->created_at->format('M d, Y') }}</small>
          </div>
        </div>
      </td>
      <td>
        <span class="badge bg-label-info">{{ $state->timezone }}</span>
      </td>
      <td>
        <form action="{{ route('admin.state.status', $state->id) }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-sm {{ $state->status == 1 ? 'btn-success' : 'btn-warning' }} status-btn">
            <i class="ti ti-{{ $state->status == 1 ? 'check' : 'clock' }} me-1"></i>
            {{ $state->status == 1 ? __('Active') : __('Inactive') }}
          </button>
        </form>
      </td>
      <td>
        <div class="dropdown">
          <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
            <i class="ti ti-dots-vertical"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item edit-state" href="#" data-bs-toggle="modal" data-bs-target="#editStateModal"
               data-id="{{ $state->id }}" data-state="{{ $state->state }}" data-timezone="{{ $state->timezone }}">
              <i class="ti ti-pencil me-1"></i> {{ __('Edit') }}
            </a>
            <div class="dropdown-divider"></div>
            <form id="delete-form-{{ $state->id }}" action="{{ route('admin.delete.state', $state->id) }}" method="POST" class="d-none">
              @csrf
            </form>
            <a class="dropdown-item text-danger delete-item" href="#" data-id="{{ $state->id }}">
              <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
            </a>
          </div>
        </div>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="6" class="text-center">
        <div class="my-5">
          <div class="avatar avatar-xl mx-auto mb-3">
            <span class="avatar-initial bg-label-secondary rounded">
              <i class="ti ti-map-off display-6"></i>
            </span>
          </div>
          <h5 class="mb-1">{{ __('No states found') }}</h5>
          <p class="text-muted mb-3">{{ __('Start by adding your first state') }}</p>
          <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addStateForm">
            <i class="ti ti-plus me-1"></i>{{ __('Add New State') }}
          </button>
        </div>
      </td>
    </tr>
    @endforelse
  </tbody>
</table>

@if($all_states->hasPages())
<div class="card-footer">
  <div class="d-flex justify-content-between align-items-center">
    <small class="text-muted">
      {{ __('Showing :from to :to of :total results', [
        'from' => $all_states->firstItem(),
        'to' => $all_states->lastItem(),
        'total' => $all_states->total()
      ]) }}
    </small>
    {{ $all_states->links() }}
  </div>
</div>
@endif