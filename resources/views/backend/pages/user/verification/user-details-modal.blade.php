<!-- Create this as a partial view and include it in your main layout -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-simple">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('User Verification Details') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
      </div>
      <div class="modal-body">
        <!-- Content will be loaded via AJAX -->
        <div class="text-center">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">{{ __('Loading...') }}</span>
          </div>
          <p class="mt-2">{{ __('Loading user details...') }}</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
      </div>
    </div>
  </div>
</div>
