@extends('layouts/layoutMaster')

@section('title', '{{ __("Add/Edit Driver - Apps") }}')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/quill/typography.scss',
  'resources/assets/vendor/libs/quill/katex.scss',
  'resources/assets/vendor/libs/quill/editor.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/dropzone/dropzone.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/tagify/tagify.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/quill/katex.js',
  'resources/assets/vendor/libs/quill/quill.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/dropzone/dropzone.js',
  'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/tagify/tagify.js'
])
@endsection

@section('page-script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    // On country selection, update regions dropdown
    $('#driver-country').change(function() {
      let countryId = $(this).val();
      if (countryId) {
        $.get('/get-regions/' + countryId, function(data) {
          $('#driver-region').html('<option value="">{{ __("Select Region") }}</option>');
          data.forEach(function(region) {
            $('#driver-region').append('<option value="'+region.id+'">'+region.name+'</option>');
          });
          $('#driver-region').prop('disabled', false);
        });
      }
    });

    // On region selection, update cities dropdown
    $('#driver-region').change(function() {
      let regionId = $(this).val();
      if (regionId) {
        $.get('/get-cities/' + regionId, function(data) {
          $('#driver-city').html('<option value="">{{ __("Select City") }}</option>');
          data.forEach(function(city) {
            $('#driver-city').append('<option value="'+city.id+'">'+city.name+'</option>');
          });
          $('#driver-city').prop('disabled', false);
        });
      }
    });

    // On city selection, update districts dropdown
    $('#driver-city').change(function() {
      let cityId = $(this).val();
      if (cityId) {
        $.get('/get-districts/' + cityId, function(data) {
          $('#driver-district').html('<option value="">{{ __("Select District") }}</option>');
          data.forEach(function(district) {
            $('#driver-district').append('<option value="'+district.id+'">'+district.name+'</option>');
          });
          $('#driver-district').prop('disabled', false);
        });
      }
    });
  });
</script>
@endsection

