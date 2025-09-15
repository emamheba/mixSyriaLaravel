<div class="singleUser chat_item" data-user-id="{{ $memberChat?->user?->id }}">
    <div class="listCap">
        <div class="userProduct-group">
            <!-- product & user img -->
            <div class="userProduct-img seller-img p-0">
                @if($memberChat?->user?->image)
                    {!! render_image_markup_by_attachment_id($memberChat?->user?->image, '', 'thumb') !!}
                @else
                   <x-image.user-no-image/>
                @endif
            </div>

            <div class="notification-dots {{ Cache::has('user_is_online_' . $memberChat?->user?->id) ? "active" : "" }}"></div>
        </div>
        <div class="proCaption">
            <h5>
                <a href="#" class="messageTittle">{{ $memberChat?->user?->fullname }}</a>
            </h5>
            <div class ="unseen_message_count_{{$memberChat?->user->id}}">
                @if($memberChat->member_unseen_msg_count > 0)
                    <span class="pricing">{{ $memberChat->member_unseen_msg_count }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="timmer mb-20">
        <span class="time">{{ $memberChat->user?->check_online_status?->diffForHumans() }}</span>
    </div>
</div>

