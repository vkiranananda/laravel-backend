@extends('Backend::layouts.admin')

@section('title', 'Менеджер файлов')

@section('content')
<file-manager list-url="{{ action('\Backend\FileManager\Controllers\FileManagerController@list') }}" parent="{{ Request::input('path', 0) }}"></file-manager>
@endsection
