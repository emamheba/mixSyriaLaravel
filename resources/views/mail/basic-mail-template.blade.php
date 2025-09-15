@component('mail::message')
# {{ $data['subject'] ?? 'Email Notification' }}

{!! $data['message'] ?? 'No message content provided' !!}

@if(isset($data['button_text']) && isset($data['button_url']))
@component('mail::button', ['url' => $data['button_url']])
{{ $data['button_text'] }}
@endcomponent
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent