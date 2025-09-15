@extends('layouts/layoutMaster')

@section('title', 'Cities Management')

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

<!-- Cities Stats Widget -->
<div class="card mb-6">
  <div class="card-widget-separator-wrapper">
    <div class="card-body card-widget-separator">
      <div class="row gy-4 gy-sm-1">
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $all_cities->where('status', 1)->count() }}</h4>
              <p class="mb-0">{{ __('Active Cities') }}</p>
            </div>
            <span class="avatar me-sm-6">
              <span class="avatar-initial bg-label-success rounded text-heading">
                <i class="ti-26px ti ti-building-community text-heading"></i>
              </span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none me-6">
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $all_cities->where('status', 0)->count() }}</h4>
              <p class="mb-0">{{ __('Inactive Cities') }}</p>
            </div>
            <span class="avatar p-2 me-lg-6">
              <span class="avatar-initial bg-label-warning rounded"><i class="ti-26px ti ti-building-off text-heading"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none">
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start border-end pb-4 pb-sm-0 card-widget-3">
            <div>
              <h4 class="mb-0">{{ $all_cities->total() }}</h4>
              <p class="mb-0">{{ __('Total Cities') }}</p>
            </div>
            <span class="avatar p-2 me-sm-6">
              <span class="avatar-initial bg-label-primary rounded"><i class="ti-26px ti ti-map-2 text-heading"></i></span>
            </span>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h4 class="mb-0">{{ $all_states->count() }}</h4>
              <p class="mb-0">{{ __('Available States') }}</p>
            </div>
            <span class="avatar p-2">
              <span class="avatar-initial bg-label-info rounded"><i class="ti-26px ti ti-map-pin text-heading"></i></span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add New City -->
<div class="card mb-4">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">
        <i class="ti ti-plus me-2"></i>{{ __('Add New City') }}
      </h5>
      <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#addCityForm">
        <i class="ti ti-chevron-down"></i>
      </button>
    </div>
  </div>
  <div class="collapse" id="addCityForm">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.all.city') }}">
        @csrf
        <div class="row g-4">
          <div class="col-md-4">
            <label class="form-label" for="state">{{ __('State') }} <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti ti-map-pin"></i></span>
              <select class="form-select @error('state') is-invalid @enderror" id="state" name="state" required>
                <option value="">{{ __('Select State') }}</option>
                @foreach($all_states as $state)
                  <option value="{{ $state->id }}" {{ old('state') == $state->id ? 'selected' : '' }}>
                    {{ $state->state }}
                  </option>
                @endforeach
              </select>
            </div>
            @error('state')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="col-md-4">
            <label class="form-label" for="city">{{ __('City Name') }} <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti ti-building-community"></i></span>
              <input type="text" class="form-control @error('city') is-invalid @enderror" 
                     id="city" name="city" placeholder="{{ __('Enter city name') }}" 
                     value="{{ old('city') }}" required>
            </div>
            @error('city')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
          
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
              <i class="ti ti-plus me-1"></i>{{ __('Add City') }}
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
      <div class="col-md-6">
        <label class="form-label">{{ __('Search Cities') }}</label>
        <div class="input-group">
          <span class="input-group-text"><i class="ti ti-search"></i></span>
          <input type="text" class="form-control" id="search-input" 
                 placeholder="{{ __('Search by city name...') }}">
          <button class="btn btn-outline-secondary" type="button" id="clear-search">
            <i class="ti ti-x"></i>
          </button>
        </div>
      </div>
      
      <div class="col-md-2">
        <label class="form-label">{{ __('State Filter') }}</label>
        <select class="form-select" id="state-filter">
          <option value="all">{{ __('All States') }}</option>
          @foreach($all_states as $state)
            <option value="{{ $state->id }}">{{ $state->state }}</option>
          @endforeach
        </select>
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

<!-- Cities Table -->
<div class="card">
  <div class="card-header border-bottom">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h5 class="card-title mb-0">
          <i class="ti ti-list me-2"></i>{{ __('Cities Management') }}
        </h5>
        <small class="text-muted">{{ __('Manage all cities and their settings') }}</small>
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
      <table id="cities-table" class="table border-top">
        <thead>
          <tr>
            <th width="50">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="select-all">
                <label class="form-check-label" for="select-all"></label>
              </div>
            </th>
            <th width="80">{{ __('ID') }}</th>
            <th>{{ __('City Name') }}</th>
            <th>{{ __('State') }}</th>
            <th width="120">{{ __('Districts Count') }}</th> 
            {{-- <th width="120">{{ __('Timezone') }}</th> --}}
            <th width="100">{{ __('Status') }}</th>
            <th width="120">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($all_cities as $city)
          <tr>
            <td>
              <div class="form-check">
                <input class="form-check-input city-checkbox" type="checkbox" value="{{ $city->id }}">
              </div>
            </td>
            <td>
              <span class="badge bg-label-secondary">{{ $city->id }}</span>
            </td>
            <td>
              <div class="d-flex align-items-center">
                <div class="avatar avatar-sm me-3">
                  <span class="avatar-initial bg-label-primary rounded">
                    <i class="ti ti-building-community"></i>
                  </span>
                </div>
                <div>
                  <h6 class="mb-0">{{ $city->city }}</h6>
<small class="text-muted">
  {{ __('Created') }}:
  @if($city->created_at)
    {{ $city->created_at->format('M d, Y') }}
  @else
    {{ __('N/A') }} {{-- يمكنك تغيير "N/A" إلى أي نص تفضله عند عدم توفر التاريخ --}}
  @endif
