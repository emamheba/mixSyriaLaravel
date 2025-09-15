<script>
    (function($){
        "use strict";

        $(document).ready(function(){

            // change status
            $(document).on('click','.swal_status_change',function(e){
                e.preventDefault();
                Swal.fire({
                    title: '{{__("Are you sure to change status complete? Once you done you can not revert this !!")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('Yes, change it!') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).next().find('.swal_form_submit_btn').trigger('click');
                    }
                });
            });

            // pagination
            $(document).on('click', '.pagination a', function(e){
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                let string_search = $('#string_search').val();
                let route = $(this).closest(".custom_pagination").attr("data-route");
                let url = route + "?page=" + page;

                subscriptions(url,string_search);
            });
            function subscriptions(url,string_search){
                $.ajax({
                     url: url,
                    data:{string_search:string_search},
                    success:function(res){
                        $('.search_result').html(res);
                    }
                });
            }

            // search category
            $(document).on('keyup','#string_search',function(){
                let string_search = $(this).val();
                let selected_value = $('#get_selected_value').val();
                let filter_val = '';
                if(selected_value == 'active-sub'){
                    filter_val = 1
                }
                if(selected_value == 'inactive-sub'){
                    filter_val = 0
                }
                if(selected_value == 'manual-sub'){
                    filter_val = 'manual_payment'
                }
                $.ajax({
                    url:"{{ route('admin.user.membership.history.search') }}",
                    method:'GET',
                    data:{string_search:string_search,filter_val:filter_val},
                    success:function(res){
                        if(res.status=='nothing'){
                            $('.search_result').html('<h3 class="text-center text-danger">'+"{{ __('Nothing Found') }}"+'</h3>');
                        }else{
                            $('.search_result').html(res);
                        }
                    }
                });
            });

            //edit manual payment membership
            $(document).on('click','.history_edit_payment_gateway_modal',function(){
                let membership_history_id = $(this).data('membership_history_id');
                let user_firstname = $(this).data('user_firstname');
                let user_email = $(this).data('user_email');
                let img_name = $(this).data('img_url');
                let manual_payment_image = "{{ url('/assets/uploads/manual-payment/membership') }}/" + img_name;
                $('#user_firstname').val(user_firstname);
                $('#user_email').val(user_email);
                $('#membership_history_id').val(membership_history_id);

                // for payment modal
                $('.user_firstname').text(user_firstname);
                $('.user_email').text(user_email);
                $('.manual_payment_img').attr('src', manual_payment_image);
            });

        });
    }(jQuery));

</script>
