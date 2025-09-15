@php
    $user_membership = \Modules\Membership\app\Models\UserMembership::where('user_id', Auth::guard('web')->user()->id)->first();
@endphp
<script>
    (function ($) {
        "use strict";
        $(document).ready(function () {

            // Get the user's membership data
            let userMembership = {!! json_encode($user_membership) !!};
            // Set the maximum allowed images
            let maxPhotosAllowed = userMembership ? userMembership.gallery_images : 50;
            let errorMessageDisplayed = false;
            $(document).on("click", ".media-uploader-image-list li", function() {
                const selectedPhotos = $(".media-uploader-image-list li.selected");
                if (selectedPhotos.length >= maxPhotosAllowed) {
                    $(".image-list-wr5apper ul li:not(.selected)").each(function() {
                        $(this).css("opacity", "0.5");
                        $(this).prop("disabled", true);
                    });
                    // $("#error_message_images").show();
                    let error_message_for_images = "{{ __('You can only select up to') }}" + " " + maxPhotosAllowed + " " + "{{ __('photos') }}";
                    errorMessageDisplayed = true;
                    toastr.error(error_message_for_images);
                }else if(selectedPhotos.length <= maxPhotosAllowed){
                    $(".image-list-wr5apper ul li:not(.selected)").each(function() {
                        $(this).css("opacity", "1");
                        $(this).removeAttr("disabled");
                    });
                }  else if (selectedPhotos.length <= maxPhotosAllowed && errorMessageDisplayed) {
                    errorMessageDisplayed = false;
                    toastr.clear();
                }
            });


        });
    })(jQuery)
</script>
