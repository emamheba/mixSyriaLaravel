<script>
  (function($) {
      "use strict";
      
      $(document).ready(function() {
          // Initialize tooltips
          const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
          tooltipTriggerList.map(function (tooltipTriggerEl) {
              return new bootstrap.Tooltip(tooltipTriggerEl);
          });
  
          // Pagination handling
          $(document).on('click', '.pagination a', function(e) {
              e.preventDefault();
              let page = $(this).attr('href').split('page=')[1];
              let string_search = $('#string_search').val();
              fetchEnquiries(page, string_search);
          });
  
          function fetchEnquiries(page, string_search) {
              $.ajax({
                  url: "{{ route('admin.enquiry.form.paginate.data') }}?page=" + page,
                  data: { string_search: string_search },
                  success: function(res) {
                      $('.search_result').html(res);
                      
                      // Reinitialize tooltips after content update
                      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                      tooltipTriggerList.map(function (tooltipTriggerEl) {
                          return new bootstrap.Tooltip(tooltipTriggerEl);
                      });
                  }
              });
          }
  
          // Search functionality
          $(document).on('keyup', '#string_search', function() {
              let string_search = $(this).val();
              $.ajax({
                  url: "{{ route('admin.enquiry.form.search') }}",
                  method: 'GET',
                  data: { string_search: string_search },
                  success: function(res) {
                      if (res.status == 'nothing') {
                          $('.search_result').html('<div class="text-center p-5"><i class="ti ti-file-x text-secondary mb-3" style="font-size: 3rem;"></i><h4 class="text-muted">No Results Found</h4></div>');
                      } else {
                          $('.search_result').html(res);
                          
                          // Reinitialize tooltips after content update
                          const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                          tooltipTriggerList.map(function (tooltipTriggerEl) {
                              return new bootstrap.Tooltip(tooltipTriggerEl);
                          });
                      }
                  }
              });
          });
  
          // Select all checkboxes
          $(document).on('click', '#selectAll', function() {
              $('.select-checkbox').prop('checked', $(this).prop('checked'));
              updateBulkDeleteButtonState();
          });
  
          // Individual checkbox click
          $(document).on('click', '.select-checkbox', function() {
              updateBulkDeleteButtonState();
              
              // If any checkbox is unchecked, uncheck the "select all" checkbox
              if (!$(this).prop('checked')) {
                  $('#selectAll').prop('checked', false);
              }
              
              // If all checkboxes are checked, check the "select all" checkbox
              if ($('.select-checkbox:checked').length === $('.select-checkbox').length) {
                  $('#selectAll').prop('checked', true);
              }
          });
  
          // Update bulk delete button state
          function updateBulkDeleteButtonState() {
              if ($('.select-checkbox:checked').length > 0) {
                  $('.bulk-delete-btn').removeAttr('disabled');
              } else {
                  $('.bulk-delete-btn').attr('disabled', 'disabled');
              }
          }
  
          // Initialize bulk delete button state
          updateBulkDeleteButtonState();
  
          // Bulk delete action
          $(document).on('click', '.bulk-delete-btn', function(e) {
              e.preventDefault();
              
              if ($('.select-checkbox:checked').length <= 0) {
                  return;
              }
              
              const ids = [];
              $('.select-checkbox:checked').each(function() {
                  ids.push($(this).val());
              });
              
              if (confirm('Are you sure you want to delete these enquiries?')) {
                  $('#bulk_delete_ids').val(JSON.stringify(ids));
                  $('.bulk_delete_form').submit();
              }
          });
      });
  })(jQuery);
  </script>