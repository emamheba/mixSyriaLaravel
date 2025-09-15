@extends('layouts/layoutMaster')

@section('title', 'تغيير كلمة المرور - الإدارة')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-account-settings.scss'])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="nav-align-top">
      <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-2 gap-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="{{ route('admin.profile.update') }}">
            <i class="ti-sm ti ti-users me-1_5"></i> الملف الشخصي
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="{{ route('admin.profile.password') }}">
            <i class="ti-sm ti ti-lock me-1_5"></i> كلمة المرور
          </a>
        </li>
      </ul>
    </div>

    @if(session('msg'))
      <div class="alert alert-{{ session('type') == 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
        {{ session('msg') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="card mb-6">
      <h5 class="card-header">
        <i class="ti ti-lock me-2"></i>
        تغيير كلمة المرور
      </h5>
      <div class="card-body pt-1">
        <form action="{{ route('admin.profile.password') }}" method="POST" id="formChangePassword">
          @csrf
          <div class="row">
            <div class="mb-6 col-md-6 form-password-toggle">
              <label class="form-label" for="old_password">كلمة المرور الحالية <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <input class="form-control @error('old_password') is-invalid @enderror" 
                       type="password" 
                       name="old_password" 
                       id="old_password" 
                       placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                       required 
                       autofocus />
                <span class="input-group-text cursor-pointer">
                  <i class="ti ti-eye-off"></i>
                </span>
                @error('old_password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="mb-6 col-md-6 form-password-toggle">
              <label class="form-label" for="password">كلمة المرور الجديدة <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <input class="form-control @error('password') is-invalid @enderror" 
                       type="password" 
                       id="password" 
                       name="password" 
                       placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                       required 
                       minlength="8" />
                <span class="input-group-text cursor-pointer">
                  <i class="ti ti-eye-off"></i>
                </span>
                @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="mb-6 col-md-6 form-password-toggle">
              <label class="form-label" for="password_confirmation">تأكيد كلمة المرور الجديدة <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <input class="form-control" 
                       type="password" 
                       name="password_confirmation" 
                       id="password_confirmation" 
                       placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                       required 
                       minlength="8" />
                <span class="input-group-text cursor-pointer">
                  <i class="ti ti-eye-off"></i>
                </span>
              </div>
            </div>
          </div>
          
          <div class="alert alert-info">
            <h6 class="text-primary mb-2">
              <i class="ti ti-info-circle me-1"></i>
              متطلبات كلمة المرور:
            </h6>
            <ul class="ps-4 mb-0">
              <li class="mb-2">الحد الأدنى 8 أحرف - كلما زادت كلما كان أفضل</li>
              <li class="mb-2">على الأقل حرف واحد صغير</li>
              <li class="mb-2">على الأقل حرف واحد كبير</li>
              <li>على الأقل رقم أو رمز واحد</li>
            </ul>
          </div>
          
          <div class="mt-6">
            <button type="submit" class="btn btn-primary me-3">
              <i class="ti ti-device-floppy me-1"></i>
              حفظ كلمة المرور الجديدة
            </button>
            <button type="reset" class="btn btn-label-secondary">
              <i class="ti ti-refresh me-1"></i>
              إعادة تعيين
            </button>
          </div>
        </form>
      </div>
    </div>
    <div class="card mb-6">
      <div class="card-body">
        <h5 class="mb-4">
          <i class="ti ti-shield-check text-success me-2"></i>
          نصائح الأمان
        </h5>
        <div class="row">
          <div class="col-12">
            <div class="alert alert-warning">
              <h6 class="alert-heading mb-2">
                <i class="ti ti-alert-triangle me-1"></i>
                تنبيه مهم
              </h6>
              <p class="mb-2">بعد تغيير كلمة المرور، سيتم تسجيل خروجك تلقائياً ويجب عليك تسجيل الدخول مرة أخرى بكلمة المرور الجديدة.</p>
            </div>
            
            <div class="d-flex align-items-start mb-4">
              <div class="avatar avatar-sm me-3">
                <span class="avatar-initial rounded bg-label-primary">
                  <i class="ti ti-key"></i>
                </span>
              </div>
              <div>
                <h6 class="mb-1">استخدم كلمة مرور قوية</h6>
                <small class="text-muted">تأكد من أن كلمة المرور تحتوي على أحرف كبيرة وصغيرة وأرقام ورموز</small>
              </div>
            </div>
            
            <div class="d-flex align-items-start mb-4">
              <div class="avatar avatar-sm me-3">
                <span class="avatar-initial rounded bg-label-success">
                  <i class="ti ti-refresh"></i>
                </span>
              </div>
              <div>
                <h6 class="mb-1">قم بتغيير كلمة المرور بانتظام</h6>
                <small class="text-muted">ننصح بتغيير كلمة المرور كل 3-6 أشهر لضمان الأمان</small>
              </div>
            </div>
            
            <div class="d-flex align-items-start">
              <div class="avatar avatar-sm me-3">
                <span class="avatar-initial rounded bg-label-warning">
                  <i class="ti ti-eye-off"></i>
                </span>
              </div>
              <div>
                <h6 class="mb-1">لا تشارك كلمة المرور</h6>
                <small class="text-muted">لا تشارك كلمة المرور مع أي شخص آخر واحتفظ بها في مكان آمن</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordToggles = document.querySelectorAll('.form-password-toggle .input-group-text');
    
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ti-eye-off');
                icon.classList.add('ti-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('ti-eye');
                icon.classList.add('ti-eye-off');
            }
        });
    });
    
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    
    function validatePasswords() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('كلمات المرور غير متطابقة');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);
    
    password.addEventListener('input', function() {
        const value = this.value;
        const strength = getPasswordStrength(value);
        
    });
    
    function getPasswordStrength(password) {
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        return strength;
    }
});
</script>

@endsection