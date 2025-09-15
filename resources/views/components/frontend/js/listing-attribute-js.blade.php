<script>
    (function ($) {
        "use strict";
        $(document).ready(function () {
            // Add More Attributes
            $(document).on('click', '#add-more-attributes', function() {
                let newAttribute = `
                            <div class="attribute-item mb-3 position-relative">
                                <label class="form-label mt-3">{{ __('Title') }}</label>
                                <input type="text" class="form-control mb-2 mt-0" name="attributes_title[]" placeholder="{{ __('Enter title') }}">

                                <label class="form-label">{{ __('Description') }}</label>
                                <input type="text" class="form-control" name="attributes_description[]" placeholder="{{ __('Enter description') }}">

                                <button type="button" class="btn btn-danger btn-sm remove-attribute position-absolute top-0 end-0 mt-0 me-2">
                                    <i class="las la-times-circle"></i>
                                </button>
                            </div>
                        `;
                $('#attribute-container').append(newAttribute);
            });
            // Remove Attribute Item
            $(document).on('click', '.remove-attribute', function () {
                $(this).closest('.attribute-item').remove();
            });
        });
    })(jQuery)
</script>
