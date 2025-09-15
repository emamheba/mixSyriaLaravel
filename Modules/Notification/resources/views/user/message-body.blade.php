@foreach($data->messages as $message)
    <x-chat::user.message :$message :$data />
@endforeach
