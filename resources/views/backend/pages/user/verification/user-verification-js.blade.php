<script>
  $(document).ready(function() {
    'use strict';
  
    // Initialize DataTables with export buttons
    var dt_table = $('.data-table').DataTable({
      responsive: true,
      dom: '<"card-header border-bottom p-4"<"head-label"><"dt-action-buttons text-end"B>><"d-flex justify-content-between align-items-center mx-4 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"table-responsive"t><"d-flex justify-content-between mx-4 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      buttons: [
        {
          extend: 'collection',
          className: 'btn btn-label-primary dropdown-toggle me-2',
          text: '<i class="ti ti-file-export me-1 ti-xs"></i>Export',
          buttons: [
            {
              extend: 'print',
              text: '<i class="ti ti-printer me-2" ></i>Print',
              className: 'dropdown-item',
              exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] }
            },
            {
              extend: 'csv',
              text: '<i class="ti ti-file-spreadsheet me-2"></i>Csv',
              className: 'dropdown-item',
              exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] }
            },
            {
              extend: 'excel',
              text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
              className: 'dropdown-item',
              exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] }
            },
            {
              extend: 'pdf',
              text: '<i class="ti ti-file-text me-2"></i>Pdf',
              className: 'dropdown-item',
              exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] }
            },
            {
              extend: 'copy',
              text: '<i class="ti ti-copy me-2"></i>Copy',
              className: 'dropdown-item',
              exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] }
            } 
          ]
        },
        {
          text: '<i class="ti ti-plus me-1 ti-xs"></i> <span>Add New User</span>',
          className: 'add-new btn btn-primary',
          attr: {
            'data-bs-toggle': 'offcanvas',
            'data-bs-target': '#offcanvasAddUser'
          }
        }
      ]
    });
    
    // Add custom header title
    $('div.head-label').html('<h5 class="card-title mb-0">User Verification Requests</h5>');
    
    // Handle action confirmations with SweetAlert
    $('.data-table').on('click', '.form-inline', function(e) {
      const form = $(this);
      const isDeleteForm = form.find('.ti-trash').length > 0;
      
      e.preventDefault();
      
      Swal.fire({
        title: isDeleteForm ? 'Decline Verification?' : 'Change Verification Status?',
        text: isDeleteForm 
          ? "Are you sure you want to decline this verification request?" 
          : "Are you sure you want to change the verification status?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: isDeleteForm ? 'Yes, decline it!' : 'Yes, change it!',
        customClass: {
          confirmButton: 'btn btn-primary me-3',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then(function(result) {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
    
    // View user details in modal
    $('.view-details').on('click', function(e) {
      e.preventDefault();
      const userId = $(this).data('id');
      
      $.ajax({
        url: '/admin/user-verification/identity-details',
        type: 'GET',
        data: { user_id: userId },
        success: function(response) {
          $('#userDetailsModal .modal-body').html(response);
          $('#userDetailsModal').modal('show');
        },
        error: function() {
          Swal.fire({
            title: 'Error!',
            text: 'Failed to load user details',
            icon: 'error',
            customClass: {
              confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
          });
        }
      });
    });
    
    // Image preview on hover
    $('.document-thumbnail').hover(function() {
      const fullImgSrc = $(this).data('full-img');
      const preview = $('<div class="img-preview"><img src="' + fullImgSrc + '" alt="Document Preview"></div>');
      
      $(this).append(preview);
    }, function() {
      $(this).find('.img-preview').remove();
    });
  });
  </script>
  