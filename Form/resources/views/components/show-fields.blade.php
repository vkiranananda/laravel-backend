@foreach ( $fields as $key => $field)
<?php

    if( isset($field['name']) && isset($params['fields'][$field['name']]) ){
        $field = array_replace_recursive($params['fields'][$field['name']], $field);
    }
   
    if(!isset($field['type']))continue;

    if($field['type'] == 'html') {
        echo $field['html'];
        continue;
    }
    elseif($field['type'] == 'html-title') { 
        $curClass = ($key == 0) ? '' : 'mt-4' ;
        echo "<h5 class='$curClass'>".$field['title']."</h5><hr>"; 
        continue;
    }

    if(!isset($field['name']))continue;
?>
    <div class="row p-2">
        <div class="col-4">
            <b>
            @if(isset($field['label']))
                {{$field['label']}}
            @endif           
        </b>
        </div>
   
        <div class="col-8">
            {{ Content::getFieldValue($data, $field) }}
        </div>
    </div>

@endforeach
