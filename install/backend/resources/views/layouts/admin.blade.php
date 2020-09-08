<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Администрирование - @yield('title')</title>

    <link href="{{ mix('/backend/css/backend.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container-fluid" id="backend-body">
    <div class="row py-0 flex-nowrap">
        <div class="col-auto left-column px-0 py-3  bg-dark">
            {!! Widget::print('menu::main-menu', '', ['tags' => 'category']) !!}
        </div>
        <div class="col content-block mt-3">

            @yield('content')

            <div class="text-center py-2">
                <small class="text-center mt-4">
                    <?php printf('Скрипт выполнялся %.4F сек.', (microtime(true) - LARAVEL_START)) ?>
                </small>
            </div>
        </div>
    </div>
</div>
<!-- <script src="/backend/tinymce/tinymce.min.js"></script> -->
<script src="{{ mix('/backend/js/admin.js') }}"></script>
</body>
</html>

