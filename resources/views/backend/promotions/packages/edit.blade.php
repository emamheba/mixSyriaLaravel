@extends('layouts/layoutMaster')

@section('title', 'Edit Promotion Package')

@section('content')

<!-- Navigation Breadcrumb -->
<div class="row">
  <div class="col-12">
    <div class="card mb-6">
      <div class="card-body">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb breadcrumb-style1 mb-0">
            <li class="breadcrumb-item">
              <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
              <a href="{{ route('promotions.packages.index') }}">{{ __('Promotion Packages') }}</a>
            </li>
            <li class="breadcrumb-item active">{{ __('Edit Package') }}</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Edit Package Form -->
  <div class="col-12 col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Edit Package Information') }}</h5>
        <small class="text-muted">{{ __('Package ID') }}: #{{ $package->id }}</small>
      </div>
      <div class="card-body">
        <form action="{{ route('promotions.packages.update', $package->id) }}" method="POST">
          @csrf
          @method('PUT')
          
          <div class="row g-4">
            <!-- Package Name -->
            <div class="col-12">
              <label class="form-label" for="name">{{ __('Package Name') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" 
                     id="name" name="name" value="{{ old('name', $package->name) }}" 
                     placeholder="{{ __('Enter package name') }}" required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Description -->
            <div class="col-12">
              <label class="form-label" for="description">{{ __('Description') }}</label>
              <textarea class="form-control @error('description') is-invalid @enderror" 
                        id="description" name="description" rows="4" 
                        placeholder="{{ __('Enter package description') }}">{{ old('description', $package->description) }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Price and Duration -->
            <div class="col-md-6">
              <label class="form-label" for="price">{{ __('Price') }} <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                       id="price" name="price" value="{{ old('price', $package->price) }}" 
                       step="0.01" min="0" placeholder="0.00" required>
                @error('price')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="duration_days">{{ __('Duration (Days)') }} <span class="text-danger">*</span></label>
              <input type="number" class="form-control @error('duration_days') is-invalid @enderror" 
                     id="duration_days" name="duration_days" value="{{ old('duration_days', $package->duration_days) }}" 
                     min="1" placeholder="{{ __('Number of days') }}" required>
              @error('duration_days')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Stripe Price ID -->
            <div class="col-12">
              <label class="form-label" for="stripe_price_id">{{ __('Stripe Price ID') }}</label>
              <input type="text" class="form-control @error('stripe_price_id') is-invalid @enderror" 
                     id="stripe_price_id" name="stripe_price_id" value="{{ old('stripe_price_id', $package->stripe_price_id) }}" 
                     placeholder="{{ __('price_xxxxxxxxxxxxx') }}">
              <div class="form-text">{{ __('Optional: Stripe Price ID for online payments') }}</div>
              @error('stripe_price_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Status -->
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                       {{ old('is_active', $package->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                  {{ __('Active Package') }}
                </label>
              </div>
              <div class="form-text">{{ __('Only active packages will be available for users to purchase') }}</div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="mt-6">
            <button type="submit" class="btn btn-primary me-2">
              <i class="ti ti-device-floppy me-1"></i>{{ __('Update Package') }}
            </button>
            <a href="{{ route('promotions.packages.index') }}" class="btn btn-outline-secondary">
              <i class="ti ti-arrow-left me-1"></i>{{ __('Back to Packages') }}
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Package Preview & Info -->
  <div class="col-12 col-lg-4">
    <!-- Package Preview -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Package Preview') }}</h5>
      </div>
      <div class="card-body">
        <div class="text-center mb-4">
          <div class="avatar avatar-xl mx-auto mb-3">
            <span class="avatar-initial bg-label-primary rounded">
              <i class="ti-xl ti ti-package"></i>
            </span>
          </div>
          <h6 class="package-preview-name">{{ $package->name }}</h6>
          <p class="text-muted package-preview-description">{{ $package->description ?: __('Package description will appear here') }}</p>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted">{{ __('Price') }}:</span>
          <span class="badge bg-label-success package-preview-price">${{ number_format($package->price, 2) }}</span>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted">{{ __('Duration') }}:</span>
          <span class="badge bg-label-info package-preview-duration">{{ $package->duration_days }} {{ __('days') }}</span>
        </div>

        <div class="d-flex justify-content-between align-items-center">
          <span class="text-muted">{{ __('Status') }}:</span>
          <span class="badge {{ $package->is_active ? 'bg-label-success' : 'bg-label-secondary' }} package-preview-status">
            {{ $package->is_active ? __('Active') : __('Inactive') }}
          </span>
        </div>

        <div class="alert alert-info mt-4">
          <i class="ti ti-info-circle me-2"></i>
          <small>{{ __('This is how your package will appear to users') }}</small>
        </div>
      </div>
    </div>

    <!-- Package Statistics -->
    <div class="card mt-4">
      <div class="card-header">
        <h6 class="card-title mb-0">
          <i class="ti ti-chart-bar me-2"></i>{{ __('Package Statistics') }}
        </h6>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-muted">{{ __('Created') }}:</span>
          <small>{{ $package->created_at->format('M d, Y') }}</small>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-muted">{{ __('Last Updated') }}:</span>
          <small>{{ $package->updated_at->format('M d, Y') }}</small>
        </div>
        @if($package->stripe_price_id)
        <div class="d-flex justify-content-between align-items-center">
          <span class="text-muted">{{ __('Stripe Integration') }}:</span>
          <span class="badge bg-label-success">{{ __('Connected') }}</span>
        </div>
        @endif
      </div>
    </div>

    <!-- Warning -->
    <div class="card mt-4">
      <div class="card-body">
        <div class="alert alert-warning mb-0">
          <i class="ti ti-alert-triangle me-2"></i>
          <small>{{ __('Changes to this package will not affect existing active promotions') }}</small>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Real-time preview updates
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    const priceInput = document.getElementById('price');
    const durationInput = document.getElementById('duration_days');
    const activeInput = document.getElementById('is_active');

    const previewName = document.querySelector('.package-preview-name');
    const previewDescription = document.querySelector('.package-preview-description');
    const previewPrice = document.querySelector('.package-preview-price');
    const previewDuration = document.querySelector('.package-preview-duration');
    const previewStatus = document.querySelector('.package-preview-status');

    // Update preview on input change
    nameInput.addEventListener('input', function() {
        previewName.textContent = this.value || '{{ __("Package Name") }}';
    });

    descriptionInput.addEventListener('input', function() {
        previewDescription.textContent = this.value || '{{ __("Package description will appear here") }}';
    });

    priceInput.addEventListener('input', function() {
        const price = parseFloat(this.value) || 0;
        previewPrice.textContent = '$' + price.toFixed(2);
    });

    durationInput.addEventListener('input', function() {
        const duration = parseInt(this.value) || 0;
        previewDuration.textContent = duration + ' {{ __("days") }}';
    });

    activeInput.addEventListener('change', function() {
        if (this.checked) {
            previewStatus.textContent = '{{ __("Active") }}';
            previewStatus.className = 'badge bg-label-success package-preview-status';
        } else {
            previewStatus.textContent = '{{ __("Inactive") }}';
            previewStatus.className = 'badge bg-label-secondary package-preview-status';
        }
    });
});
</script>

@endsection
