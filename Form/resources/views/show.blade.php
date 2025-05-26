@extends('Backend::layouts.admin')

@section('title', $config['title'])

@section('content')
    <edit-html-form :fields='@json($fields)' :config='@json($config)'></edit-html-form>
@endsection
