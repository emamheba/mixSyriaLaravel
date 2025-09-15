@extends('layouts/layoutMaster')

@section('title', 'States Management')

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

@section('content')

<!-- States Stats Widget -->
<div class="card mb-6">
  <div class="card-widget-separator-wrapper">
    <div class="card-body card-widget-separator">
      <div class="row gy-4 gy-sm-1">
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $all_states->where('status', 1)->count() }}</h4>
              <p class="mb-0">{{ __('Active States') }}</p>
            </div>
            <span class="avatar me-sm-6">
              <span class="avatar-initial bg-label-success rounded text-heading">
                <i class="ti-26px ti ti-map-pin text-heading"></i>
              </span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none me-6">
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $all_states->where('status', 0)->count() }}</h4>
              <p class="mb-0">{{ __('Inactive States') }}</p>
            </div>
            <span class="avatar p-2 me-lg-6">
              <span class="avatar-initial bg-label-warning rounded"><i class="ti-26px ti ti-map-off text-heading"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none">
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start border-end pb-4 pb-sm-0 card-widget-3">
            <div>
              <h4 class="mb-0">{{ $all_states->total() }}</h4>
              <p class="mb-0">{{ __('Total States') }}</p>
            </div>
            <span class="avatar p-2 me-sm-6">
              <span class="avatar-initial bg-label-primary rounded"><i class="ti-26px ti ti-map text-heading"></i></span>
            </span>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h4 class="mb-0">{{ \Modules\CountryManage\app\Models\City::whereHas('state', function($q) { $q->where('country_id', 1); })->count() }}</h4>
              <p class="mb-0">{{ __('Total Cities') }}</p>
            </div>
            <span class="avatar p-2">
              <span class="avatar-initial bg-label-info rounded"><i class="ti-26px ti ti-building-community text-heading"></i></span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add New State -->
