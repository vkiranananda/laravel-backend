<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	@foreach($data as $url)
		<url>
			<loc>
				{!!$url['url']!!}
			</loc>

			@if($url['changefreq'] != '')
	  			<changefreq>{{$url['changefreq']}}</changefreq>
	  		@endif

			@if($url['priority'] != '')
	  			<priority>{{$url['priority']}}</priority>
	  		@endif
	  		@if(isset($url['date']) &&  $url['date'] != '')
				<lastmod>{{$url['date']}}</lastmod>
			@endif
  		</url> 		
	@endforeach
</urlset>
