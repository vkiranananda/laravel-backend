
<table class="table {{$data['classes']}}" >
    @foreach($data['data']['content'] as $row)
        @if($loop->first && $data['data']['withHeadings'])
            <thead>
                <tr>
                    @foreach($row as $col)
                        <th>{!! $col !!}</th>
                    @endforeach
                </tr>
            </thead>
        @else
            <tr>
                @foreach($row as $col)
                    <td>{!! $col !!}</td>
                @endforeach
            </tr>
        @endif
    @endforeach


</table>

{{--@dd($data)--}}

