<!--Google Adds-->
@if(get_static_option('google_adsense_status') == 'on')
<div class="google-adds" data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}">
    <div class="text-{{$custom_container}} single-banner-ads ads-banner-box" id="home_advertisement_store">
        <input type="hidden" id="add_id" value="{{$add_id}}">
        {!! $add_markup !!}
    </div>
</div>
@endif
<!--Google Adds-->
