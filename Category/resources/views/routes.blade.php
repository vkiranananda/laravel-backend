@extends('Backend::layouts.admin')

@section('title', $params['title'])

@section('content')

<div class="card">
  <div class="card-header">{{$params['title']}}</div>
  <div class="card-body">
	<table class="table table-hover list" id="table-list">
      <thead>
          <tr class="table-success">
                <th>Маршрут</th>
                <th>Контроллер</th>
                <th>Назначение</th>
                <th>Категории</th>
          </tr>
      </thead>
      <tbody>

      	@foreach( $data as $url => $cat)
       	<tr>
            <td>{{ $url }}</td>
            <td>CategoryController</td>
            <td> 
            	{{ implode(', ', array_keys($cat) )}} 
            	@if(count($cat) > 1)
            		use {{array_key_first($cat)}}
            	@endif
        	</td>
            <td>{{ implode(', ', reset($cat)) }}</td>
        </tr>
      	@endforeach
      	@foreach( $data as $url => $cat)
       	<tr>
            <td>{{ $url }}/{post-name}</td>
            <td>{{ key($cat) }}Controller</td>
            <td> 
            	{{ implode(', ', array_keys($cat) )}} 
            	@if(count($cat) > 1)
            		use {{array_key_first($cat)}}
            	@endif
            </td>
            <td>{{ implode(', ', reset($cat)) }}</td>
        </tr>
      	@endforeach
        </tbody>
  	</table>
  </div>
</div>

@endsection
