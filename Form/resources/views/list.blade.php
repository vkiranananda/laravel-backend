@extends('Backend::layouts.admin')

@section('title', $params['lang']['title'])

@section('content')

<div class="card">
  <div class="card-header">{{$params['lang']['title']}}</div>

  <div class="card-body">
	  	@component('Form::components.list-button', ['params' => $params ]) @endcomponent
      
      	@if (isset($params['conf']['breadcrumb']) && $params['conf']['breadcrumb'] == true )
        	@component('Form::components.breadcrumb', ['params' => $params ]) @endcomponent
     	@endif

		@if (isset($params['search']) )
	  		@component('Form::components.search', ['params' => $params]) @endcomponent
	  	@endif
	
      	@component('Form::components.list', ['params' => $params, 'data' => $data]) @endcomponent

  </div>
</div>


@endsection
