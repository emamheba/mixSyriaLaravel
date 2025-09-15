/**
 * App eCommerce subCategory List
 */

'use strict';

// Comment editor

const commentEditor = document.querySelector('.comment-editor');

if (commentEditor) {
  new Quill(commentEditor, {
    modules: {
      toolbar: '.comment-toolbar'
    },
    placeholder: 'Write a Comment...',
    theme: 'snow'
  });
}


// subCategory.js
$(function () {
    let borderColor, bodyBg, headingColor;
    if (isDarkStyle) {
        borderColor = config.colors_dark.borderColor;
        bodyBg = config.colors_dark.bodyBg;
        headingColor = config.colors_dark.headingColor;
    } else {
        borderColor = config.colors.borderColor;
        bodyBg = config.colors.bodyBg;
        headingColor = config.colors.headingColor;
    }

    // DataTable Configuration
    var dt_subCategory_list_table = $('.datatables-subCategory-list');

    if (dt_subCategory_list_table.length) {
        var dt_subCategory = dt_subCategory_list_table.DataTable({
        ajax: {
            url: '/api/subCategories',
            dataSrc: function (json) {
            return json.map(function (subCategory) {
                return {
                id: subCategory.id,
                name: subCategory.name,
                category: subCategory.category.name,
                image: subCategory.image,
                status: subCategory.status,
                description: subCategory.description
                };
            });
            }
        },
        columns: [
            { data: '' }, // Responsive control column
            { data: 'id' },
            { data: 'name' },
            { data: 'category' },
            { data: 'status' },
            { data: '' } // Actions column
        ],
        columnDefs: [
            {
            className: 'control',
            orderable: false,
            targets: 0,
            render: function () {
                return '';
            }
            },
            {
            targets: 1,
            orderable: false,
            render: function () {
                return '<input type="checkbox" class="dt-checkboxes form-check-input">';
            }
            },
            {
            targets: 2,
            render: function (data, type, full, meta) {
                return `
                <div class="d-flex align-items-center">
                    <div class="avatar me-3">
                    ${full.image ?
                        `<img src="${full.image}" alt="${full.name}" class="rounded-2">` :
                        `<span class="avatar-initial rounded-2 bg-label-primary">${full.name.charAt(0)}</span>`
                    }
                    </div>
                    <div class="d-flex flex-column">
                    <span class="text-heading fw-medium">${full.name}</span>
                    <small class="text-muted">${full.description || 'No description'}</small>
                    </div>
                </div>
                `;
            }
            },
            {
            targets: 4,
            render: function (data, type, full, meta) {
                return full.status === 1 ?
                '<span class="badge bg-success">Active</span>' :
                '<span class="badge bg-danger">Inactive</span>';
            }
            },
            {
            targets: -1,
            title: 'Actions',
            orderable: false,
            render: function (data, type, full, meta) {
                return `
                  <div class="d-flex align-items-sm-center justify-content-sm-center">
                    <!-- Edit Button -->
                    <button
                      class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light edit-subcategory me-2"
                      data-id="${full.id}"
                      data-name="${full.subCategories}"
                      data-slug="${full.slug}"
                      data-description="${full.subCategory_detail}"
                      data-image="${full.cat_image}"
                      data-icon="${full.icon}"
                      data-mobile-icon="${full.mobile_icon}"
                    >
                      <i class="ti ti-edit"></i>
                    </button>

                    <!-- Action Dropdown -->
                    <div class="dropdown">
                      <button
                        class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light dropdown-toggle hide-arrow"
                        data-bs-toggle="dropdown"
                      >
                        <i class="ti ti-dots-vertical ti-md"></i>
                      </button>
                      <div class="dropdown-menu dropdown-menu-end m-0" data-bs-auto-close="false">
                        <a
                          href="javascript:void(0);"
                          class="dropdown-item view-subcategory"
                          data-id="${full.id}"
                        >
                          View
                        </a>
                        <a
                          href="javascript:void(0);"
                          class="dropdown-item delete-subcategory text-danger"
                          data-id="${full.id}"
                        >
                          Delete
                        </a>
                      </div>
                    </div>
                  </div>
                `;
              }
            }
            // render: function (data, type, full, meta) {
            //     return `
            //     <div class="d-flex align-items-sm-center justify-content-sm-center">
            //         <button class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light">
            //         <i class="ti ti-edit"></i>
            //         </button>
            //         <button class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light dropdown-toggle hide-arrow"
            //         data-bs-toggle="dropdown">
            //         <i class="ti ti-dots-vertical ti-md"></i>
            //         </button>
            //         <div class="dropdown-menu dropdown-menu-end m-0">
            //         <a href="javascript:void(0);" class="dropdown-item">View</a>
            //         <a href="javascript:void(0);" class="dropdown-item">Delete</a>
            //         </div>
            //     </div>
            //     `;
            // }
            // }
        ],
        order: [[2, 'asc']],
        dom:
          '<"card-header d-flex flex-wrap py-0 flex-column flex-sm-row"' +
          '<f>' +
          '<"d-flex justify-content-center justify-content-md-end align-items-baseline"<"dt-action-buttons d-flex justify-content-center flex-md-row align-items-baseline"lB>>' +
          '>t' +
          '<"row mx-1"' +
          '<"col-sm-12 col-md-6"i>' +
          '<"col-sm-12 col-md-6"p>' +
          '>',
        lengthMenu: [7, 10, 20, 50, 70, 100], //for length of menu
        language: {
          sLengthMenu: '_MENU_',
          search: '',
          searchPlaceholder: 'Search subCategory',
          paginate: {
            next: '<i class="ti ti-chevron-right ti-sm"></i>',
            previous: '<i class="ti ti-chevron-left ti-sm"></i>'
          }
        },
        // Button for offcanvas
        buttons: [
          {
            text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span class="d-none d-sm-inline-block">Add subCategory</span>',
            className: 'add-new btn btn-primary ms-2 waves-effect waves-light',
             attr: {
              'data-bs-toggle': 'offcanvas',
              'data-bs-target': '#offcanvasEcommercesubCategoryList'
            }
          }
        ],
        });
    }

    // Form Validation
    const eCommercesubCategoryListForm = document.getElementById('eCommercesubCategoryListForm');
    if (eCommercesubCategoryListForm) {
        const fv = FormValidation.formValidation(eCommercesubCategoryListForm, {
        fields: {
            name: {
            validators: {
                notEmpty: {
                message: 'Subcategory name is required'
                }
            }
            },
            category_id: {
            validators: {
                notEmpty: {
                message: 'Parent category is required'
                }
            }
            },
            image: {
            validators: {
                notEmpty: {
                message: 'Image is required'
                },
                file: {
                extension: 'jpeg,png,jpg',
                type: 'image/jpeg,image/png,image/jpg',
                message: 'Please upload valid image file'
                }
            }
            }
        },
        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap5: new FormValidation.plugins.Bootstrap5(),
            submitButton: new FormValidation.plugins.SubmitButton(),
            autoFocus: new FormValidation.plugins.AutoFocus()
        }
        });
    }
});

//For form validation
(function () {
  const eCommercesubCategoryListForm = document.getElementById('eCommercesubCategoryListForm');

  //Add New customer Form Validation
  const fv = FormValidation.formValidation(eCommercesubCategoryListForm, {
    fields: {
      subCategoryTitle: {
        validators: {
          notEmpty: {
            message: 'Please enter subCategory title'
          }
        }
      },
      slug: {
        validators: {
          notEmpty: {
            message: 'Please enter slug'
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        // Use this for enabling/changing valid/invalid class
        eleValidClass: 'is-valid',
        rowSelector: function (field, ele) {
          // field is the field name & ele is the field element
          return '.mb-6';
        }
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      // Submit the form when all fields are valid
      defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  });
})
();

