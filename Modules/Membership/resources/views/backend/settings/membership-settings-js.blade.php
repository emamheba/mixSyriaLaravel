<script>
      "use strict";
$(function () {
  const select2 = $('.select2');
  
  // Initialize select2
  if (select2.length) {
    select2.each(function () {
      const $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select option',
        dropdownParent: $this.parent()
      });
    });
  }

  // Form validation
  const membershipSettingsForm = document.querySelector('form');
  
  // Form validation
  if (membershipSettingsForm) {
    FormValidation.formValidation(membershipSettingsForm, {
      fields: {
        'register_membership': {
          validators: {
            notEmpty: {
              message: 'Please select a membership'
            }
          }
        },
        'package_expire_notify_mail_days[]': {
          validators: {
            notEmpty: {
              message: 'Please select at least one day'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.form-group'
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        autoFocus: new FormValidation.plugins.AutoFocus()
      }
    });
  }

  // Show success message if it exists in session
  const successMessage = sessionStorage.getItem('success_message');
  if (successMessage) {
    toastr.success(successMessage);
    sessionStorage.removeItem('success_message');
  }
});
</script>
