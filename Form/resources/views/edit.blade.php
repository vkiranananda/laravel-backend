
@extends('Backend::layouts.admin')

@section('title', $params['lang']['title'])

@section('content')

    @component('Form::components.edit', ['params' => $params, 'data' => $data])
        
    @endcomponent

@endsection