<div class="card mb-4">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">
        <i class="ti ti-plus me-2"></i>{{ __('Add New State') }}
      </h5>
      <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#addStateForm">
        <i class="ti ti-chevron-down"></i>
      </button>
    </div>
  </div>
  <div class="collapse" id="addStateForm">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.all.state') }}">
        @csrf
        <div class="row g-4">
          <div class="col-md-4">
            <label class="form-label" for="state">{{ __('State Name') }} <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti ti-map-pin"></i></span>
              <input type="text" class="form-control @error('state') is-invalid @enderror" 
                     id="state" name="state" placeholder="{{ __('Enter state name') }}" 
                     value="{{ old('state') }}" required>
            </div>
            @error('state')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
          
          {{-- <div class="col-md-4">
            <label class="form-label" for="timezone">{{ __('Timezone') }} <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti ti-clock"></i></span>
              <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone" required>
                <option value="">{{ __('Select Timezone') }}</option>
                <option value="UTC" {{ old('timezone') == 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                <option value="Asia/Dubai" {{ old('timezone') == 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GMT+4)</option>
                <option value="Asia/Riyadh" {{ old('timezone') == 'Asia/Riyadh' ? 'selected' : '' }}>Asia/Riyadh (GMT+3)</option>
                <option value="Europe/London" {{ old('timezone') == 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT+0)</option>
                <option value="America/New_York" {{ old('timezone') == 'America/New_York' ? 'selected' : '' }}>America/New_York (GMT-5)</option>
              </select>
            </div>
            @error('timezone')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div> --}}
          
          <div class="col-md-2">
            <label class="form-label" for="status">{{ __('Status') }}</label>
            <select class="form-select" id="status" name="status">
              <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
              <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
            </select>
          </div>
          
          <div class="col-md-2">
            <label class="form-label d-block">&nbsp;</label>
            <button type="submit" class="btn btn-primary w-100">
              <i class="ti ti-plus me-1"></i>{{ __('Add State') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="card-title mb-0">
      <i class="ti ti-search me-2"></i>{{ __('Search & Filters') }}
    </h5>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-8">
        <label class="form-label">{{ __('Search States') }}</label>
        <div class="input-group">
          <span class="input-group-text"><i class="ti ti-search"></i></span>
          <input type="text" class="form-control" id="search-input" 
                 placeholder="{{ __('Search by state name...') }}">
          <button class="btn btn-outline-secondary" type="button" id="clear-search">
            <i class="ti ti-x"></i>
          </button>
        </div>
      </div>
      
      <div class="col-md-2">
        <label class="form-label">{{ __('Status Filter') }}</label>
        <select class="form-select" id="status-filter">
          <option value="all">{{ __('All Status') }}</option>
          <option value="1">{{ __('Active') }}</option>
          <option value="0">{{ __('Inactive') }}</option>
        </select>
      </div>
      
      <div class="col-md-2">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-outline-primary w-100" id="reset-filters">
          <i class="ti ti-refresh me-1"></i>{{ __('Reset') }}
        </button>
      </div>
    </div>
  </div>
</div>

<!-- States Table -->
<div class="card">
  <div class="card-header border-bottom">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h5 class="card-title mb-0">
          <i class="ti ti-list me-2"></i>{{ __('States Management') }}
        </h5>
        <small class="text-muted">{{ __('Manage all states and their settings') }}</small>
      </div>
      
      <div class="d-flex gap-2">
        <!-- Bulk Actions -->
        <div class="d-flex">
          <select id="bulk_option" class="form-select form-select-sm">
            <option value="">{{ __('Bulk Action') }}</option>
            <option value="delete">{{ __('Delete Selected') }}</option>
            <option value="activate">{{ __('Activate Selected') }}</option>
            <option value="deactivate">{{ __('Deactivate Selected') }}</option>
          </select>
          <button class="btn btn-sm btn-primary ms-2" id="bulk_action_btn">{{ __('Apply') }}</button>
        </div>
      </div>
    </div>
  </div>
  
  <div class="card-datatable table-responsive">
    <div id="search-results">
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
            {{-- <th>{{ __('Timezone') }}</th> --}}
            <th width="120">{{ __('Cities Count') }}</th>
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
            {{-- <td>
              <span class="badge bg-label-info">{{ $state->timezone }}</span>
            </td> --}}
            <td>
              <div class="d-flex align-items-center">
                <i class="ti ti-building-community me-1"></i>
                <span class="fw-medium">{{ $state->cities->count() }}</span>
              </div>
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
            <td colspan="7" class="text-center">
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
    </div>
    
    <!-- Pagination -->
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
  </div>
</div>

<!-- Edit State Modal -->
<div class="modal fade" id="editStateModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="ti ti-pencil me-2"></i>{{ __('Edit State') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.edit.state') }}" method="POST">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="state_id" id="edit_state_id">
          
          <div class="mb-4">
            <label class="form-label" for="edit_state">{{ __('State Name') }} <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti ti-map-pin"></i></span>
              <input type="text" class="form-control" id="edit_state" name="edit_state" required>
            </div>
          </div>
          
          {{-- <div class="mb-4">
            <label class="form-label" for="edit_timezone">{{ __('Timezone') }} <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti ti-clock"></i></span>
              <select class="form-select" id="edit_timezone" name="edit_timezone" required>
                <option value="UTC">UTC (GMT+0)</option>
                <option value="Asia/Dubai">Asia/Dubai (GMT+4)</option>
                <option value="Asia/Riyadh">Asia/Riyadh (GMT+3)</option>
                <option value="Europe/London">Europe/London (GMT+0)</option>
                <option value="America/New_York">America/New_York (GMT-5)</option>
              </select>
            </div>
          </div>
        </div> --}}
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="ti ti-x me-1"></i>{{ __('Cancel') }}
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="ti ti-check me-1"></i>{{ __('Update State') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All Functionality
    const selectAll = document.getElementById('select-all');
    const stateCheckboxes = document.querySelectorAll('.state-checkbox');
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            stateCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Update Select All state
    stateCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.state-checkbox:checked').length;
            const totalCount = stateCheckboxes.length;
            
            if (selectAll) {
                if (checkedCount === 0) {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                } else if (checkedCount === totalCount) {
                    selectAll.checked = true;
                    selectAll.indeterminate = false;
                } else {
                    selectAll.checked = false;
                    selectAll.indeterminate = true;
                }
            }
        });
    });

    // Search functionality
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            searchTimeout = setTimeout(() => {
                if (query.length >= 2) {
                    performSearch(query);
                } else if (query.length === 0) {
                    resetSearch();
                }
            }, 500);
        });
    }
    
    function performSearch(query) {
        fetch(`{{ route('admin.search.state') }}?string_search=${encodeURIComponent(query)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            if (html.includes('nothing')) {
                searchResults.innerHTML = `
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl mx-auto mb-3">
                            <span class="avatar-initial bg-label-warning rounded">
                                <i class="ti ti-search-off display-6"></i>
                            </span>
                        </div>
                        <h5 class="mb-1">${'{{ __("No results found") }}'}</h5>
                        <p class="text-muted">${'{{ __("Try a different search term") }}'}</p>
                    </div>
                `;
            } else {
                searchResults.innerHTML = html;
            }
        })
        .catch(error => console.error('Search error:', error));
    }
    
    function resetSearch() {
        location.reload();
    }
    
    // Clear search
    const clearSearch = document.getElementById('clear-search');
    if (clearSearch) {
        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            resetSearch();
        });
    }
    
    // Reset filters
    const resetFilters = document.getElementById('reset-filters');
    if (resetFilters) {
        resetFilters.addEventListener('click', function() {
            searchInput.value = '';
            document.getElementById('status-filter').value = 'all';
            resetSearch();
        });
    }

    // Edit State Modal
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-state') || e.target.closest('.edit-state')) {
            const button = e.target.classList.contains('edit-state') ? e.target : e.target.closest('.edit-state');
            const id = button.getAttribute('data-id');
            const state = button.getAttribute('data-state');
            const timezone = button.getAttribute('data-timezone');
            
            document.getElementById('edit_state_id').value = id;
            document.getElementById('edit_state').value = state;
            document.getElementById('edit_timezone').value = timezone;
        }
    });

    // Delete State
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-item') || e.target.closest('.delete-item')) {
            e.preventDefault();
            const button = e.target.classList.contains('delete-item') ? e.target : e.target.closest('.delete-item');
            const id = button.getAttribute('data-id');
            
            if (confirm('{{ __("Are you sure you want to delete this state? This will also delete all cities in this state.") }}')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    });

    // Bulk Actions
    const bulkActionBtn = document.getElementById('bulk_action_btn');
    if (bulkActionBtn) {
        bulkActionBtn.addEventListener('click', function() {
            const bulkOption = document.getElementById('bulk_option').value;
            const checkedBoxes = document.querySelectorAll('.state-checkbox:checked');
            
            if (bulkOption === '') {
                alert('{{ __("Please select a bulk action") }}');
                return;
            }
            
            if (checkedBoxes.length === 0) {
                alert('{{ __("Please select at least one state") }}');
                return;
            }
            
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            
            if (bulkOption === 'delete') {
                if (confirm(`{{ __("Are you sure you want to delete these :count states?") }}`.replace(':count', ids.length))) {
                    performBulkAction('delete', ids);
                }
            } else if (bulkOption === 'activate' || bulkOption === 'deactivate') {
                performBulkAction(bulkOption, ids);
            }
        });
    }
    
    function performBulkAction(action, ids) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.bulk.action.state") }}';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);
        
        ids.forEach(id => {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'ids[]';
            idInput.value = id;
            form.appendChild(idInput);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
});
</script>

@endsection