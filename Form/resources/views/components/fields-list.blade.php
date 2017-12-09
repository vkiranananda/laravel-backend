<!-- Выводим список полей -->
<?php
    //For uploads
    $uploadAction = (isset($params['baseNamespace'])) ? "\\".$params['baseNamespace'].'\Controllers\UploadController@index' : '' ;

?>

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

    if(isset($field['post-id-field'])){
        $dataId = $data[$field['post-id-field']];
    }else {
        $dataId = (isset($data['id'])) ? $data['id']  : '' ;
    }

    if(!isset($field['attr']['class']))$field['attr']['class'] = '';
    
    if(in_array($field['type'], ['date', 'email', 'text', 'tel', 'textarea', 'mce', 'password', 'select']))
    {
        $field['attr']['class'] .= " form-control";
    }elseif(in_array($field['type'], ['radio', 'checkbox']))
    {
        $field['attr']['class'] .= " form-check-input";
    }
 
?>
     <div class="form-group {{isset($field['conteiner-class']) ? $field['conteiner-class'] : ''}}" id="{{$field['attr']['id']}}-block">
      @if(isset($field['label']))
        <label for="{{$field['attr']['id']}}">{{$field['label']}}</label>
      @endif
      <div class="Forms-field-con">
        @if(isset($field['upload']))
        	<show-uploads-button url="{!!action($uploadAction, $dataId)!!}"></show-uploads-button>
        @endif
        @if( in_array($field['type'], ['gallery', 'files']) )
            <?php
                $field['data-url'] = (isset($field['upload-action'])) ? action($field['upload-action'], $dataId) : action($uploadAction, $dataId);
            ?>
        	<attached-files :field='@json($field)'></attached-files>
        @else
            {!!Forms::fieldHTML($field, $data)!!}
        @endif
        <span class="form-control-feedback Forms-error-text"></span>
        @if( isset($field['desc']) )
            <small class="form-text text-muted">{{$field['desc']}}</small>
        @endif
      </div>
    </div>

@endforeach
