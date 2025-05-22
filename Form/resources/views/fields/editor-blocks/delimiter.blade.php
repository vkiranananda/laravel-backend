@switch($data['data']['type'])
    @case('***')
        <div class="blocks-delimiter-asterisk">* * *</div>
        @break
    @case('space')
        <div class="blocks-delimiter-height">&nbsp;</div>
        @break
    @case('downble-space')
        <div class="blocks-delimiter-dowble-height">&nbsp;</div>
        @break
    @default
        <div>&nbsp;</div>
@endswitch

