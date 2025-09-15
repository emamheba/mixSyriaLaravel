@extends('layouts/layoutMaster')

@section('title', 'Districts Management')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js'
])
@endsection

@section('content')

<!-- Districts Stats Widget -->
<div class="card mb-6">
  <div class="card-widget-separator-wrapper">
    <div class="card-body card-widget-separator">
      <div class="row gy-4 gy-sm-1">
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $stats['active'] }}</h4>
              <p class="mb-0">{{ __('Active Districts') }}</p>
            </div>
            <span class="avatar me-sm-6">
              <span class="avatar-initial bg-label-success rounded text-heading">
                <i class="ti-26px ti ti-building-skyscraper text-heading"></i>
              </span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none me-6">
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
            <div>
              <h4 class="mb-0">{{ $stats['inactive'] }}</h4>
              <p class="mb-0">{{ __('Inactive Districts') }}</p>
            </div>
            <span class="avatar p-2 me-lg-6">
              <span class="avatar-initial bg-label-warning rounded"><i class="ti-26px ti ti-ban text-heading"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none">
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start border-end pb-4 pb-sm-0 card-widget-3">
            <div>
              <h4 class="mb-0">{{ $stats['total'] }}</h4>
              <p class="mb-0">{{ __('Total Districts') }}</p>
            </div>
            <span class="avatar p-2 me-sm-6">
              <span class="avatar-initial bg-label-primary rounded"><i class="ti-26px ti ti-map-2 text-heading"></i></span>
            </span>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h4 class="mb-0">{{ $stats['total_cities'] }}</h4>
              <p class="mb-0">{{ __('Active Cities') }}</p>
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

<!-- Add New District -->
<div class="card mb-4">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">
        <i class="ti ti-plus me-2"></i>{{ __('Add New District') }}
      </h5>
      <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#addDistrictForm">
        <i class="ti ti-chevron-down"></i>
      </button>
    </div>
  </div>
  <div class="collapse" id="addDistrictForm">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.all.district') }}">
        @csrf
        <div class="row g-4">
          <div class="col-md-3">
            <label class="form-label" for="add_state_id">{{ __('State') }} <span class="text-danger">*</span></label>
            <select class="form-select select2" id="add_state_id" name="state_id" data-url="{{ route('admin.get.cities') }}" required>
              <option value="">{{ __('Select State') }}</option>
              @foreach($all_states as $state)
                <option value="{{ $state->id }}">{{ $state->state }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label" for="add_city_id">{{ __('City') }} <span class="text-danger">*</span></label>
            <select class="form-select select2" id="add_city_id" name="city_id" required>
              <option value="">{{ __('Select State First') }}</option>
            </select>
          </div>
          
          <div class="col-md-3">
            <label class="form-label" for="district">{{ __('District Name') }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('district') is-invalid @enderror" 
                   id="district" name="district" placeholder="{{ __('Enter district name') }}" required>
            @error('district')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          
          <div class="col-md-1">
            <label class="form-label" for="status">{{ __('Status') }}</label>
            <select class="form-select" id="status" name="status">
              <option value="1" selected>{{ __('Active') }}</option>
              <option value="0">{{ __('Inactive') }}</option>
            </select>
          </div>
          
          <div class="col-md-2">
            <label class="form-label d-block"> </label>
            <button type="submit" class="btn btn-primary w-100"><i class="ti ti-plus me-1"></i>{{ __('Add') }}</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Districts Table -->
<div class="card">
  <div class="card-header border-bottom">
     <h5 class="card-title mb-0"><i class="ti ti-list me-2"></i>{{ __('Districts Management') }}</h5>
  </div>
  <div class="card-datatable table-responsive">
    <div id="search-results">
        @include('countrymanage::district.search-result', ['all_districts' => $all_districts])
    </div>
  </div>
</div>

<!-- Edit District Modal -->
<div class="modal fade" id="editDistrictModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="ti ti-pencil me-2"></i>{{ __('Edit District') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.edit.district') }}" method="POST">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="district_id" id="edit_district_id">
          
          <div class="mb-3">
            <label class="form-label" for="edit_state_id">{{ __('State') }}</label>
            <select class="form-select" id="edit_state_id" name="state_id" data-url="{{ route('admin.get.cities') }}" required>
              @foreach($all_states as $state)
                <option value="{{ $state->id }}">{{ $state->state }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label" for="edit_city_id">{{ __('City') }}</label>
            <select class="form-select" id="edit_city_id" name="city_id" required></select>
          </div>
          
          <div class="mb-3">
            <label class="form-label" for="edit_district_name">{{ __('District Name') }}</label>
            <input type="text" class="form-control" id="edit_district_name" name="district" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Update District') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize select2
    $('.select2').select2();

    function fetchCities(stateId, cityDropdown, selectedCityId = null) {
        const url = $('#add_state_id').data('url');
        if (stateId) {
            $.ajax({
                url: `${url}?state_id=${stateId}`,
                type: 'GET',
                success: function(data) {
                    cityDropdown.empty().append('<option value="">{{ __("Select City") }}</option>');
                    $.each(data, function(index, city) {
                        const option = new Option(city.city, city.id, false, city.id == selectedCityId);
                        cityDropdown.append(option);
                    });
                    if (selectedCityId) {
                        cityDropdown.val(selectedCityId).trigger('change');
                    }
                }
            });
        } else {
            cityDropdown.empty().append('<option value="">{{ __("Select State First") }}</option>');
        }
    }

    // Handle city dropdown for Add form
    $('#add_state_id').on('change', function() {
        const stateId = $(this).val();
        fetchCities(stateId, $('#add_city_id'));
    });
    
    // Handle city dropdown for Edit form
    $('#edit_state_id').on('change', function() {
        const stateId = $(this).val();
        fetchCities(stateId, $('#edit_city_id'));
    });

    // Populate Edit Modal
    $(document).on('click', '.edit-district', function() {
        const district = $(this).data('district');
        $('#edit_district_id').val(district.id);
        $('#edit_district_name').val(district.district);
        $('#edit_state_id').val(district.state_id).trigger('change');
        // Fetch cities and then select the correct one
        fetchCities(district.state_id, $('#edit_city_id'), district.city_id);
    });
    
    // Handle Delete
    $(document).on('click', '.delete-item', function(e) {
        e.preventDefault();
        const formId = $(this).data('form-id');
        if (confirm('{{ __("Are you sure you want to delete this district?") }}')) {
            $(`#${formId}`).submit();
        }
    });
});
</script>
@endsection