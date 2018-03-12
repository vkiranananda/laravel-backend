
@extends('Backend::layouts.admin')

@section('title', $params['lang']['title'])

@section('content')

    @component('Form::components.show', ['params' => $params, 'data' => $data])
        
    @endcomponent

@endsection
