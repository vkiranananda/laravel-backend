<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	@foreach($data as $url)
		@if (!$url['url']) @continue @endif
		<url>
			<loc>
				{!!$url['url']!!}
			</loc>

			@if($url['freq'] != '')
	  			<changefreq>{{$url['freq']}}</changefreq>
	  		@endif

			@if($url['priority'] != '')
	  			<priority>{{$url['priority']}}</priority>
	  		@endif
	  		@if(isset($url['date']) &&  $url['date'] != '')
				<lastmod>{{$url['date']}}</lastmod>
			@endif
  		</url> 		
	@endforeach
	@auth
		<worktime>
			<?php printf('Скрипт выполнялся %.4F сек.', (microtime(true) - LARAVEL_START)) ?>
		</worktime>
	@endauth
</urlset>
