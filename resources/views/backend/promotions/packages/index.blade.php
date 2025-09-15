@extends('layouts/layoutMaster')

@section('title', 'Promotion Packages Management')

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

<!-- Packages Stats Widget -->
<div class="card mb-6">
  <div class="card-widget-separator-wrapper">
    <div class="card-body card-widget-separator">
      <div class="row gy-4 gy-sm-1">
        <div class="col-sm-6 col-lg-4">
          <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
              <p class="mb-0">{{ __('Total Packages') }}</p>
            </div>
            <span class="avatar me-sm-6">
              <span class="avatar-initial bg-label-primary rounded text-heading">
                <i class="ti-26px ti ti-package text-heading"></i>
              </span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none me-6">
        </div>
        <div class="col-sm-6 col-lg-4">
          <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $stats['active'] ?? 0 }}</h4>
              <p class="mb-0">{{ __('Active Packages') }}</p>
            </div>
            <span class="avatar p-2 me-lg-6">
              <span class="avatar-initial bg-label-success rounded"><i class="ti-26px ti ti-checks text-heading"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none">
        </div>
        <div class="col-sm-6 col-lg-4">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h4 class="mb-0">{{ $stats['inactive'] ?? 0 }}</h4>
              <p class="mb-0">{{ __('Inactive Packages') }}</p>
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
    <form method="GET" action="{{ route('promotions.packages.index') }}" id="search-form">
      <div class="row g-3">
        <!-- البحث الأساسي -->
        <div class="col-md-6">
          <label class="form-label">{{ __('Search') }}</label>
          <div class="input-group">
            <input type="text" class="form-control" name="search" 
                   value="{{ request('search') }}" 
                   placeholder="{{ __('Search in name, description...') }}">
            <button class="btn btn-primary" type="submit">
              <i class="ti ti-search"></i>
            </button>
          </div>
        </div>
        
        <!-- فلتر الحالة -->
        <div class="col-md-3">
          <label class="form-label">{{ __('Status') }}</label>
          <select class="form-select" name="status">
            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
          </select>
        </div>
        
        <!-- أزرار التحكم -->
        <div class="col-md-3">
          <label class="form-label d-block">&nbsp;</label>
          <div class="btn-group w-100" role="group">
            <button type="submit" class="btn btn-primary">
              <i class="ti ti-search me-1"></i>{{ __('Search') }}
            </button>
            <a href="{{ route('promotions.packages.index') }}" class="btn btn-outline-secondary">
              <i class="ti ti-refresh me-1"></i>{{ __('Reset') }}
            </a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Packages Table -->
<div class="card">
  <div class="card-header border-bottom">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h5 class="card-title mb-0">{{ __('Promotion Packages') }}</h5>
        @if(request()->hasAny(['search', 'status']))
          <small class="text-muted">
            {{ __('Showing :count results', ['count' => $packages->total()]) }}
            @if(request('search'))
              {{ __('for') }} "<strong>{{ request('search') }}</strong>"
            @endif
          </small>
        @endif
      </div>
      
      <div class="d-flex gap-2">
        <a href="{{ route('promotions.packages.create') }}" class="btn btn-primary">
          <i class="ti ti-plus me-1"></i>{{ __('Add New Package') }}
        </a>
      </div>
    </div>
  </div>
  
  <div class="card-datatable table-responsive">
    <table id="packages-table" class="table border-top">
      <thead>
        <tr>
          <th>{{ __('ID') }}</th>
          <th>{{ __('Name') }}</th>
          <th>{{ __('Description') }}</th>
          <th>{{ __('Price') }}</th>
          <th>{{ __('Duration') }}</th>
          <th>{{ __('Status') }}</th>
          <th>{{ __('Created') }}</th>
          <th>{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($packages as $package)
        <tr>
          <td>{{ $package->id }}</td>
          <td>
            <div class="d-flex align-items-center">
              <div class="avatar avatar-sm me-3">
                <span class="avatar-initial bg-label-primary rounded">
                  <i class="ti ti-package"></i>
                </span>
              </div>
              <div>
                <strong>{{ $package->name }}</strong>
                @if($package->stripe_price_id)
                  <br><small class="text-info">Stripe: {{ $package->stripe_price_id }}</small>
                @endif
              </div>
            </div>
          </td>
          <td>
            <span class="text-truncate d-block" style="max-width: 200px;" title="{{ $package->description }}">
              {{ $package->description ?? 'N/A' }}
            </span>
          </td>
          <td>
            <span class="badge bg-label-success rounded-pill">
              ${{ number_format($package->price, 2) }}
            </span>
          </td>
          <td>
            <span class="badge bg-label-info rounded-pill">
              {{ $package->duration_days }} {{ __('days') }}
            </span>
          </td>
          <td>
            <form action="{{ route('promotions.packages.toggle-status', $package->id) }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-sm {{ $package->is_active ? 'btn-success' : 'btn-secondary' }}">
                <i class="ti ti-{{ $package->is_active ? 'check' : 'x' }} me-1"></i>
                {{ $package->is_active ? __('Active') : __('Inactive') }}
              </button>
            </form>
          </td>
          <td>
            <span class="text-muted">{{ $package->created_at->format('M d, Y') }}</span>
            <br><small class="text-muted">{{ $package->created_at->format('H:i') }}</small>
          </td>
          <td>
            <div class="dropdown">
              <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                <i class="ti ti-dots-vertical"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="{{ route('promotions.packages.edit', $package->id) }}">
                  <i class="ti ti-edit me-1"></i> {{ __('Edit') }}
                </a>
                <div class="dropdown-divider"></div>
                <form id="delete-form-{{ $package->id }}" action="{{ route('promotions.packages.delete', $package->id) }}" method="POST" class="d-none">
                  @csrf
                  @method('DELETE')
                </form>
                <a class="dropdown-item text-danger delete-item" href="#" data-id="{{ $package->id }}">
                  <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
                </a>
              </div>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center">
            <div class="my-4">
              <i class="ti ti-package-off display-4 text-muted"></i>
              <h5 class="mt-2">{{ __('No packages found') }}</h5>
              @if(request()->hasAny(['search', 'status']))
                <p class="text-muted">{{ __('Try adjusting your search criteria') }}</p>
                <a href="{{ route('promotions.packages.index') }}" class="btn btn-primary">
                  <i class="ti ti-refresh me-1"></i>{{ __('Reset Filters') }}
                </a>
              @else
                <p class="text-muted">{{ __('Create your first promotion package') }}</p>
                <a href="{{ route('promotions.packages.create') }}" class="btn btn-primary">
                  <i class="ti ti-plus me-1"></i>{{ __('Add Package') }}
                </a>
              @endif
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
    
    <!-- Pagination -->
    @if($packages->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3">
      <div>
        <small class="text-muted">
          {{ __('Showing :from to :to of :total results', [
            'from' => $packages->firstItem(),
            'to' => $packages->lastItem(),
            'total' => $packages->total()
          ]) }}
        </small>
      </div>
      <div>
        {{ $packages->links() }}
      </div>
    </div>
    @endif
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
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
    const filterSelects = document.querySelectorAll('select[name="status"]');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('search-form').submit();
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-item')) {
            e.preventDefault();
            const id = e.target.getAttribute('data-id');
            if (confirm('{{ __("Are you sure you want to delete this package?") }}')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    });
});
</script>

@endsection