<ul>
@foreach($menu as $el)
	<li>
		@if($el['url'])
			<a href="{{ $el['url'] }}">{{ $el['label'] }}</a>
		@else
			{{ $el['label'] }}
		@endif
		@if(count($el['elements']) > 0)
			@component($template, ['menu' => $el['elements'], 'template' => $template]) @endcomponent
		@endif
	</li>
@endforeach
</ul>