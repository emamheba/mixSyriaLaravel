<script>
  (function($){
      "use strict";
  
      $(document).ready(function(){
  
          /* -------------------------------------------------------------------------
           * Generic Helpers
           * ---------------------------------------------------------------------*/
          function applyButtonFilters($btn, value){
              // visual active state
              $('#active_membership, #inactive_membership, #manual_membership').removeClass('active');
              $btn.addClass('active');
              // remember current filter in hidden input so search & pagination are aware
              $('#get_selected_value').val(value);
          }
  
          /* -------------------------------------------------------------------------
           * Swal – change payment/status confirmation
           * ---------------------------------------------------------------------*/
          $(document).on('click','.swal_status_change',function(e){
              e.preventDefault();
              Swal.fire({
                  title: '{{__("Are you sure to change status? Once you change it, you cannot revert this!")}}',
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: "{{ __('Yes, change it!') }}",
                  cancelButtonText: "{{ __('Cancel') }}"
              }).then((result) => {
                  if (result.isConfirmed) {
                      $(this).next().find('.swal_form_submit_btn').trigger('click');
                  }
              });
          });
  
          /* -------------------------------------------------------------------------
           * Pagination (keeps existing search string)
           * ---------------------------------------------------------------------*/
          $(document).on('click', '.pagination a', function(e){
              e.preventDefault();
              const page           = $(this).attr('href').split('page=')[1];
              const string_search  = $('#string_search').val();
              const route          = $(this).closest('.custom_pagination').data('route');
              const url            = `${route}?page=${page}`;
  
              loadMemberships(url, string_search);
          });
  
          function loadMemberships(url, string_search){
              $.ajax({
                  url: url,
                  data: {string_search},
                  beforeSend: () => $('.search_result').addClass('opacity-50'),
                  success:  res => { $('.search_result').removeClass('opacity-50').html(res); },
                  error:    ()  => {
                      $('.search_result').removeClass('opacity-50');
                      Swal.fire({ icon:'error', title:'Oops...', text:'Something went wrong! Please try again.'});
                  }
              });
          }
  
          /* -------------------------------------------------------------------------
           * Debounced search (500 ms)
           * ---------------------------------------------------------------------*/
          let searchTimer;
          $(document).on('keyup','#string_search',function(){
              clearTimeout(searchTimer);
  
              const query          = $(this).val();
              const selected_value = $('#get_selected_value').val();
              let   filter_val     = '';
  
              if(selected_value === 'active-sub')   filter_val = 1;
              if(selected_value === 'inactive-sub') filter_val = 0;
              if(selected_value === 'manual-sub')   filter_val = 'manual_payment';
  
              searchTimer = setTimeout(() => performSearch(query, filter_val), 500);
          });
  
          function performSearch(string_search, filter_val){
              $.ajax({
                  url: "{{ route('admin.user.membership.search') }}",
                  method: 'GET',
                  data: {string_search, filter_val},
                  beforeSend: () => $('.search_result').addClass('opacity-50'),
                  success: res => {
                      $('.search_result').removeClass('opacity-50');
                      if(res.status === 'nothing'){
                          $('.search_result').html(`<div class="text-center p-4"><div class="mb-3"><i class="ti ti-search-off text-muted" style="font-size:48px;"></i></div><h4 class="text-muted">{{ __('No Results Found') }}</h4><p class="text-muted">{{ __('Try adjusting your search criteria') }}</p></div>`);
                      }else{
                          $('.search_result').html(res);
                      }
                  },
                  error: () => {
                      $('.search_result').removeClass('opacity-50');
                      Swal.fire({ icon:'error', title:'Oops...', text:'Something went wrong with the search!'});
                  }
              });
          }
  
          /* -------------------------------------------------------------------------
           * Quick filters (active / inactive / manual-payment)
           * ---------------------------------------------------------------------*/
          $(document).on('click','#active_membership',function(){
              applyButtonFilters($(this), 'active-sub');
              filterRequest("{{ route('admin.user.membership.active') }}", '{{ __('No Active Memberships') }}');
          });
  
          $(document).on('click','#inactive_membership',function(){
              applyButtonFilters($(this), 'inactive-sub');
              filterRequest("{{ route('admin.user.membership.inactive') }}", '{{ __('No Inactive Memberships') }}');
          });
  
          $(document).on('click','#manual_membership',function(){
              applyButtonFilters($(this), 'manual-sub');
              filterRequest("{{ route('admin.user.membership.manual') }}", '{{ __('No Manual Payment Memberships') }}');
          });
  
          function filterRequest(url, emptyText){
              $.ajax({
                  url,
                  method: 'GET',
                  data: { string_search: $('#string_search').val() },
                  beforeSend: () => $('.search_result').addClass('opacity-50'),
                  success: res => {
                      $('.search_result').removeClass('opacity-50');
                      if(res.status === 'nothing'){
                          $('.search_result').html(`<div class=\"text-center p-4\"><div class=\"mb-3\"><i class=\"ti ti-filter-off text-muted\" style=\"font-size:48px;\"></i></div><h4 class=\"text-muted\">${emptyText}</h4></div>`);
                      }else{
                          $('.search_result').html(res);
                      }
                  },
                  error: () => $('.search_result').removeClass('opacity-50')
              });
          }
  
          /* -------------------------------------------------------------------------
           * Populate the modal for manual‑payment verification
           * ---------------------------------------------------------------------*/
          $(document).on('click','.edit_payment_gateway_modal',function(){
              const membership_id  = $(this).data('membership_id');
              const user_firstname = $(this).data('user_firstname');
              const user_email     = $(this).data('user_email');
              const img_url        = $(this).data('img_url');
  
              $('#membership_id').val(membership_id);
              $('#user_firstname').val(user_firstname);
              $('#user_email').val(user_email);
              $('.user_firstname').val(user_firstname);
              $('.user_email').val(user_email);
              $('.manual_payment_img').attr('src', img_url ? img_url : "{{ asset('assets/img/placeholder.png') }}");
          });
  
      }); // document.ready
  })(jQuery);
  </script>
  