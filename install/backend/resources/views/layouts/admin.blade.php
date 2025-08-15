<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Администрирование - @yield('title')</title>
  <link rel="stylesheet" href="/backend/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/backend/js/trumbowyg/ui/trumbowyg.min.css" />
  @vite('vendor/vkiranananda/backend/resources/sass/backend.scss')
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
            <?php printf('Скрипт выполнялся %.4F сек.', microtime(true) - LARAVEL_START); ?>
          </small>
        </div>
      </div>
    </div>
  </div>
  <v-alert></v-alert>
  <v-image-view></v-image-view>
</body>

<script src="/backend/js/jquery.min.js"></script>
<script src="/backend/js/trumbowyg/trumbowyg.min.js"></script>
<script src="/backend/js/trumbowyg/langs/ru.min.js"></script>
@vite('vendor/vkiranananda/backend/resources/js/backend.js')

</html>
