<a tabindex="0" class="pl-btn pl_active_deactive {{ $class ?? 'swal_status_change'}}">
    {{get_static_option('site_google_captcha_enable') == 'on' ? __("Deactivate") : __("Active") }}
</a>
<form method='post' action='{{$url}}' class="d-none">
    <input type='hidden' name='_token' value='{{csrf_token()}}'>
    <input type='hidden' name='site_google_captcha_enable' value="{{$value ?? null}}">
    <br>
    <button type="submit" class="cmnBtn btn_5 btn_small swal_form_submit_btn d-none"></button>
</form>
