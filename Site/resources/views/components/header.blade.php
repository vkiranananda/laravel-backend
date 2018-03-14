@if(isset($params['page']['seo-title']))
	<title>{{$params['page']['seo-title']}}</title>
@endif

@if(isset($params['page']['seo-description']))
	<meta name="description" content="{{$params['page']['seo-description']}}">
@endif

@if(isset($params['page']['seo-keywords']))
	<meta name="keywords" content="{{$params['page']['seo-keywords']}}">
@endif

@if(isset($params['page']['seo-canonical']))
	<link rel="canonical" href="{{$params['page']['seo-canonical']}}" />
@else
	<link rel="canonical" href="{{str_ireplace('index.php', '', url()->current())}}" />
@endif

