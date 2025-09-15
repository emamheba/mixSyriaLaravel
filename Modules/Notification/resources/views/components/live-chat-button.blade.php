@if (Auth::guard('web')->check())
    <div class="btn-wrapper">
        <form
            @if($listing->user_id !== Auth::guard('web')->user()->id)
                action="{{ route('user.message.send') }}"
            @else
                action="{{ route('member.message.send') }}"
            @endif
            method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="member_id" id="member_id" value="{{ $listing->user_id }}">
            <input type="hidden" name="from_user" id="from_user"  value="{{ Auth::guard('web')->user()->id }}">
            <input type="hidden" name="listing_id" id="listing_id"  value="{{ $listing->id }}">
            <div class="send-massage">
                <button type="submit" class="w-100">{{ __('Send a Massage') }}</button>
            </div>
        </form>
    </div>
@endif
