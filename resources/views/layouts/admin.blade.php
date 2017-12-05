<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Администрирование - @yield('title')</title>
	
	<link href="/css/octicons.css" rel="stylesheet">
	<link href="{{ mix('/css/backend.css') }}" rel="stylesheet">
	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
	<div class="container-fluid" id="backend-body">
	@include('Backend::components.panel')

		<div class="row">
			<div class="col-3">
			@if( Request::is('content*') )
				{!! Content::getWidget('category::category-tree', '', ['time' => 0, 'tags' => 'category']) !!}

				<ul class="list-group mt-1 menu-tree mt-3">
				  <li class="list-group-item list-group-item-action">
				    <a href="{{ action('\Backend\Root\MediaFile\Controllers\UploadController@index') }}">
				    	<span class="icons-media-file"></span>&nbsp;Медиа файлы</a>
				  </li>
				</ul>
			@elseif(Request::is('utils*'))

				<ul class="list-group mt-1 menu-tree mt-3">
					<li class="list-group-item list-group-item-action title">
				        <b>Утилиты</b>
				  </li>
					  <li class="list-group-item list-group-item-action">
				    <a href="{{ action('\Backend\Root\Redirect\Controllers\RedirectController@index') }}">
				    	Редиректы
				    </a>
				  </li>
				  <li class="list-group-item list-group-item-action">
				    <a href="{{ action('\Backend\Root\Info\Controllers\InfoController@routes') }}">
				    	Роутинг
				    </a>
				  </li>
				</ul>

			@elseif(Request::is('control*'))

				<ul class="list-group mt-1 menu-tree mt-3">
					<li class="list-group-item list-group-item-action title">
				        <b>Управление</b>
				  </li>
			  	  <li class="list-group-item list-group-item-action">
				    <a href="{{ action('\Backend\Root\Option\Controllers\OptionGeneralController@edit') }}">
				    	Настройки</a>
				  </li>				 
			  	  <li class="list-group-item list-group-item-action">
				    <a href="{{ action('\Backend\Root\Option\Controllers\OptionController@index') }}">
				    	Ключ-значение</a>
				  </li>
				  <li class="list-group-item list-group-item-action">
				    <a href="{{ action('\Backend\Root\User\Controllers\UserController@index') }}">
				    	Пользователи</a>
				  </li>
				  <!-- <li class="list-group-item list-group-item-action">
				    <a href="action('\Backend\Root\Controllers\Admin\FieldsController@index') ">
				    	Поля</a>
				  </li> -->
				</ul>
			@endif

			</div>
			<div class="col-9">@yield('content')</div>
		</div>
	</div>	

	{{ csrf_field() }}
	@include('Site::components.footer')
    <script src="{{ mix('/js/admin.js') }}"></script>
    <center>
	    <small class="text-center mt-4">
			<?php printf('Скрипт выполнялся %.4F сек.', (microtime(true) - $GLOBALS['START-TIME'])) ?>
		</small>
	</center>
</body>
</html>

