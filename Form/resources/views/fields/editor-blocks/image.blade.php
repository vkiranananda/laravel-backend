<div class="{{$data['classes']}}">
    @if(isset($data['data']['link']))
        <a href="{{$data['data']['url']}}"><img src="{{$data['data']['url']}}" alt="">
        </a>
    @else
        <img src="{{$data['data']['url']}}" alt="">
    @endif
</div>
