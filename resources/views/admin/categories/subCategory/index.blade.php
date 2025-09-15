@extends('layouts/layoutMaster')

@section('title', 'eCommerce Product Category - Apps')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/quill/typography.scss', 'resources/assets/vendor/libs/quill/katex.scss', 'resources/assets/vendor/libs/quill/editor.scss'])
@endsection

@section('page-style')
    @vite('resources/assets/vendor/scss/pages/app-ecommerce.scss')
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/quill/katex.js', 'resources/assets/vendor/libs/quill/quill.js'])
@endsection

@section('page-script')
    {{-- @vite('resources/assets/js/app-ecommerce-category-list.js') --}}
@endsection

@section('content')
    <div class="app-ecommerce-category">
        <div class="card">
            <div class="card-datatable table-responsive">
                <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                    <div class="card-header d-flex flex-wrap py-0 flex-column flex-sm-row">
                        <div>
                            <div id="DataTables_Table_0_filter" class="dataTables_filter me-3 mb-sm-6 mb-0 ps-0"><label><input
                                        type="search" class="form-control ms-0" placeholder="Search Category"
                                        aria-controls="DataTables_Table_0"></label></div>
                        </div>
                        <div class="d-flex justify-content-center justify-content-md-end align-items-baseline">
                            <div
                                class="dt-action-buttons d-flex justify-content-center flex-md-row align-items-baseline pt-0">
                                <div class="dataTables_length" id="DataTables_Table_0_length"><label><select
                                            name="DataTables_Table_0_length" aria-controls="DataTables_Table_0"
                                            class="form-select ms-0">
                                            <option value="7">7</option>
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="70">70</option>
                                            <option value="100">100</option>
                                        </select></label></div>
                                <div class="dt-buttons btn-group flex-wrap"><button
                                        class="btn btn-secondary add-new btn-primary ms-2 waves-effect waves-light"
                                        tabindex="0" aria-controls="DataTables_Table_0" type="button" onclick="window.location='{{ route('sub-categories.create') }}'"><span><i
                                                class="ti ti-plus ti-xs me-0 me-sm-2"></i><span
                                                class="d-none d-sm-inline-block">{{__('Add Sub Category')}}</span></span></button> </div>
                            </div>
                        </div>
                    </div>
                    <table class="datatables-category-list table border-top dataTable no-footer dtr-column"
                        id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info" style="width: 1392px;">
                        <thead>
                            <tr>
                                <th class="control sorting_disabled dtr-hidden" rowspan="1" colspan="1"
                                    style="width: 0px; display: none;" aria-label=""></th>
                                <th class="sorting sorting_desc" tabindex="0" aria-controls="DataTables_Table_0"
                                    rowspan="1" colspan="1" style="width: 684px;"
                                    aria-label="Categories: activate to sort column ascending" aria-sort="descending">
                                    Categories</th>
                                <th class="text-lg-center sorting_disabled" rowspan="1" colspan="1"
                                    style="width: 126px;" aria-label="Actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subCategories as $subCategory)
                                <tr class="odd">
                                    <td class="  control" tabindex="0" style="display: none;"></td>
                                    <td class="sorting_1">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-wrapper me-3 rounded-2 bg-label-secondary">
                                                <div class="avatar"> <img src="{{ $subCategory->image }}" alt="IMAGE"
                                                        class="rounded-2"></div>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center"><span
                                                    class="text-heading text-wrap fw-medium">{{ $subCategory->name }}</span><span
                                                    class="text-truncate mb-0 d-none d-sm-block"><small>{{ $subCategory?->description }}</small></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-sm-center justify-content-sm-center"><button
                                                class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light"><i
                                                    class="ti ti-edit" onclick="window.location='{{ route('sub-categories.edit', $subCategory->id) }}'"></i></button><button
                                                class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></button>
                                            <div class="dropdown-menu dropdown-menu-end m-0"><a href="javascript:0;"
                                                    class="dropdown-item">View</a><a href="javascript:0;"
                                                    class="dropdown-item">Suspend</a></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach    
                        </tbody>
                    </table>
                    <div class="row mx-1">
                        <div class="col-sm-12 col-md-6">
                            <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">
                                Showing 1 to 7 of 14 entries</div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
                                <ul class="pagination">
                                    <li class="paginate_button page-item previous disabled"
                                        id="DataTables_Table_0_previous"><a aria-controls="DataTables_Table_0"
                                            aria-disabled="true" role="link" data-dt-idx="previous" tabindex="-1"
                                            class="page-link"><i class="ti ti-chevron-left ti-sm"></i></a></li>
                                    <li class="paginate_button page-item active"><a href="#"
                                            aria-controls="DataTables_Table_0" role="link" aria-current="page"
                                            data-dt-idx="0" tabindex="0" class="page-link">1</a></li>
                                    <li class="paginate_button page-item "><a href="#"
                                            aria-controls="DataTables_Table_0" role="link" data-dt-idx="1"
                                            tabindex="0" class="page-link">2</a></li>
                                    <li class="paginate_button page-item next" id="DataTables_Table_0_next"><a
                                            href="#" aria-controls="DataTables_Table_0" role="link"
                                            data-dt-idx="next" tabindex="0" class="page-link"><i
                                                class="ti ti-chevron-right ti-sm"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div style="width: 1%;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
