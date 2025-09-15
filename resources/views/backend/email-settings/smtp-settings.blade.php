@extends('layouts/layoutMaster')

@section('title', __('SMTP Settings - Email'))

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/@form-validation/popular.js',
        'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
        'resources/assets/vendor/libs/@form-validation/auto-focus.js'
    ])
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            // Form submission with AJAX
            $('#smtp-form').on('submit', function(e) {
                e.preventDefault();
                const submitBtn = $(this).find('[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + __('Updating...'));
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        toastr.success(__('SMTP Settings Updated Successfully'));
                        submitBtn.html(originalText);
                        submitBtn.prop('disabled', false);
                    },
                    error: function(error) {
                        toastr.error(__('Something went wrong!'));
                        submitBtn.html(originalText);
                        submitBtn.prop('disabled', false);
                    }
                });
            });

            // Test Email with AJAX
            $('#test-email-form').on('submit', function(e) {
                e.preventDefault();
                const submitBtn = $(this).find('[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + __('Sending...'));
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        toastr.success(__('Test Email Sent Successfully'));
                        submitBtn.html(originalText);
                        submitBtn.prop('disabled', false);
                    },
                    error: function(error) {
                        toastr.error(error.responseJSON.message || __('Failed to send test email'));
                        submitBtn.html(originalText);
                        submitBtn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-xl-6 col-lg-6">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">{{ __('SMTP Settings') }}</h5>
            </div>
            <div class="card-body">
                <form id="smtp-form" action="{{ route('admin.email.smtp.update.settings') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label" for="site_global_email">{{ __('Global Email') }}</label>
                            <input type="email" class="form-control" id="site_global_email" name="site_global_email" value="{{ get_static_option('site_global_email') }}" placeholder="{{ __('Use your web mail here') }}" required>
                            <div class="form-text">{{ __('Use your web mail here') }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label" for="site_smtp_mail_mailer">{{ __('SMTP Mailer') }}</label>
                            <select class="form-select" id="site_smtp_mail_mailer" name="site_smtp_mail_mailer">
                                <option value="smtp" @if(get_static_option('site_smtp_mail_mailer') == 'smtp') selected @endif>{{ __('SMTP') }}</option>
                                <option value="sendmail" @if(get_static_option('site_smtp_mail_mailer') == 'sendmail') selected @endif>{{ __('SendMail') }}</option>
                                <option value="mailgun" @if(get_static_option('site_smtp_mail_mailer') == 'mailgun') selected @endif>{{ __('Mailgun') }}</option>
                                <option value="postmark" @if(get_static_option('site_smtp_mail_mailer') == 'postmark') selected @endif>{{ __('Postmark') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label" for="site_smtp_mail_host">{{ __('SMTP Mail Host') }}</label>
                            <input type="text" class="form-control" id="site_smtp_mail_host" name="site_smtp_mail_host" value="{{ get_static_option('site_smtp_mail_host') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label" for="site_smtp_mail_port">{{ __('SMTP Mail Port') }}</label>
                            <select class="form-select" id="site_smtp_mail_port" name="site_smtp_mail_port">
                                <option value="587" @if(get_static_option('site_smtp_mail_port') == '587') selected @endif>587</option>
                                <option value="465" @if(get_static_option('site_smtp_mail_port') == '465') selected @endif>465</option>
                                <option value="25" @if(get_static_option('site_smtp_mail_port') == '25') selected @endif>25</option>
                                <option value="2525" @if(get_static_option('site_smtp_mail_port') == '2525') selected @endif>2525</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label" for="site_smtp_mail_username">{{ __('SMTP Mail Username') }}</label>
                            <input type="text" class="form-control" id="site_smtp_mail_username" name="site_smtp_mail_username" value="{{ get_static_option('site_smtp_mail_username') }}" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label" for="site_smtp_mail_password">{{ __('SMTP Mail Password') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="site_smtp_mail_password" name="site_smtp_mail_password" value="{{ get_static_option('site_smtp_mail_password') }}" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label" for="site_smtp_mail_encryption">{{ __('SMTP Mail Encryption') }}</label>
                            <select class="form-select" id="site_smtp_mail_encryption" name="site_smtp_mail_encryption">
                                <option value="ssl" @if(get_static_option('site_smtp_mail_encryption') == 'ssl') selected @endif>{{ __('SSL') }}</option>
                                <option value="tls" @if(get_static_option('site_smtp_mail_encryption') == 'tls') selected @endif>{{ __('TLS') }}</option>
                                <option value="none" @if(get_static_option('site_smtp_mail_encryption') == 'none') selected @endif>{{ __('None') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">{{ __('Update Settings') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-6">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">{{ __('Test Email Configuration') }}</h5>
            </div>
            <div class="card-body">
                <form id="test-email-form" action="{{ route('admin.email.smtp.settings.test') }}" method="POST">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label" for="email">{{ __('Send Test Email To') }}</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('Enter email address') }}" required>
                            <div class="form-text">{{ __('Well send a test email to verify your SMTP configuration') }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">{{ __('Send Test Email') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('site_smtp_mail_password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle icon
        const icon = this.querySelector('i');
        if (type === 'text') {
            icon.classList.remove('ti-eye');
            icon.classList.add('ti-eye-off');
        } else {
            icon.classList.remove('ti-eye-off');
            icon.classList.add('ti-eye');
        }
    });
</script>
@endsection