@section('content')
<div class="app-ecommerce">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
    <div class="d-flex flex-column justify-content-center">
      <h4 class="mb-1">{{ isset($driver) ? __('Edit Driver') : __('Add a New Driver') }}</h4>
      {{-- <p class="mb-0">{{ __('Manage drivers in your system') }}</p> --}}
    </div>
    <div class="d-flex align-content-center flex-wrap gap-4">
      <div class="d-flex gap-4">
        <button class="btn btn-label-secondary">{{ __('Discard') }}</button>
        {{-- <button class="btn btn-label-primary">{{ __('Save Draft') }}</button> --}}
      </div>
      <button type="submit" form="driver-form" class="btn btn-primary">{{ __('Publish Driver') }}</button>
    </div>
  </div>

  <form id="driver-form" action="{{ isset($driver) ? route('drivers.update', $driver->id) : route('drivers.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($driver))
      @method('PUT')
    @endif

    <div class="row">
      <div class="col-12 col-lg-8">
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-tile mb-0">{{ __('Driver Information') }}</h5>
          </div>
          <div class="card-body">
            <div class="mb-6">
              <label class="form-label" for="driver-name">{{ __('Name') }}</label>
              <input type="text" class="form-control" id="driver-name" placeholder="{{ __('Driver Name') }}" name="name" value="{{ isset($driver) ? $driver->name : old('name') }}" required>
            </div>
            <div class="row mb-6">
              <div class="col">
                <label class="form-label" for="driver-phone">{{ __('Phone') }}</label>
                <input type="text" class="form-control" id="driver-phone" placeholder="{{ __('Phone Number') }}" name="phone" value="{{ isset($driver) ? $driver->phone : old('phone') }}" required>
              </div>
              <div class="col">
                <label class="form-label" for="driver-salary">{{ __('Salary') }}</label>
                <input type="number" class="form-control" id="driver-salary" placeholder="{{ __('Salary') }}" name="salary" value="{{ isset($driver) ? $driver->salary : old('salary') }}" required>
              </div>
            </div>
            <div class="mb-6">
              <label class="form-label" for="driver-identity-number">{{ __('Identity Number') }}</label>
              <input type="text" class="form-control" id="driver-identity-number" placeholder="{{ __('Identity Number') }}" name="identity_number" value="{{ isset($driver) ? $driver->identity_number : old('identity_number') }}" required>
            </div>
            <div class="mb-6">
              <label class="form-label" for="driver-iban">{{ __('IBAN') }}</label>
              <input type="text" class="form-control" id="driver-iban" placeholder="{{ __('IBAN') }}" name="iban" value="{{ isset($driver) ? $driver->iban : old('iban') }}" required>
            </div>
            <div class="mb-6">
              <label class="form-label" for="driver-image">{{ __('Image') }}</label>
              <input type="file" class="form-control" id="driver-image" name="image" accept="image/*">
              @if(isset($driver) && $driver->image)
                <img src="{{ asset($driver->image) }}" alt="{{ __('Driver Image') }}" class="mt-2" style="max-width: 200px;">
              @endif
            </div>
            <div class="mb-6">
              <label class="form-label" for="driver-identity-image">{{ __('Identity Image') }}</label>
              <input type="file" class="form-control" id="driver-identity-image" name="identity_image" accept="image/*" required>
              @if(isset($driver) && $driver->identity_image)
                <img src="{{ asset($driver->identity_image) }}" alt="{{ __('Identity Image') }}" class="mt-2" style="max-width: 200px;">
              @endif
            </div>
          </div>
        </div>

        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-tile mb-0">{{ __('Address Information') }}</h5>
          </div>
          <div class="card-body">
            <div class="mb-6">
              <label class="form-label" for="driver-country">{{ __('Country') }}</label>
              <select class="form-select" id="driver-country" name="country_id" required>
                <option value="">{{ __('Select Country') }}</option>
                @foreach($countries as $country)
                  <option value="{{ $country->id }}" {{ (isset($driver) && $driver->country_id == $country->id) ? 'selected' : '' }}>{{ $country->name }}</option>
                @endforeach
              </select>
            </div>
            
            <!-- Region Dropdown -->
            <div class="mb-6">
              <label class="form-label" for="driver-region">{{ __('Region') }}</label>
              <select class="form-select" id="driver-region" name="region_id" required>
                <option value="">{{ __('Select Region') }}</option>
                @foreach($regions as $region)
                  <option value="{{ $region->id }}" {{ (isset($driver) && $driver->region_id == $region->id) ? 'selected' : '' }}>{{ $region->name }}</option>
                @endforeach
              </select>
            </div>

            <!-- City Dropdown -->
            <div class="mb-6">
              <label class="form-label" for="driver-city">{{ __('City') }}</label>
              <select class="form-select" id="driver-city" name="city_id" required>
                <option value="">{{ __('Select City') }}</option>
                @foreach($cities as $city)
                  <option value="{{ $city->id }}" {{ (isset($driver) && $driver->city_id == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
                @endforeach
              </select>
            </div>

            <!-- District Dropdown -->
            <div class="mb-6">
              <label class="form-label" for="driver-district">{{ __('District') }}</label>
              <select class="form-select" id="driver-district" name="district_id" required>
                <option value="">{{ __('Select District') }}</option>
                @foreach($districts as $district)
                  <option value="{{ $district->id }}" {{ (isset($driver) && $driver->district_id == $district->id) ? 'selected' : '' }}>{{ $district->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="mb-6">
              <label class="form-label" for="driver-address">{{ __('Address') }}</label>
              <textarea class="form-control" id="driver-address" placeholder="{{ __('Address') }}" name="address">{{ isset($driver) ? $driver->address : old('address') }}</textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Status') }}</h5>
          </div>
          <div class="card-body">
            <div class="mb-6">
              <label class="form-label" for="driver-status">{{ __('Status') }}</label>
              <select class="form-select" id="driver-status" name="status" required>
                <option value="active" {{ (isset($driver) && $driver->status == 'active') ? 'selected' : '' }}>{{ __('Active') }}</option>
                <option value="inactive" {{ (isset($driver) && $driver->status == 'inactive') ? 'selected' : '' }}>{{ __('Inactive') }}</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

   
  </form>
</div>
@endsection