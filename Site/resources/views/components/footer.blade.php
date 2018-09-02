@if(isset($params['conf']['load-scripts']) && is_array($params['conf']['load-scripts']))
	@foreach($params['conf']['load-scripts'] as $script)
		<script src="{{$script}}" type="text/javascript"></script>
	@endforeach
@endif
