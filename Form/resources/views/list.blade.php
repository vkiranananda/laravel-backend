@extends('Backend::layouts.admin')

@section('title', $data['config']['title'])

@section('content')
    <list-html-posts :data='@json($data)'></list-html-posts>
@endsection
