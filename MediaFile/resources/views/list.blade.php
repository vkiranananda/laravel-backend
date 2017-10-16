@extends('Backend::layouts.admin')

@section('title', $params['title'])

@section('content')

<div class="card">
  <div class="card-header">{{$params['title']}}</div>
  <div class="card-block">
      @component('Upload::components.media-search', ['params' => $params, 'data' => $data]) @endcomponent
      @component('Upload::components.media', ['params' => $params, 'data' => $data]) @endcomponent
    
    {{ csrf_field() }}
  </div>
</div>
@component('Upload::components.edit-modal')  @endcomponent
@endsection
