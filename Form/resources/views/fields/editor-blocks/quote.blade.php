<div>
    <blockquote>
        <p @if($data['data']['alignment'] == 'center') class="text-center" @endif>{!! $data['data']['text'] !!}</p>
        @if($data['data']['caption'])
            <p class="text-end">
                <cite><b>{{$data['data']['caption']}}</b></cite>
            </p>
        @endif
    </blockquote>
</div>
<?php
