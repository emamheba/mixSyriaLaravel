{{-- resources/views/backend/user-membership/manual-payment-modal.blade.php --}}
<div class="modal fade" id="editPaymentGatewayModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">{{ __('Complete Payment Status') }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <form action="{{ route('admin.user.membership.update.manual.payment') }}"
                method="POST">
              @csrf
              <input type="hidden" name="membership_id" id="membership_id">
              <input type="hidden" name="user_firstname" id="user_firstname">
              <input type="hidden" name="user_email" id="user_email">

              <div class="modal-body">
                  <h6>{{ __('User Details') }}</h6>
                  <p class="mb-1">
                      {{ __('User Name:') }} <span class="user_firstname fw-semibold"></span>
                  </p>
                  <p>
                      {{ __('User Email:') }} <span class="user_email fw-semibold"></span>
                  </p>

                  <div class="mt-3">
                      <label class="form-label">{{ __('Payment Image') }}</label>
                      <div>
                          <img class="manual_payment_img img-thumbnail" style="max-width:100%;">
                      </div>
                  </div>
              </div>

              <div class="modal-footer">
                  <button type="button"
                          class="btn btn-outline-secondary"
                          data-bs-dismiss="modal">
                      {{ __('Close') }}
                  </button>
                  <x-btn.submit-btn :title="__('Update')" class="btn btn-primary" />
              </div>
          </form>
      </div>
  </div>
</div>
