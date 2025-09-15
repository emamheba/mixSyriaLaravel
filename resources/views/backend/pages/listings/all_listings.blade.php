@extends('layouts/layoutMaster')

@section('title', 'User Listings Management')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'
])
@endsection

@section('content')

<!-- Listings Stats Widget -->
<div class="card mb-6">
  <div class="card-widget-separator-wrapper">
    <div class="card-body card-widget-separator">
      <div class="row gy-4 gy-sm-1">
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $stats['pending'] ?? $all_listings->where('status', 0)->count() }}</h4>
              <p class="mb-0">{{ __('Pending Listings') }}</p>
            </div>
            <span class="avatar me-sm-6">
              <span class="avatar-initial bg-label-warning rounded text-heading">
                <i class="ti-26px ti ti-calendar-stats text-heading"></i>
              </span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none me-6">
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $stats['approved'] ?? $all_listings->where('status', 1)->count() }}</h4>
              <p class="mb-0">{{ __('Approved Listings') }}</p>
            </div>
            <span class="avatar p-2 me-lg-6">
              <span class="avatar-initial bg-label-success rounded"><i class="ti-26px ti ti-checks text-heading"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none">
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start border-end pb-4 pb-sm-0 card-widget-3">
            <div>
              <h4 class="mb-0">{{ $stats['published'] ?? $all_listings->where('is_published', 1)->count() }}</h4>
              <p class="mb-0">{{ __('Published') }}</p>
            </div>
            <span class="avatar p-2 me-sm-6">
              <span class="avatar-initial bg-label-primary rounded"><i class="ti-26px ti ti-world text-heading"></i></span>
            </span>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h4 class="mb-0">{{ $stats['unpublished'] ?? $all_listings->where('is_published', 0)->count() }}</h4>
              <p class="mb-0">{{ __('Unpublished') }}</p>
            </div>
            <span class="avatar p-2">
              <span class="avatar-initial bg-label-secondary rounded"><i class="ti-26px ti ti-eye-off text-heading"></i></span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="card-title mb-0">{{ __('Search & Filters') }}</h5>
  </div>
  <div class="card-body">
    <form method="GET" action="{{ route('admin.user.all.listings') }}" id="search-form">
      <div class="row g-3">
        <!-- البحث الأساسي -->
        <div class="col-md-4">
          <label class="form-label">{{ __('Search') }}</label>
          <div class="input-group">
            <input type="text" class="form-control" name="search" 
                   value="{{ request('search') }}" 
                   placeholder="{{ __('Search in title, description, category, user...') }}">
            <button class="btn btn-primary" type="submit">
              <i class="ti ti-search"></i>
            </button>
          </div>
        </div>
        
        <!-- فلتر الحالة -->
        <div class="col-md-2">
          <label class="form-label">{{ __('Status') }}</label>
          <select class="form-select" name="status">
            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ __('Approved') }}</option>
            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>{{ __('Pending') }}</option>
          </select>
        </div>
        
        <!-- فلتر النشر -->
        <div class="col-md-2">
          <label class="form-label">{{ __('Published') }}</label>
          <select class="form-select" name="published">
            <option value="all" {{ request('published') == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
            <option value="1" {{ request('published') == '1' ? 'selected' : '' }}>{{ __('Published') }}</option>
            <option value="0" {{ request('published') == '0' ? 'selected' : '' }}>{{ __('Unpublished') }}</option>
          </select>
        </div>
        
        <!-- فلتر الفئة -->
        <div class="col-md-2">
          <label class="form-label">{{ __('Category') }}</label>
          <select class="form-select" name="category">
            <option value="all">{{ __('All Categories') }}</option>
            @if(isset($categories))
              @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
              @endforeach
            @endif
          </select>
        </div>
        
        <!-- فلتر التاريخ -->
        <div class="col-md-2">
          <label class="form-label">{{ __('Date From') }}</label>
          <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
        </div>
        
        <div class="col-md-2">
          <label class="form-label">{{ __('Date To') }}</label>
          <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
        </div>
        
        <!-- أزرار التحكم -->
        <div class="col-md-4">
          <label class="form-label d-block">&nbsp;</label>
          <div class="btn-group" role="group">
            <button type="submit" class="btn btn-primary">
              <i class="ti ti-search me-1"></i>{{ __('Search') }}
            </button>
            <a href="{{ route('admin.user.all.listings') }}" class="btn btn-outline-secondary">
              <i class="ti ti-refresh me-1"></i>{{ __('Reset') }}
            </a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- User Listings Table -->
<div class="card">
  <div class="card-header border-bottom">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h5 class="card-title mb-0">{{ __('User Listings') }}</h5>
        @if(request()->hasAny(['search', 'status', 'published', 'category', 'date_from', 'date_to']))
          <small class="text-muted">
            {{ __('Showing :count results', ['count' => $all_listings->total()]) }}
            @if(request('search'))
              {{ __('for') }} "<strong>{{ request('search') }}</strong>"
            @endif
          </small>
        @endif
      </div>
      
      <div class="d-flex gap-2">
        <!-- الحذف المجمع -->
        <div class="d-flex">
          <select id="bulk_option" class="form-select">
            <option value="">{{ __('Bulk Action') }}</option>
            <option value="delete">{{ __('Delete') }}</option>
          </select>
          <button class="btn btn-primary ms-2" id="bulk_delete_btn">{{ __('Apply') }}</button>
        </div>
        
        <!-- الموافقة على الكل -->
        @if($all_listings->where('status', 0)->count() > 0)
        <form action="{{ route('admin.listings.user.all.approved') }}" method="POST">
          @csrf
          <button class="btn btn-success" type="submit">
            {{ __('Approve All Pending') }} ({{ $all_listings->where('status', 0)->count() }})
          </button>
        </form>
        @endif
      </div>
    </div>
  </div>
  
  <div class="card-datatable table-responsive">
    <table id="listings-table" class="table border-top">
      <thead>
        <tr>
          <th>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="main-select-all">
              <label class="form-check-label" for="main-select-all">{{ __('Select All') }}</label>
            </div>
          </th>
          <th>{{ __('ID') }}</th>
          <th>{{ __('Image') }}</th>
          <th>{{ __('Title') }}</th>
          <th>{{ __('Category') }}</th>
          <th>{{ __('User') }}</th>
          <th>{{ __('Status') }}</th>
          <th>{{ __('Published') }}</th>
          <th>{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($all_listings as $listing)
        <tr>
          <td>
            <div class="form-check">
              <input class="form-check-input listing-checkbox" data-id="{{ $listing->id }}" type="checkbox" value="{{ $listing->id }}">
            </div>
          </td>
          <td>{{ $listing->id }}</td>
          <td>
            <div class="avatar avatar-md">
              @php
                  $imageData = $listing->image;
                  $imageUrl = '';
                  
                  if(is_array($imageData) && isset($imageData['image_url'])) {
                      $imageUrl = $imageData['image_url'];
                  }
                  
                  if(empty($imageUrl)) {
                      $imageUrl = asset('storage/uploads/no-image.png');
                  }
              @endphp
              <img src="{{ $imageUrl }}" class="rounded" >
            </div>
          </td>
          <td>
            <strong>{{ Str::limit($listing->title, 30) }}</strong>
            @if(request('search') && stripos($listing->title, request('search')) !== false)
              <br><small class="text-primary">{{ __('Found in title') }}</small>
            @endif
          </td>
          <td>
            {{ $listing->category->name ?? 'N/A' }}
            @if(request('search') && $listing->category && stripos($listing->category->name, request('search')) !== false)
              <br><small class="text-primary">{{ __('Found in category') }}</small>
            @endif
          </td>
          <td>
            {{ $listing->user->name ?? 'N/A' }}
            @if(request('search') && $listing->user && (stripos($listing->user->name, request('search')) !== false || stripos($listing->user->email, request('search')) !== false))
              <br><small class="text-primary">{{ __('Found in user') }}</small>
            @endif
          </td>
          <td>
            <form action="{{ route('admin.listings.status.change', $listing->id) }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-sm {{ $listing->status == 1 ? 'btn-success' : 'btn-warning' }}">
                {{ $listing->status == 1 ? __('Approved') : __('Pending') }}
              </button>
            </form>
          </td>
          <td>
            <form action="{{ route('admin.listings.published.status.change', $listing->id) }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-sm {{ $listing->is_published == 1 ? 'btn-primary' : 'btn-secondary' }}">
                {{ $listing->is_published == 1 ? __('Published') : __('Unpublished') }}
              </button>
            </form>
          </td>
          <td>
            <div class="dropdown">
              <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                <i class="ti ti-dots-vertical"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="{{ route('admin.listings.details', $listing->id) }}">
                  <i class="ti ti-eye me-1"></i> {{ __('View') }}
                </a>
                <form id="delete-form-{{ $listing->id }}" action="{{ route('admin.listings.delete', $listing->id) }}" method="POST" class="d-none">
                  @csrf
                </form>
                <a class="dropdown-item delete-item" href="#" data-id="{{ $listing->id }}">
                  <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
                </a>
              </div>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" class="text-center">
            <div class="my-4">
              <i class="ti ti-search-off display-4 text-muted"></i>
              <h5 class="mt-2">{{ __('No listings found') }}</h5>
              @if(request()->hasAny(['search', 'status', 'published', 'category', 'date_from', 'date_to']))
                <p class="text-muted">{{ __('Try adjusting your search criteria') }}</p>
                <a href="{{ route('admin.user.all.listings') }}" class="btn btn-primary">
                  <i class="ti ti-refresh me-1"></i>{{ __('Reset Filters') }}
                </a>
              @endif
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
    
    <!-- Pagination -->
    @if($all_listings->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3">
      <div>
        <small class="text-muted">
          {{ __('Showing :from to :to of :total results', [
            'from' => $all_listings->firstItem(),
            'to' => $all_listings->lastItem(),
            'total' => $all_listings->total()
          ]) }}
        </small>
      </div>
      <div>
        {{ $all_listings->links() }}
      </div>
    </div>
    @endif
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // تحديد الكل
    const mainSelectAll = document.getElementById('main-select-all');
    const listingCheckboxes = document.querySelectorAll('.listing-checkbox');
    
    if (mainSelectAll) {
        mainSelectAll.addEventListener('change', function() {
            const isChecked = this.checked;
            listingCheckboxes.forEach(function(checkbox) {
                checkbox.checked = isChecked;
            });
        });
    }
    
    // تحديث حالة تحديد الكل
    listingCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.listing-checkbox:checked').length;
            const totalCount = listingCheckboxes.length;
            
            if (mainSelectAll) {
                if (checkedCount === 0) {
                    mainSelectAll.checked = false;
                    mainSelectAll.indeterminate = false;
                } else if (checkedCount === totalCount) {
                    mainSelectAll.checked = true;
                    mainSelectAll.indeterminate = false;
                } else {
                    mainSelectAll.checked = false;
                    mainSelectAll.indeterminate = true;
                }
            }
        });
    });

    // البحث السريع عند الكتابة
    const searchInput = document.querySelector('input[name="search"]');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3 || this.value.length === 0) {
                    document.getElementById('search-form').submit();
                }
            }, 500);
        });
    }
    
    // تغيير الفلاتر تلقائياً
    const filterSelects = document.querySelectorAll('select[name="status"], select[name="published"], select[name="category"]');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('search-form').submit();
        });
    });

    // حذف العنصر الواحد
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-item')) {
            e.preventDefault();
            const id = e.target.getAttribute('data-id');
            if (confirm('{{ __("Are you sure you want to delete this listing?") }}')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    });

    // الحذف المجمع
    const bulkDeleteBtn = document.getElementById('bulk_delete_btn');
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const bulkOption = document.getElementById('bulk_option').value;
            
            if (bulkOption === 'delete') {
                const checkedBoxes = document.querySelectorAll('.listing-checkbox:checked');
                
                if (checkedBoxes.length > 0) {
                    const ids = Array.from(checkedBoxes).map(checkbox => checkbox.value);
                    
                    if (confirm('{{ __("Are you sure you want to delete these listings?") }} (' + ids.length + ')')) {
                        fetch('{{ route("admin.listing.bulk.action") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                ids: ids
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'ok') {
                                location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('{{ __("An error occurred") }}');
                        });
                    }
                } else {
                    alert('{{ __("Please select at least one listing") }}');
                }
            } else {
                alert('{{ __("Please select a bulk action option") }}');
            }
        });
    }
});
</script>

@endsection