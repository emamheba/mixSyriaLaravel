<script>
    (function($){
        "use strict";
        $(document).ready(function(){

            // show membership current info in modal
            $(document).on('click','.show_membership_payment_history_modal',function(){
                let membership_history_id = $(this).data('membership_history_id');
                let membership_type = $(this).data('membership_type');
                let membership_purchase_date_history = $(this).data('membership_purchase_date_history');
                let membership_expire_date_history = $(this).data('membership_expire_date_history');

                let listing_limit = $(this).data('listing_limit');
                let gallery_images = $(this).data('gallery_images');
                let featured_listing = $(this).data('featured_listing');
                let business_hour = $(this).data('business_hour');
                let enquiry_form = $(this).data('enquiry_form');
                let membership_badge = $(this).data('membership_badge');

                $('#membership_history_id').val(membership_history_id);
                $('#membership_type').text(membership_type);
                $('#membership_purchase_date_history').text(membership_purchase_date_history);
                $('#membership_expire_date_history').text(membership_expire_date_history);

                // listing limit
                if (listing_limit != 0) {
                    $('#listing_limit').html(listing_limit);
                } else {
                    $('#listing_limit').html('<i class="las la-times-circle text-danger fs-4 mx-2"></i>');
                }

                if (gallery_images != 0) {
                    $('#gallery_images').html(gallery_images);
                } else {
                    $('#gallery_images').html('<i class="las la-times-circle text-danger fs-4 mx-2"></i>');
                }

                if (featured_listing != 0) {
                    $('#featured_listing').html(featured_listing);
                } else {
                    $('#featured_listing').html('<i class="las la-times-circle text-danger fs-4 mx-2"></i>');
                }

                if (business_hour != 0) {
                    $('#business_hour').html('<i class="las la-check-circle text-success fs-4 mx-2"></i>');
                } else {
                    $('#business_hour').html('<i class="las la-times-circle text-danger fs-4 mx-2"></i>');
                }

                if (enquiry_form != 0) {
                    $('#enquiry_form').html('<i class="las la-check-circle text-success fs-4 mx-2"></i>');
                } else {
                    $('#enquiry_form').html('<i class="las la-times-circle text-danger fs-4 mx-2"></i>');
                }

                if (membership_badge != 0) {
                    $('#membership_badge').html('<i class="las la-check-circle text-success fs-4 mx-2"></i>');
                } else {
                    $('#membership_badge').html('<i class="las la-times-circle text-danger fs-4 mx-2"></i>');
                }

            });

        });
    }(jQuery));
</script>
