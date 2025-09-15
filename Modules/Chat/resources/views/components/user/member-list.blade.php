<div class="singleUser chat_item" data-member-id="{{ $userChat?->member?->id }}">
    <div class="listCap">
        <div class="userProduct-group">
            <!-- product & user img -->
            <div class="userProduct-img seller-img p-0">
                @if($userChat?->member?->image)
                    {!! render_image_markup_by_attachment_id($userChat?->member?->image, '', 'thumb') !!}
                @else
                   <x-image.user-no-image/>
                @endif
            </div>
            <div class="notification-dots {{ Cache::has('user_is_online_' . $userChat->member?->id) ? "active" : "" }}"></div>
        </div>
        <div class="proCaption">
            <h5>
                <a href="#" class="messageTittle">{{ $userChat->member?->fullname }}</a>
            </h5>
            <div class ="unseen_message_count_{{$userChat?->member?->id}}">
                @if($userChat->user_unseen_msg_count > 0)
                   <span class="pricing">{{ $userChat->user_unseen_msg_count }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="timmer mb-20">
        <span class="time">{{ $userChat?->member?->check_online_status?->diffForHumans() }}</span>
    </div>
</div>
