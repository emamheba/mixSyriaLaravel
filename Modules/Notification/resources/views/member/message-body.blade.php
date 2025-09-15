@foreach($data->messages as $message)
    <x-chat::member.message :$message :$data />
@endforeach
