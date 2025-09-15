$(document).ready(function () {
  // Initialize DataTable
  var table = $('.datatables-products').DataTable({
    dom:
      '<"card-header d-flex border-top rounded-0 flex-wrap py-0 flex-column flex-md-row align-items-start"' +
      '<"me-5 ms-n4 pe-5 mb-n6 mb-md-0"f>' +
      '<"d-flex justify-content-start justify-content-md-end align-items-baseline"<"dt-action-buttons d-flex flex-column align-items-start align-items-sm-center justify-content-sm-center pt-0 gap-sm-4 gap-sm-0 flex-sm-row"lB>>' +
      '>t' +
      '<"row"' +
      '<"col-sm-12 col-md-6"i>' +
      '<"col-sm-12 col-md-6"p>' +
      '>',
    lengthMenu: [7, 10, 20, 50, 70, 100], // Number of rows per page
    language: {
      sLengthMenu: '_MENU_',
      search: '',
      searchPlaceholder: 'Search Product',
      info: 'Displaying _START_ to _END_ of _TOTAL_ entries',
      paginate: {
        next: '<i class="ti ti-chevron-right ti-sm"></i>',
        previous: '<i class="ti ti-chevron-left ti-sm"></i>'
      }
    },
    buttons: [
      {
        extend: 'collection',
        className: 'btn btn-label-secondary dropdown-toggle me-4 waves-effect waves-light',
        text: '<i class="ti ti-upload me-1 ti-xs"></i>Export',
        buttons: [
          {
            extend: 'print',
            text: '<i class="ti ti-printer me-2" ></i>Print',
            className: 'dropdown-item',
            exportOptions: {
              columns: [1, 2, 3, 4, 5, 6, 7]
            }
          },
          {
            extend: 'csv',
            text: '<i class="ti ti-file me-2" ></i>Csv',
            className: 'dropdown-item',
            exportOptions: {
              columns: [1, 2, 3, 4, 5, 6, 7]
            }
          },
          {
            extend: 'excel',
            text: '<i class="ti ti-file-export me-2"></i>Excel',
            className: 'dropdown-item',
            exportOptions: {
              columns: [1, 2, 3, 4, 5, 6, 7]
            }
          },
          {
            extend: 'pdf',
            text: '<i class="ti ti-file-text me-2"></i>Pdf',
            className: 'dropdown-item',
            exportOptions: {
              columns: [1, 2, 3, 4, 5, 6, 7]
            }
          },
          {
            extend: 'copy',
            text: '<i class="ti ti-copy me-2"></i>Copy',
            className: 'dropdown-item',
            exportOptions: {
              columns: [1, 2, 3, 4, 5, 6, 7]
            }
          }
        ]
      },
      {
        text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">أظافة </span>',
        className: 'add-new btn btn-primary ms-2 ms-sm-0 waves-effect waves-light',
        action: function () {
          window.location.href = createRoute;
        }
      }
    ]
  });

  // Filter by Status
  $('#ProductStatus').on('change', function () {
    var status = $(this).val();
    table.column(8).search(status).draw();
  });

  // Filter by Category
  $('#ProductCategory').on('change', function () {
    var category = $(this).val();
    table.column(3).search(category).draw();
  });

  // Filter by Stock
  $('#ProductStock').on('change', function () {
    var stock = $(this).val();
    table.column(4).search(stock).draw();
  });
});
