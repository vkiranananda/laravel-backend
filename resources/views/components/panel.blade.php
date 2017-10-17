@if(Auth::check() && Auth::user()['role'] == 'admin')
	<nav class="navbar navbar-toggleable-md navbar-light bg-faded mb-2">
	  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  </button>
	  <a class="navbar-brand" href="{{ url('/') }}">Сайт</a>

	  <div class="collapse navbar-collapse" id="navbarSupportedContent">
	    <ul class="navbar-nav mr-auto">
	      <li class="nav-item">
	        <a class="nav-link" href="{{ action('\Backend\Root\Home\Controllers\HomeController@content') }}">Контент</a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="{{ action('\Backend\Root\Home\Controllers\HomeController@admin') }}">Управление</a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="{{ action('\Backend\Root\Home\Controllers\HomeController@utils') }}">Утилиты</a>
	      </li>

	      @if(isset($params['edit-link']))
	      <li class="nav-item ml-4 pt-1">
	        <a class="btn btn-primary btn-sm" href="{{$params['edit-link']}}" role="button">Править</a>
	      </li>
	      @endif
	    </ul>
<!-- 	    <form class="form-inline my-2 my-lg-0">
	      <input class="form-control mr-sm-2" type="text" placeholder="Поиск">
	      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Поиск</button>
	    </form> -->
	  </div>
	</nav>
@endif