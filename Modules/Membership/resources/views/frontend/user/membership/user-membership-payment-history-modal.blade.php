<div class="modal fade" id="user_membership_payment_history_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Membership Payment History') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <div class="modal-body">
                    <div class="singleMember">
                        <div class="memberDetails">
                                <input type="hidden" name="membership_history_id" id="membership_history_id" value="">
                                <div class="infoSingle">
                                    <div class="row_1 d-flex gap-3">
                                        <div class="col_1">
                                         {{ __('Plan:') }}
                                        </div>
                                        <div class="col_2">
                                            <span id="membership_type"></span>
                                        </div>
                                    </div>

                                    <div class="row_1 d-flex gap-3">
                                        <div class="col_1">
                                         {{ __('Purchase Date:') }}
                                        </div>
                                        <div class="col_2">
                                            <span id="membership_purchase_date_history"></span>
                                        </div>
                                    </div>

                                    <div class="row_1 d-flex gap-3">
                                        <div class="col_1">
                                         {{ __('Expire Date:') }}
                                        </div>
                                        <div class="col_2">
                                            <span class="text-danger" id="membership_expire_date_history"></span>
                                        </div>
                                    </div>
                                </div>

                            <div class="divider"></div>

                            <!-- part two -->
                            <div class="infoSingleTwo d-flex gap-3 mt-4">
                                <!--left part -->
                                <div class="left_part">
                                    <!--single items -->
                                    <div class="row_1 d-flex gap-3">
                                        <div class="col_1">
                                            {{ __('Listing Limit') }}
                                        </div>
                                        <div class="col_2">
                                           <strong id="listing_limit"></strong>
                                        </div>
                                    </div>
                                    <!--single items -->
                                    <div class="row_1 d-flex gap-3">
                                        <div class="col_1">
                                            {{ __('Gallery Images Per Listing') }}
                                        </div>
                                        <div class="col_2">
                                            <strong id="gallery_images">  </strong>
                                        </div>
                                    </div>
                                    <!--single items -->
                                    <div class="row_1 d-flex gap-3">
                                        <div class="col_1">
                                            {{ __('Featured Listing Limit') }}
                                        </div>
                                        <div class="col_2">
                                            <strong id="featured_listing"> </strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- right part -->
                                <div class="right_part">
                                    <!-- Single Items -->
                                    <div class="row_1 d-flex gap-3">
                                        <div class="col_1">
                                            {{ __('Business Hour') }}
                                        </div>
                                        <div class="col_2">
                                            <strong id="business_hour"> </strong>
                                        </div>
                                    </div>
                                    <!-- Single Items -->
                                    <div class="row_1 d-flex gap-3">
                                        <div class="col_1">
                                            {{ __('Enquiry Form') }}
                                        </div>
                                        <div class="col_2">
                                            <strong id="enquiry_form"> </strong>
                                        </div>
                                    </div>
                                    <!-- Single Items -->
                                    <div class="row_1 d-flex gap-3">
                                        <div class="col_1">
                                            {{ __('Membership Badge') }}
                                        </div>
                                        <div class="col_2">
                                            <strong id="membership_badge"> </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="red-global-close-btn mt-4" data-bs-dismiss="modal">{{ __('Close') }}</button>

                </div>
        </div>
    </div>
</div>
