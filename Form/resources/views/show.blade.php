@extends('Backend::layouts.admin')

@section('title', $config['title'])

@section('content')

    <show-html-form :fields='@json($fields)' :config='@json($config)'></show-html-form>

@endsection
