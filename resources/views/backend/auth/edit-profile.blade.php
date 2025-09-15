@extends('layouts/layoutMaster')

@section('title', 'تحديث الملف الشخصي - الإدارة')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
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
          <a class="nav-link active" href="{{ route('admin.profile.edit') }}">
            <i class="ti-sm ti ti-users me-1_5"></i> الملف الشخصي
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('admin.profile.password') }}">
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
      <div class="card-body">
        <div class="d-flex align-items-start align-items-sm-center gap-6">
          
          <img src="{{ $imageUrl }}" 
               alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
          
          <div class="button-wrapper">
            <label for="upload" class="btn btn-primary me-3 mb-4" tabindex="0">
              <span class="d-none d-sm-block">رفع صورة جديدة</span>
              <i class="ti ti-upload d-block d-sm-none"></i>
            </label>
            <button type="button" class="btn btn-label-secondary account-image-reset mb-4">
              <i class="ti ti-refresh-dot d-block d-sm-none"></i>
              <span class="d-none d-sm-block">إعادة تعيين</span>
            </button>
            <div>مسموح JPG, GIF أو PNG. الحد الأقصى 800KB</div>
          </div>
        </div>
      </div>
      
      <div class="card-body pt-4">
        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" id="formAccountSettings">
          @csrf
          <input type="file" id="upload" name="image" class="account-file-input" hidden accept="image/png, image/jpeg, image/jpg, image/gif" />

          <div class="row">
            <div class="mb-4 col-md-6">
              <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
              <input class="form-control @error('name') is-invalid @enderror" 
                     type="text" 
                     id="name" 
                     name="name" 
                     value="{{ old('name', $admin->name) }}" 
                     required 
                     autofocus />
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="mb-4 col-md-6">
              <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
              <input class="form-control @error('email') is-invalid @enderror" 
                     type="email" 
                     id="email" 
                     name="email" 
                     value="{{ old('email', $admin->email) }}" 
                     required />
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="mb-4 col-12">
              <label for="about" class="form-label">نبذة شخصية</label>
              <textarea class="form-control @error('about') is-invalid @enderror" 
                        id="about" 
                        name="about" 
                        rows="4" 
                        maxlength="1000"
                        placeholder="اكتب نبذة مختصرة عنك...">{{ old('about', $admin->about) }}</textarea>
              @error('about')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">الحد الأقصى 1000 حرف</div>
            </div>
          </div>
          
          <div class="mt-4">
            <button type="submit" class="btn btn-primary me-3">
              <i class="ti ti-device-floppy me-1"></i>
              حفظ التغييرات
            </button>
            <button type="reset" class="btn btn-label-secondary" id="formResetButton">
              <i class="ti ti-refresh me-1"></i>
              إلغاء
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadInput = document.getElementById('upload');
    const uploadedAvatar = document.getElementById('uploadedAvatar');
    const imageResetButton = document.querySelector('.account-image-reset');
    const formResetButton = document.getElementById('formResetButton');
    const accountForm = document.getElementById('formAccountSettings');

    const currentAvatar = '{{ $imageUrl }}';

    uploadInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 800 * 1024) { 
                alert('حجم الملف كبير جداً. الحد الأقصى 800KB');
                this.value = '';
                return;
            }
            
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('نوع الملف غير مدعوم. مسموح فقط JPG, PNG, GIF');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(event) {
                uploadedAvatar.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    
    imageResetButton.addEventListener('click', function() {
        uploadInput.value = '';
        uploadedAvatar.src = currentAvatar;
    });

    formResetButton.addEventListener('click', function() {
        accountForm.reset();
        uploadedAvatar.src = currentAvatar;
    });
    
    accountForm.addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        
        if (!name) {
            e.preventDefault();
            alert('الرجاء إدخال الاسم الكامل');
            document.getElementById('name').focus();
            return;
        }
        
        if (!email) {
            e.preventDefault();
            alert('الرجاء إدخال البريد الإلكتروني');
            document.getElementById('email').focus();
            return;
        }
        
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            e.preventDefault();
            alert('صيغة البريد الإلكتروني غير صحيحة');
            document.getElementById('email').focus();
            return;
        }
    });
});
</script>

@endsection