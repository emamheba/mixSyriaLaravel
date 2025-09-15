@extends('membership::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('membership.name') !!}</p>
@endsection
