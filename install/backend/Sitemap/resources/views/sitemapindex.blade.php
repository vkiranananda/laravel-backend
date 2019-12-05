<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	@foreach($data as $key => $mod)
		<sitemap>
			<loc>{!!url('sitemap/'. $key .'.xml')!!}</loc>
			<lastmod>{{date("Y-m-d")}}</lastmod>
  		</sitemap>
  	@endforeach
</sitemapindex>
