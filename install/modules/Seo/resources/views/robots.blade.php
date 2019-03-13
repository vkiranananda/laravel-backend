@if( Option::get('general', 'robots-index-deny') !== '0' )
User-agent: *
Disallow: /
@endif
{!!Option::get('general', 'robots')!!}
@if( Option::get('general', 'robots-sitemap') === '1' )
Sitemap: {{url('/').'/sitemap.xml'}}
@endif

