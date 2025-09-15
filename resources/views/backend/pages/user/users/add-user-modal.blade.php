<!-- Add New User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Add New User') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
      </div>
      <form action="{{ route('admin.users.add') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="first_name">{{ __('First Name') }}</label>
              <input type="text" id="first_name" name="first_name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="last_name">{{ __('Last Name') }}</label>
              <input type="text" id="last_name" name="last_name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="username">{{ __('Username') }}</label>
              <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="email">{{ __('Email') }}</label>
              <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="phone">{{ __('Phone') }}</label>
              <input type="text" id="phone" name="phone" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="password">{{ __('Password') }}</label>
              <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
              <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>

            <!-- Country -->
            <div class="col-md-6">
              <label class="form-label" for="country_id">{{ __('Country') }}</label>
              <select id="country_id" name="country_id" class="form-select">
                <option value="">{{ __('Select Country') }}</option>
                @foreach($countries as $country)
                  <option value="{{ $country->id }}">{{ $country->country }}</option>
                @endforeach
              </select>
            </div>

            <!-- State -->
            <div class="col-md-6">
              <label class="form-label" for="state_id">{{ __('State') }}</label>
              <select id="state_id" name="state_id" class="form-select">
                <option value="">{{ __('Select State') }}</option>
              </select>
            </div>

            <!-- City -->
            <div class="col-md-6">
              <label class="form-label" for="city_id">{{ __('City') }}</label>
              <select id="city_id" name="city_id" class="form-select">
                <option value="">{{ __('Select City') }}</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Add User') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
