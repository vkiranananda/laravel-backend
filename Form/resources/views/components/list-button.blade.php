<?php
	$menu = [];
	$urlPostfix = "";

	foreach ($params['conf']['url-params'] as $param) {
		$urlPostfix .= ($urlPostfix == '') ? '?' : '&' ;
		$urlPostfix .= $param.'='.Request::input($param, '');
	}

	$menu[0]['name'] = isset($params['lang']['create-title']) ? $params['lang']['create-title'] : 'Создать';
	$menu[0]['link'] = action($params['controllerName'].'@create').$urlPostfix;

	if(isset($params['sortable'])) {
		$sUrl = (isset($params['conf']['url-sortable'])) ? $params['conf']['url-sortable'] : action($params['controllerName'].'@listSortable').$urlPostfix;
		$menu[] = '<a id="list-sortable-link" class="dropdown-item" href="'.$sUrl.'">Сортировка</a>';
	}

?>
@if(count($menu) > 1)
	<div class="btn-group mb-3">
	  <button type="button" class="btn btn-primary" data-role='href' href='{{$menu[0]['link']}}'>{{ $menu[0]['name'] }}</button>
	  <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    <span class="sr-only"></span>
	  </button>

	  <div class="dropdown-menu">
		<?php unset($menu[0])?>
		@foreach($menu as $item)
			{!!$item!!}
		@endforeach
	  </div>
	</div>
@else
    <button type="button" class="btn btn-primary mb-3" data-role='href' href='{{$menu[0]['link']}}'>
      	{{ $menu[0]['name'] }}
    </button>
@endif


