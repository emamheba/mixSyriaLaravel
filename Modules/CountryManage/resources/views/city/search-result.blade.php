{{-- resources/views/modules/countrymanage/city/search-result.blade.php --}}

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
{{-- resources/views/modules/countrymanage/city/search-result.blade.php --}}

<small class="text-muted">
  {{ __('Created') }}:
  @if($city->created_at) {{-- <-- تم التعديل هنا --}}
    {{ $city->created_at->format('M d, Y') }} {{-- <-- وتم التعديل هنا --}}
  @else
    {{ __('N/A') }} {{-- يمكنك تغيير "N/A" إلى أي نص تفضله عند عدم توفر التاريخ --}}
  @endif
</small>
</div>
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
      <td colspan="6" class="text-center">
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