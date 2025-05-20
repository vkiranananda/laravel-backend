<?php
    $baseTag = $data['data']['style'] == 'ordered' ? "ol" : "ul";
    ?>
<{{$baseTag}}>
@foreach($data['data']['items'] as $item)
    <li>{!! $item !!}</li>
@endforeach
</{{$baseTag}}>
