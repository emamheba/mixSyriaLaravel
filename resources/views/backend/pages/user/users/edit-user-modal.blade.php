<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">{{ __('Edit User') }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
          </div>
          <form action="{{ route('admin.users.edit.info') }}" method="POST">
              @csrf
              <div class="modal-body">
                  <input type="hidden" name="edit_user_id" value="{{ $user->id }}">
                  <div class="row g-3">
                      <div class="col-md-6">
                          <label class="form-label" for="edit_first_name">{{ __('First Name') }}</label>
                          <input type="text" id="edit_first_name" name="edit_first_name"
                              class="form-control" value="{{ $user->first_name }}" required>
                      </div>
                      <div class="col-md-6">
                          <label class="form-label" for="edit_last_name">{{ __('Last Name') }}</label>
                          <input type="text" id="edit_last_name" name="edit_last_name"
                              class="form-control" value="{{ $user->last_name }}" required>
                      </div>
                      <div class="col-md-6">
                          <label class="form-label" for="edit_username">{{ __('Username') }}</label>
                          <input type="text" id="edit_username" name="edit_username"
                              class="form-control" value="{{ $user->username }}" required>
                      </div>
                      <div class="col-md-6">
                          <label class="form-label" for="edit_email">{{ __('Email') }}</label>
                          <input type="email" id="edit_email" name="edit_email"
                              class="form-control" value="{{ $user->email }}" required>
                      </div>
                      <div class="col-md-6">
                          <label class="form-label" for="edit_phone">{{ __('Phone') }}</label>
                          <input type="text" id="edit_phone" name="edit_phone"
                              class="form-control" value="{{ $user->phone }}" required>
                      </div>
                      <!-- Country field -->
                      <div class="col-md-6">
                          <label class="form-label" for="edit_country">{{ __('Country') }}</label>
                          <select id="edit_country" name="edit_country" class="form-select">
                              @if($user->country_id)
                              <option value="{{ $user->country_id }}">{{ $user->user_country->country }}</option>
                              @else
                              <option value="">{{ __('Select Country') }}</option>
                              @endif
                          
                              @foreach($countries as $country)
                              @if($user->country_id != $country->id)
                                  <option value="{{ $country->id }}">{{ $country->country }}</option>
                              @endif
                              @endforeach
                          </select>
                      </div>

                      <!-- State field -->
                      <div class="col-md-6">
                          <label class="form-label" for="edit_state">{{ __('State') }}</label>
                          <select id="edit_state" name="edit_state" class="form-select">
                              <option value="">{{ __('Select State') }}</option>
                              @foreach ($states as $state)
                                  <option value="{{ $state->id }}"
                                      @if ($state->id == $user->state_id) selected @endif>
                                      {{ $state->state }}
                                  </option>
                              @endforeach
                          </select>
                      </div>

                      <!-- City field -->
                      <div class="col-md-6">
                          <label class="form-label" for="edit_city">{{ __('City') }}</label>
                          <select id="edit_city" name="edit_city" class="form-select">
                              <option value="">{{ __('Select City') }}</option>
                              @foreach ($cities as $city)
                                  <option value="{{ $city->id }}"
                                      @if ($city->id == $user->city_id) selected @endif>
                                      {{ $city->city }}
                                  </option>
                              @endforeach
                          </select>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                  <button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
              </div>
          </form>
      </div>
  </div>
</div>