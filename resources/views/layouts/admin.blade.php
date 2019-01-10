<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Администрирование - @yield('title')</title>
	
	<link href="/backend/css/octicons.css" rel="stylesheet">
	<link href="{{ mix('/backend/css/backend.css') }}" rel="stylesheet">
	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-light1">
	<div class="container-fluid" id="backend-body">
		<div class="row py-0">
			{!! Content::getWidget('form::left-menu', '', ['tags' => 'category']) !!}
			<div class="col content-block bg-light1 mt-3">
				
				@yield('content')
				
				<center class="py-2">
				    <small class="text-center mt-4">
						<?php printf('Скрипт выполнялся %.4F сек.', (microtime(true) - $GLOBALS['START-TIME'])) ?>
					</small>
				</center>
			</div>
		</div>
	</div>	
    <script src="{{ mix('/backend/js/admin.js') }}"></script>

</body>
</html>

