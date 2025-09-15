@extends('layouts/layoutMaster')

@section('title', __('Categories'))

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/toastr/toastr.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/toastr/toastr.js'])
@endsection

@section('page-script')
    <script>
        const createRoute = "{{ route('categories.create') }}";
        const changeStatusUrl = "{{ url('admin/categories/change-status') }}";
    </script>

    @vite(['resources/assets/js/app-ecommerce-product-list.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle Status
            document.querySelectorAll('.toggle-status').forEach(input => {
                input.addEventListener('change', function() {
                    const categoryId = this.dataset.id;
                    const newStatus = this.checked ? 1 : 0;
                    const checkbox = this;

                    fetch(`${changeStatusUrl}/${categoryId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                status: newStatus
                            })
                        })
                        .then(async response => {
                            if (!response.ok) {
                                const html = await response.text(); // لو رجع HTML بدل JSON
                                throw new Error('Invalid response: ' + html.substring(0,
                                    100));
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // const statusElement = checkbox.closest('td').querySelector('.status-value');
                                // if (statusElement) statusElement.textContent = newStatus;
                                // toastr.success('تم تحديث الحالة بنجاح');
                            } else {
                                // toastr.error('فشل في تحديث الحالة');
                                // checkbox.checked = !checkbox.checked;
                            }
                        })
                        .catch(error => {
                            //   console.error('Error:', error);
                            //   toastr.error('حدث خطأ أثناء تحديث الحالة');
                            //   checkbox.checked = !checkbox.checked;
                        });
                });
            });
        });
    </script>
@endsection

@section('content')
    <div class="app-ecommerce-product">
        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="datatables-products table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('النوع') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td></td>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center product-name">
                                        <div class="avatar-wrapper">
                                            <div class="avatar avatar me-4 rounded-2 bg-label-secondary">
                                                @if ($category->image)
                                                    <img src="{{ $category->image }}" alt="category-{{ $category->id }}"
                                                        class="rounded-2">
                                                @else
                                                    <span
                                                        class="avatar-initial rounded-2 bg-label-primary">{{ substr($category->name, 0, 2) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <h6 class="text-nowrap mb-0">{{ $category->name }}</h6>
                                            <small
                                                class="text-truncate d-none d-sm-block">{{ $category->description }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $type = $category->category_type;
                                        $typeText = $type;
                                        $badgeClass = 'bg-label-secondary';
                                        if ($type == 'sell') {
                                            $badgeClass = 'bg-label-success';
                                            $typeText = 'بيع';
                                        } elseif ($type == 'rent') {
                                            $badgeClass = 'bg-label-info';
                                            $typeText = 'إيجار';
                                        } elseif ($type == 'job') {
                                            $badgeClass = 'bg-label-warning';
                                            $typeText = 'وظيفة';
                                        } elseif ($type == 'service') {
                                            $badgeClass = 'bg-label-primary';
                                            $typeText = 'خدمة';
                                        } else {
                                            $typeText = 'غير محدد';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $typeText }}</span>
                                </td>
                                <td>
                                    <span class="text-truncate">
                                        <label class="switch switch-primary switch-sm">
                                            <input type="checkbox" class="switch-input toggle-status"
                                                data-id="{{ $category->id }}"
                                                {{ $category->status == 1 ? 'checked' : '' }}>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                        </label>
                                        <span class="d-none status-value">{{ $category->status }}</span>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-inline-block text-nowrap">
                                        <button
                                            class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light"
                                            onclick="window.location='{{ route('categories.edit', $category->id) }}'">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <button
                                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect waves-light dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical ti-md"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end m-0">
                                            <a href="#" class="dropdown-item">{{ __('View') }}</a>
                                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="dropdown-item text-danger">{{ __('Delete') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
