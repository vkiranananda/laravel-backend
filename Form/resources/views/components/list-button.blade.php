<?php
	$menu = [];
	$url = (isset($params['url'])) ? $params['url'] : '' ;

// dd($params);
	$menu[0]['name'] = isset($params['lang']['create-title']) ? $params['lang']['create-title'] : 'Создать';
	$menu[0]['link'] = action($params['controllerName'].'@create').$url;

	if(isset($params['sortable'])) {
		$menu[] = '<a id="list-sortable-link" class="dropdown-item" href="'.action($params['controllerName'].'@listSortable').$url.'">Сортировка</a>';
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


