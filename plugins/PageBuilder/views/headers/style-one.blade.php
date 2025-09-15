<!--Banner part Start-->
<div class="home-banner" data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}" {!! $background_image !!}>
    <div class="container-1920 position-relative plr">
        <div class="letf-part-img">
            <div class="img-wraper">
                @if(isset($banner_left_images_01['banner_left_images_']))
                    @foreach ($banner_left_images_01['banner_left_images_'] as $key => $banner_left_image)
                        @if(isset($banner_left_image))
                            @php $image_key = $key+1  @endphp
                            <div class="img{{$image_key}} imges">
                                {!! render_image_markup_by_attachment_id($banner_left_images_01['banner_left_images_'][$key]) !!}
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
        <div class="right-part-img">
            <div class="img-wraper">
                @if(isset($banner_right_images_02['banner_right_images_']))
                    @foreach ($banner_right_images_02['banner_right_images_'] as $key => $banner_right_image)
                        @if(isset($banner_right_image))
                            @php $image_right_key = $key+1  @endphp
                            <div class="img{{$image_right_key}} imges">
                                {!! render_image_markup_by_attachment_id($banner_right_images_02['banner_right_images_'][$key]) !!}
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
        <div class="banner-wraper">
            <div class="banner-text">
                <div class="top-text text-center">
                    {!! $top_image !!}
                    {{ $top_title }}
                </div>
                <h1 class="banner-main-head text-center"> {{ $title }} </h1>
                <p class="text text-center">{{$subtitle}}</p>
            </div>
            <div class="banner-form">
                <form  action="{{get_static_option('listing_filter_page_url') ?? '/listings'}}" class="d-flex align-items-center banner-search-location" method="get">
                    <div class="banner-form-wraper align-items-center">
                        @if(!empty(get_static_option('google_map_settings_on_off')))
                            <div class="new_banner__search__input">
                                <div class="new_banner__search__location_left" id="myLocationGetAddress">
                                    <i class="fa-solid fa-location-crosshairs fs-4"></i>
                                </div>
                                <input class="form--control" name="change_address_new" id="change_address_new" type="hidden" value="">
                                <input class="banner-input-field w-100" name="autocomplete" id="autocomplete" type="text" placeholder="{{ __('Search location here') }}">
                            </div>
                        @endif
                        <div class="search-with-any-texts">
                            <input class="banner-input-field w-100" type="text" name="home_search" id="home_search" placeholder="{{ __('What are you looking for?') }}">
                            <span id="all_search_result" class="search_with_text_section"></span>
                        </div>
                    </div>
                    <div class="banner-btn">
                        <button type="submit" class="new-cmn-btn rounded-red-btn setLocation_btn border-0">{{ get_static_option('search_button_title') ?? __('Search') }} </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Banner part End-->
