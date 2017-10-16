@extends('Backend::layouts.admin')

@section('title', $params['lang']['title'])

@section('content')

<div class="card">
  <div class="card-header">{{$params['lang']['title']}}</div>

  <div class="card-block">
      <button type="button" class="btn btn-primary mb-3" data-role='href' href='{{ action($params['controllerName'].'@create') }}{{ isset($params['url']) ? $params['url'] : '' }}'>
      	{{ isset($params['lang']['create-title']) ? $params['lang']['create-title'] : 'Создать' }}
      </button>
      
      @if (isset($params['conf']['breadcrumb']) && $params['conf']['breadcrumb'] == true )
        @component('Form::components.breadcrumb', ['params' => $params ]) @endcomponent
      @endif

      @component('Form::components.list', ['params' => $params, 'data' => $data]) @endcomponent

  </div>
</div>


@endsection
