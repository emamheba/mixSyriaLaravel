<script>
    (function($){
        "use strict";
        $(document).ready(function(){

            $(document).on('click','.swal_delete_button',function(e){
                e.preventDefault();
                Swal.fire({
                    title: '{{__("Are you sure?")}}',
                    text: '{{__("You would not be able to revert this item!")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
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
                let string_search = $(this).val();
                subscriptions(page,string_search);
            });

            function subscriptions(page,string_search){
                $.ajax({
                    url:"{{ route('user.enquiries.paginate.data').'?page='}}" + page,
                    data:{string_search:string_search},
                    success:function(res){
                        $('.search_result').html(res);
                    }
                });
            }

            // search category
            $(document).on('keyup','#string_search',function(){
                let string_search = $(this).val();
                $.ajax({
                    url:"{{ route('user.enquiries.search') }}",
                    method:'GET',
                    data:{string_search:string_search},
                    success:function(res){
                        if(res.status=='nothing'){
                            $('.search_result').html('<h5 class="text-center text-danger mb-5 mt-5">'+"{{ __('Nothing Found') }}"+'</h5>');
                        }else{
                            $('.search_result').html(res);
                        }
                    }
                });
            });

        });
    }(jQuery));
</script>
