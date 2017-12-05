@if( Option::get('general', 'robots-index-deny') !== '0' )
User-agent: *
Disallow: /
@endif
@if( Option::get('general', 'robots-sitemap') === '1' )
Sitemap: {{url('/').'/sitemap.xml'}}
@endif
{!!Option::get('general', 'robots')!!}
