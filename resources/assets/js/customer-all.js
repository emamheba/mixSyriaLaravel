/**
 * App eCommerce customer all
 */

'use strict';

// Datatable Initialization
$(function () {
  let borderColor, bodyBg, headingColor;

  // Dark Mode Handling
  if (window.isDarkStyle) {
    borderColor = window.config.colors_dark.borderColor;
    bodyBg = window.config.colors_dark.bodyBg;
    headingColor = window.config.colors_dark.headingColor;
  } else {
    borderColor = window.config.colors.borderColor;
    bodyBg = window.config.colors.bodyBg;
    headingColor = window.config.colors.headingColor;
  }

  // Initialize DataTable
  const dt_customer_table = $('.datatables-customers');
  if (dt_customer_table.length) {
    const dt_customer = dt_customer_table.DataTable({
      order: [[2, 'desc']],
      dom: `
        <"card-header d-flex flex-wrap flex-md-row flex-column align-items-start align-items-sm-center py-0"
          <"d-flex align-items-center me-5"f>
          <"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end flex-wrap flex-sm-nowrap mb-6 mb-sm-0"lB>
        >t
        <"row mx-1"
          <"col-sm-12 col-md-6"i>
          <"col-sm-12 col-md-6"p>
        >`,
      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search Order',
        paginate: {
          next: '<i class="ti ti-chevron-right ti-sm"></i>',
          previous: '<i class="ti ti-chevron-left ti-sm"></i>'
        }
      },
      buttons: [
        {
          extend: 'collection',
          className: 'btn btn-label-secondary dropdown-toggle me-4 waves-effect waves-light',
          text: '<i class="ti ti-upload ti-xs me-2"></i>Export',
          buttons: [
            {
              extend: 'print',
              text: '<i class="ti ti-printer me-2"></i>Print',
              className: 'dropdown-item',
              customize: function (win) {
                $(win.document.body)
                  .css('color', headingColor)
                  .css('border-color', borderColor)
                  .css('background-color', bodyBg);
                $(win.document.body)
                  .find('table')
                  .addClass('compact')
                  .css('color', 'inherit')
                  .css('border-color', 'inherit')
                  .css('background-color', 'inherit');
              }
            },
            {
              extend: 'csv',
              text: '<i class="ti ti-file me-2"></i>Csv',
              className: 'dropdown-item'
            },
            {
              extend: 'excel',
              text: '<i class="ti ti-file-export me-2"></i>Excel',
              className: 'dropdown-item'
            },
            {
              extend: 'pdf',
              text: '<i class="ti ti-file-text me-2"></i>Pdf',
              className: 'dropdown-item'
            },
            {
              extend: 'copy',
              text: '<i class="ti ti-copy me-2"></i>Copy',
              className: 'dropdown-item'
            }
          ]
        },
        {
          text: "<i class='ti ti-plus me-0 me-sm-1 mb-1 ti-xs'></i><span class='d-none d-sm-inline-block'>Add Customer</span>",
          className: 'add-new btn btn-primary waves-effect waves-light',
          attr: {
            'data-bs-toggle': 'offcanvas',
            'data-bs-target': '#offcanvasEcommerceCustomerAdd'
          }
        }
      ],

    });

    // Delete Record
    $('.datatables-customers tbody').on('click', '.delete-record', function () {
      dt_customer.row($(this).parents('tr')).remove().draw();
    });
  }

  // Select2 Initialization
  $('.select2').each(function () {
    $(this).wrap('<div class="position-relative"></div>').select2({
      placeholder: 'United States',
      dropdownParent: $(this).parent()
    });
  });

  // Phone Mask
  const phoneMaskList = document.querySelectorAll('.phone-mask');
  if (phoneMaskList) {
    phoneMaskList.forEach((phoneMask) => {
      new Cleave(phoneMask, {
        phone: true,
        phoneRegionCode: 'US'
      });
    });
  }

  // Form Validation
  const eCommerceCustomerAddForm = document.getElementById('eCommerceCustomerAddForm');
  if (eCommerceCustomerAddForm) {
    const fv = FormValidation.formValidation(eCommerceCustomerAddForm, {
      fields: {
        customerName: {
          validators: {
            notEmpty: {
              message: 'Please enter fullname'
            }
          }
        },
        customerEmail: {
          validators: {
            notEmpty: {
              message: 'Please enter your email'
            },
            emailAddress: {
              message: 'The value is not a valid email address'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.mb-6'
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        autoFocus: new FormValidation.plugins.AutoFocus()
      }
    });
  }
});