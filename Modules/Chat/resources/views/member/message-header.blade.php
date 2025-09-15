<div class="chat-wrapper-details-header profile-border-bottom flex-between" id="livechat-message-header"
    data-user-id="{{ $data->user->id }}">
    <div class="chat-wrapper-details-header-left d-flex gap-2 align-items-center">
        <div class="chat-wrapper-details-header-left-author d-flex gap-2 align-items-center">
            @if ($data->user?->image)
                <div class="chat-wrapper-contact-list-thumb seller-img p-0 mb-3">
                    {!! render_image_markup_by_attachment_id($data->user?->image, '', 'thumb') !!}
                </div>
            @else
                <div class="seller-img p-0">
                    <x-image.user-no-image/>
                </div>
            @endif
            <div class="chat-wrapper-contact-list-thumb-contents">
                <h5 class="chat-wrapper-details-header-title">{{ $data->user?->fullname }}</h5>
            </div>
        </div>
    </div>
</div>
