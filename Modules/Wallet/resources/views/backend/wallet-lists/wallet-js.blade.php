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
                countries(page);
            });
            function countries(page){
                $.ajax({
                    url:"{{ route('admin.wallet.paginate.data').'?page='}}" + page,
                    success:function(res){
                        $('.search_result').html(res);
                    }
                });
            }

            // search country
            $(document).on('keyup','#string_search',function(){
                let string_search = $(this).val();
                $.ajax({
                    url:"{{ route('admin.wallet.search') }}",
                    method:'GET',
                    data:{string_search:string_search},
                    success:function(res){
                        if(res.status=='nothing'){
                            $('.search_result').html('<h3 class="text-center text-danger">'+"{{ __('Nothing Found') }}"+'</h3>');
                        }else{
                            $('.search_result').html(res);
                        }
                    }
                });
            })

        });
    }(jQuery));
</script>
