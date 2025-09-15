@php
    $listing = json_decode(json_encode($message->message['listing']));
@endphp
@if($message->from_user == 1)
    <div class="leftMessage chat-reply">
        <!-- single-->
        <div class="singleLeft-message">
            <div class="messageText">

                <div class="messageImg">
                    @if($data->user?->image)
                        {!! render_image_markup_by_attachment_id($data->user?->image, '', 'thumb') !!}
                    @else
                        <x-image.user-no-image/>
                    @endif
                </div>

                <div class="messageCaption">
                    @if(!empty($message->message['message']))
                        <p class="messagePera">{{ $message->message['message'] ?? '' }}</p>
                    @endif

                        @if(!empty($message->file))
                            <br />
                            <br />
                            <img src="{{ asset('storage/uploads/media-uploader/live-chat/'. $message->file) }}" alt="" style="max-height: 150px">
                                <?php
                                $ext = pathinfo($message->file, PATHINFO_EXTENSION);
                                ?>
                            @if($ext == 'pdf')
                                <a class="download-pdf-chat" href="{{ asset('storage/uploads/media-uploader/live-chat/'. $message->file) }}" download>{{ __('Download pdf') }}</a>
                            @endif
                        @endif

                        @if(!empty($listing))
                            <div class="card mb-3" style="max-width: 540px;">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        {!! render_image_markup_by_attachment_id($listing->image, '', 'thumb') !!}
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $listing->title }}</h5>
                                            <a class="red-global-btn" target="_blank"
                                               href="{{ route('frontend.listing.details', ['username' => $listing->username, 'slug' => $listing->slug]) }}">
                                                {{ __('View details') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <span  class="sendTime">{{ $message->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>
@endif

@if($message->from_user == 2)
    <div class="rightMessage chat-reply">
        <!-- single-->
        <div class="singleRight-message">
            <div class="messageText">
                <div class="messageCaption">
                    @if(!empty($message->message['message']))
                        <p class="messagePera">{{ $message->message['message'] ?? '' }}</p>
                    @endif
                    @if(!empty($message->file))
                        <br />
                        <br />
                        <img src="{{ asset('storage/uploads/media-uploader/live-chat/'. $message->file) }}" alt="" style="max-height: 150px">
                            <?php
                            $ext = pathinfo($message->file, PATHINFO_EXTENSION);
                            ?>
                        @if($ext == 'pdf')
                            <a class="download-pdf-chat" href="{{ asset('storage/uploads/media-uploader/live-chat/'. $message->file) }}" download>{{ __('Download pdf') }}</a>
                        @endif
                    @endif

                    @if(!empty($listing))
                        <div class="card mb-3" style="max-width: 540px;">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    {!! render_image_markup_by_attachment_id($listing->image, '', 'thumb') !!}
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $listing->title }}</h5>
                                        <a class="red-global-btn" target="_blank"
                                           href="{{ route('frontend.listing.details', ['username' => $listing->username, 'slug' => $listing->slug]) }}">
                                            {{ __('View details') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <span class="sendTime">{{ $message->created_at->diffForHumans() }}</span>
                </div>

                <div class="messageImg">
                    @if($data->member?->image)
                        {!! render_image_markup_by_attachment_id($data->member?->image, '', 'thumb') !!}
                    @else
                        <x-image.user-no-image/>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endif
