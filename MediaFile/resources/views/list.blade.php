@extends('Backend::layouts.admin')

@section('title', $params['title'])

@section('content')

<div class="card">
  	<div class="card-header">{{$params['title']}}</div>
  	<div class="card-body">
      	@component('Upload::components.media-search', ['params' => $params, 'data' => $data]) @endcomponent
		<upload-file :params='@json($params)' :data='@json($data)'></upload-file>
  	</div>
</div>
<upload-edit-file></upload-edit-file>
@endsection
