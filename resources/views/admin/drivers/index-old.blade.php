@extends('layouts/layoutMaster')
@section('title', 'Drivers List - Apps')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/select2/select2.scss'
  ])
@endsection

@section('vendor-script')
@vite([
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/cleavejs/cleave.js',
'resources/assets/vendor/libs/cleavejs/cleave-phone.js'
])
@endsection

@section('page-script')
{{-- @vite('resources/assets/js/app-drivers-list.js') --}}
@endsection

@section('content')
<!-- Drivers List Table -->
<div class="card">
  <div class="card-datatable table-responsive">
    <table class="datatables-drivers table border-top">
      <thead>
        <tr>
          <th></th>
          <th>{{ __('Name') }}</th>
          <th>{{ __('Phone') }}</th>
          <th>{{ __('Salary') }}</th>
          <th>{{ __('Identity Number') }}</th>
          <th>{{ __('IBAN') }}</th>
          <th>{{ __('Country') }}</th>
          <th>{{ __('Region') }}</th>
          <th>{{ __('City') }}</th>
          <th>{{ __('District') }}</th>
          <th>{{ __('Status') }}</th>
          <th>{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($drivers as $driver)
        <tr>
          <td></td>
          <td>{{ $driver->name }}</td>
          <td>{{ $driver->phone }}</td>
          <td>{{ $driver->salary }}</td>
          <td>{{ $driver->identity_number }}</td>
          <td>{{ $driver->iban }}</td>
          <td>{{ $driver->country?->name }}</td>
          <td>{{ $driver->region?->name }}</td>
          <td>{{ $driver->city?->name }}</td>
          <td>{{ $driver->district?->name }}</td>
          <td>
            <span class="badge bg-{{ $driver->status === 'active' ? 'success' : 'danger' }}">
              {{ __($driver->status) }}
            </span>
          </td>
          <td>
            <div class="d-flex">
              <a href="{{ route('drivers.edit', $driver->id) }}" class="btn btn-sm btn-icon">
                <i class="bx bx-edit"></i>
              </a>
              <form action="{{ route('drivers.destroy', $driver->id) }}" method="POST" class="ms-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-icon">
                  <i class="bx bx-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Offcanvas to add new driver -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasDriverAdd" aria-labelledby="offcanvasDriverAddLabel">
  <div class="offcanvas-header">
    <h5 id="offcanvasDriverAddLabel" class="offcanvas-title">{{ __('Add Driver') }}</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body border-top mx-0 flex-grow-0">
    <form class="driver-add pt-0" id="driverAddForm" onsubmit="return false">
      <div class="driver-add-basic mb-4">
        <h6 class="mb-6">{{ __('Basic Information') }}</h6>
        <div class="mb-6">
          <label class="form-label" for="driver-add-name">{{ __('Name') }}*</label>
          <input type="text" class="form-control" id="driver-add-name" placeholder="{{ __('John Doe') }}" name="name" aria-label="{{ __('John Doe') }}" />
        </div>
        <div class="mb-6">
          <label class="form-label" for="driver-add-phone">{{ __('Phone') }}*</label>
          <input type="text" id="driver-add-phone" class="form-control" placeholder="{{ __('+1234567890') }}" aria-label="{{ __('+1234567890') }}" name="phone" />
        </div>
        <div class="mb-6">
          <label class="form-label" for="driver-add-salary">{{ __('Salary') }}*</label>
          <input type="number" id="driver-add-salary" class="form-control" placeholder="{{ __('1000') }}" aria-label="{{ __('1000') }}" name="salary" />
        </div>
        <div class="mb-6">
          <label class="form-label" for="driver-add-identity-number">{{ __('Identity Number') }}*</label>
          <input type="text" id="driver-add-identity-number" class="form-control" placeholder="{{ __('123456789') }}" aria-label="{{ __('123456789') }}" name="identity_number" />
        </div>
        <div class="mb-6">
          <label class="form-label" for="driver-add-iban">{{ __('IBAN') }}*</label>
          <input type="text" id="driver-add-iban" class="form-control" placeholder="{{ __('SA1234567890123456789012') }}" aria-label="{{ __('SA1234567890123456789012') }}" name="iban" />
        </div>
      </div>

      <div class="driver-add-address mb-6 pt-4">
        <h6 class="mb-6">{{ __('Address Information') }}</h6>
        <div class="mb-6">
          <label class="form-label" for="driver-add-country">{{ __('Country') }}</label>
          <select id="driver-add-country" class="select2 form-select" name="country_id">
            <option value="">{{ __('Select Country') }}</option>
            @foreach($countries as $country)
            <option value="{{ $country->id }}">{{ $country->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-6">
          <label class="form-label" for="driver-add-region">{{ __('Region') }}</label>
          <select id="driver-add-region" class="select2 form-select" name="region_id">
            <option value="">{{ __('Select Region') }}</option>
            @foreach($regions as $region)
            <option value="{{ $region->id }}">{{ $region->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-6">
          <label class="form-label" for="driver-add-city">{{ __('City') }}</label>
          <select id="driver-add-city" class="select2 form-select" name="city_id">
            <option value="">{{ __('Select City') }}</option>
            @foreach($cities as $city)
            <option value="{{ $city->id }}">{{ $city->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-6">
          <label class="form-label" for="driver-add-district">{{ __('District') }}</label>
          <select id="driver-add-district" class="select2 form-select" name="district_id">
            <option value="">{{ __('Select District') }}</option>
            @foreach($districts as $district)
            <option value="{{ $district->id }}">{{ $district->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-6">
          <label class="form-label" for="driver-add-address">{{ __('Address') }}</label>
          <input type="text" id="driver-add-address" class="form-control" placeholder="{{ __('45 Roker Terrace') }}" aria-label="{{ __('45 Roker Terrace') }}" name="address" />
        </div>
      </div>

      <div class="mb-6">
        <label class="form-label" for="driver-add-status">{{ __('Status') }}</label>
        <select id="driver-add-status" class="select2 form-select" name="status">
          <option value="active">{{ __('Active') }}</option>
          <option value="inactive">{{ __('Inactive') }}</option>
        </select>
      </div>

      <div>
        <button type="submit" class="btn btn-primary me-sm-4 data-submit">{{ __('Add') }}</button>
        <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">{{ __('Discard') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection









@extends('layouts/layoutMaster')

@section('title', 'إضافة سائق جديد - التطبيقات')

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

@section('content')
<div class="app-ecommerce">

  <!-- إضافة سائق جديد -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
    <div class="d-flex flex-column justify-content-center">
      <h4 class="mb-1">إضافة سائق جديد</h4>
      <p class="mb-0">إدارة السائقين في النظام</p>
    </div>
    <div class="d-flex align-content-center flex-wrap gap-4">
      <div class="d-flex gap-4">
        <button class="btn btn-label-secondary">إلغاء</button>
        <button class="btn btn-label-primary">حفظ كمسودة</button>
      </div>
      <button type="submit" form="driver-form" class="btn btn-primary">نشر السائق</button>
    </div>
  </div>

  <!-- الإحصائيات -->
  <div class="row mb-6">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">عدد السائقين</h5>
          <p class="card-text">{{ $totalDrivers }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">السائقون المتاحون</h5>
          <p class="card-text">{{ $availableDrivers }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">السائقون مع طلبات حالية</h5>
          <p class="card-text">{{ $driversWithOrders }}</p>
        </div>
      </div>
    </div>
  </div>

  <!-- النموذج -->
  <form id="driver-form" action="{{ route('drivers.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="image_path" id="image-path"> 

    <div class="row">
      <div class="col-12 col-lg-8">
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-tile mb-0">معلومات السائق</h5>
          </div>
          <div class="card-body">
            <div class="mb-6">
              <label class="form-label" for="driver-name">الاسم</label>
              <input type="text" class="form-control" id="driver-name" placeholder="اسم السائق" name="name" aria-label="اسم السائق" required>
            </div>
            <div class="row mb-6">
              <div class="col">
                <label class="form-label" for="driver-license">رقم الرخصة</label>
                <input type="text" class="form-control" id="driver-license" placeholder="رقم الرخصة" name="license_number" aria-label="رقم الرخصة" required>
              </div>
              <div class="col">
                <label class="form-label" for="driver-phone">رقم الهاتف</label>
                <input type="text" class="form-control" id="driver-phone" placeholder="رقم الهاتف" name="phone" aria-label="رقم الهاتف" required>
              </div>
            </div>
            <div class="mb-6">
              <label class="form-label" for="basic-default-upload-file">صورة الرخصة</label>
              <input type="file" class="form-control" name="license_image" id="basic-default-upload-file" accept="image/*" required/>
            </div>
            <div class="mb-6">
              <label class="form-label" for="driver-address">العنوان</label>
              <textarea class="form-control" id="driver-address" placeholder="عنوان السائق" name="address" aria-label="عنوان السائق"></textarea>
            </div>
            <div class="row mb-6">
              <div class="col">
                <label class="form-label" for="driver-status">الحالة</label>
                <select class="form-select" id="driver-status" name="status" required>
                  <option value="1">نشط</option>
                  <option value="0">غير نشط</option>
                </select>
              </div>
              <div class="col">
                <label class="form-label" for="driver-availability">التوفر</label>
                <select class="form-select" id="driver-availability" name="availability" required>
                  <option value="1">متاح</option>
                  <option value="0">غير متاح</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /العمود الأول -->

      <!-- العمود الثاني -->
      <div class="col-12 col-lg-4">
        <!-- تنظيم -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-title mb-0">تنظيم</h5>
          </div>
          <div class="card-body">
            <!-- المنطقة -->
            <div class="mb-6">
              <label class="form-label" for="driver-region">المنطقة</label>
              <select class="form-select" id="driver-region" name="region_id" required>
                @foreach($regions as $region)
                  <option value="{{ $region->id }}">{{ $region->name }}</option>
                @endforeach
              </select>
            </div>
            <!-- المركبة -->
            {{-- <div class="mb-6">
              <label class="form-label" for="driver-vehicle">المركبة</label>
              <select class="form-select" id="driver-vehicle" name="vehicle_id" required>
                @foreach($vehicles as $id => $name)
                  <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
              </select>
            </div> --}}
          </div>
        </div>
        <!-- /تنظيم -->
      </div>
      <!-- /العمود الثاني -->
    </div>

    <!-- زر الحفظ -->
    <div class="text-end mt-4">
      <button type="submit" class="btn btn-primary">حفظ السائق</button>
    </div>
  </form>
  <!-- /النموذج -->
</div>

<script>
  'use strict';

  // Dropzone
  const previewTemplate = `<div class="dz-preview dz-file-preview">
    <div class="dz-details">
      <div class="dz-thumbnail">
        <img data-dz-thumbnail>
        <span class="dz-nopreview">No preview</span>
        <div class="dz-success-mark"></div>
        <div class="dz-error-mark"></div>
        <div class="dz-error-message"><span data-dz-errormessage></span></div>
        <div class="progress">
          <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
        </div>
      </div>
      <div class="dz-filename" data-dz-name></div>
      <div class="dz-size" data-dz-size></div>
    </div>
  </div>`;

  const dropzoneBasic = document.querySelector('#dropzone-basic');
  if (dropzoneBasic) {
    const myDropzone = new Dropzone(dropzoneBasic, {
      url: "{{ route('drivers.store') }}", // Route الخاص برفع الصور
      paramName: "license_image", // اسم الحقل الذي سيتم إرسال الصورة عبره
      maxFilesize: 5, // الحد الأقصى لحجم الملف
      acceptedFiles: '.jpg,.jpeg,.png,.gif', // أنواع الملفات المقبولة
      addRemoveLinks: true, // إظهار رابط لإزالة الملف
      maxFiles: 1, // الحد الأقصى لعدد الملفات
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // إضافة CSRF Token
      },
      init: function () {
        this.on("success", function (file, response) {
          console.log("File uploaded successfully:", response);
          // تخزين مسار الصورة في الحقل المخفي
          document.getElementById('image-path').value = response.path;
        });
        this.on("error", function (file, errorMessage) {
          console.error("Error uploading file:", errorMessage);
        });
      }
    });
  }
</script>

@endsection