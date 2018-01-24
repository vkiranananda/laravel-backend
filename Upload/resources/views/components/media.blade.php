<div role="filesUpload" data-upload-url="{{$params['upload-url']}}" data-delete-url="{{$params['destroy-url']}}">
  @if(!isset($field))
    <div data-name="errorsArea"></div>
  @endif
  <?php 
    array_unshift($data, []);
  ?>
  <div class='file-upload' data-name="filesArea" >
    <div class='upload-button media-file'></div>
    @foreach ($data as $key => $file)
      <?php
        $img['thumb'] = '/images/system/file.png';
        if($key){
          $img = ($file['file_type'] == 'image') ? Content::genImgLink($file, [128, 128, 'fit']) : ['orig' => Content::genFileLink($file), 'thumb' => $img['thumb']] ;
        }else {
          $img['orig'] = '';
        }
      ?>
      <div class='media-file' data-type="{{$key ? 'item' : 'forClone'}}" data-file-type="{{$key ? $file['file_type'] : ''}}" data-id="{{ $key ? $file['id'] : '' }}" data-url="{!!$img['orig']!!}">

        <a href='#' subrole='delete'>&times;</a>
        @if($key)
        <a href="#" data-get-url="{{ action('\Backend\Root\Upload\Controllers\EditController@getInfo', $file['id']) }}" data-save-url="{{  }}" subrole='edit' class="icons-pencil"></a>
        @else
        <a href="#" subrole='edit' class="icons-pencil"></a>
        @endif
        <div data-type='imgCon'>
          <img src="{{ (isset($img['thumb'])) ? $img['thumb'] : '/images/system/file.png' }}" data-type="thumb-image" alt="">
          {{--  Выводим доп поле для сохранения данных только если это модальное окно
          или поле --}}
          @if($key == 0 )
            <input type="hidden" name="_media-file-uploaded-id[]" value="" data-type='file-id'>
          @elseif(isset($field))
            <input type="hidden" name="{{ $field['name'] }}" value="{{ $key ? $file['id'] : '' }}">
          @endif
        </div>
        <div class="text" data-type="text">
            {{ isset($file['orig_name']) ? $file['orig_name'] : '' }}
        </div>
        @if($key == 0)
          <progress class="progress progress-info" value="0" max="100"></progress>
        @endif
      </div>
    @endforeach
  </div>
  @if(isset($field))
    <div data-name="errorsArea"></div>
  @endif
  <input type="file" data-name="filesInput" class="hidden-xs-up" multiple>
</div>