</small>                </div>
              </div>
            </td>
            <td>
              <div class="d-flex align-items-center">
                <div class="avatar avatar-xs me-2">
                  <span class="avatar-initial bg-label-info rounded">
                    <i class="ti ti-map-pin"></i>
                  </span>
                </div>
                <span class="fw-medium">{{ $city->state->state ?? __('N/A') }}</span>
              </div>
            </td>
            {{-- <td>
              <span class="badge bg-label-info">{{ $city->state->timezone ?? __('N/A') }}</span>
            </td> --}}
            <td> {{-- <-- إضافة جديدة --}}
              <div class="d-flex align-items-center">
                <i class="ti ti-building-skyscraper me-1"></i>
                <span class="fw-medium">{{ $city->districts->count() }}</span>
              </div>
            </td>
            <td>
              <form action="{{ route('admin.city.status', $city->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm {{ $city->status == 1 ? 'btn-success' : 'btn-warning' }} status-btn">
                  <i class="ti ti-{{ $city->status == 1 ? 'check' : 'clock' }} me-1"></i>
                  {{ $city->status == 1 ? __('Active') : __('Inactive') }}
                </button>
              </form>
            </td>
            <td>
              <div class="dropdown">
                <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="ti ti-dots-vertical"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                  <a class="dropdown-item edit-city" href="#" data-bs-toggle="modal" data-bs-target="#editCityModal"
                     data-id="{{ $city->id }}" data-city="{{ $city->city }}" data-state="{{ $city->state_id }}">
                    <i class="ti ti-pencil me-1"></i> {{ __('Edit') }}
                  </a>
                  <div class="dropdown-divider"></div>
                  <form id="delete-form-{{ $city->id }}" action="{{ route('admin.delete.city', $city->id) }}" method="POST" class="d-none">
                    @csrf
                  </form>
                  <a class="dropdown-item text-danger delete-item" href="#" data-id="{{ $city->id }}">
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
                    <i class="ti ti-building-off display-6"></i>
                  </span>
                </div>
                <h5 class="mb-1">{{ __('No cities found') }}</h5>
                <p class="text-muted mb-3">{{ __('Start by adding your first city') }}</p>
                <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addCityForm">
                  <i class="ti ti-plus me-1"></i>{{ __('Add New City') }}
                </button>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    @if($all_cities->hasPages())
    <div class="card-footer">
      <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">
          {{ __('Showing :from to :to of :total results', [
            'from' => $all_cities->firstItem(),
            'to' => $all_cities->lastItem(),
            'total' => $all_cities->total()
          ]) }}
        </small>
        {{ $all_cities->links() }}
      </div>
    </div>
    @endif
  </div>
</div>

<!-- Edit City Modal -->
<div class="modal fade" id="editCityModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="ti ti-pencil me-2"></i>{{ __('Edit City') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.edit.city') }}" method="POST">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="city_id" id="edit_city_id">
          
          <div class="mb-4">
            <label class="form-label" for="edit_state">{{ __('State') }} <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti ti-map-pin"></i></span>
              <select class="form-select" id="edit_state" name="state" required>
                <option value="">{{ __('Select State') }}</option>
                @foreach($all_states as $state)
                  <option value="{{ $state->id }}">{{ $state->state }}</option>
                @endforeach
              </select>
            </div>
          </div>
          
          <div class="mb-4">
            <label class="form-label" for="edit_city">{{ __('City Name') }} <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti ti-building-community"></i></span>
              <input type="text" class="form-control" id="edit_city" name="city" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="ti ti-x me-1"></i>{{ __('Cancel') }}
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="ti ti-check me-1"></i>{{ __('Update City') }}
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
    const cityCheckboxes = document.querySelectorAll('.city-checkbox');
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            cityCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Update Select All state
    cityCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.city-checkbox:checked').length;
            const totalCount = cityCheckboxes.length;
            
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
        fetch(`{{ route('admin.search.city') }}?string_search=${encodeURIComponent(query)}`, {
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
            document.getElementById('state-filter').value = 'all';
            document.getElementById('status-filter').value = 'all';
            resetSearch();
        });
    }

    // Edit City Modal
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-city') || e.target.closest('.edit-city')) {
            const button = e.target.classList.contains('edit-city') ? e.target : e.target.closest('.edit-city');
            const id = button.getAttribute('data-id');
            const city = button.getAttribute('data-city');
            const state = button.getAttribute('data-state');
            
            document.getElementById('edit_city_id').value = id;
            document.getElementById('edit_city').value = city;
            document.getElementById('edit_state').value = state;
        }
    });

    // Delete City
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-item') || e.target.closest('.delete-item')) {
            e.preventDefault();
            const button = e.target.classList.contains('delete-item') ? e.target : e.target.closest('.delete-item');
            const id = button.getAttribute('data-id');
            
            if (confirm('{{ __("Are you sure you want to delete this city?") }}')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    });

    // Bulk Actions
    const bulkActionBtn = document.getElementById('bulk_action_btn');
    if (bulkActionBtn) {
        bulkActionBtn.addEventListener('click', function() {
            const bulkOption = document.getElementById('bulk_option').value;
            const checkedBoxes = document.querySelectorAll('.city-checkbox:checked');
            
            if (bulkOption === '') {
                alert('{{ __("Please select a bulk action") }}');
                return;
            }
            
            if (checkedBoxes.length === 0) {
                alert('{{ __("Please select at least one city") }}');
                return;
            }
            
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            
            if (bulkOption === 'delete') {
                if (confirm(`{{ __("Are you sure you want to delete these :count cities?") }}`.replace(':count', ids.length))) {
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
        form.action = '{{ route("admin.bulk.action.city") }}';
        
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