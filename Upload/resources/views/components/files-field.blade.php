<div role="formAttachFiles" class="file-upload " >
  <div class="mb-3">
    @if($field['type'] == 'files')
      <button type="button" class="btn btn-secondary btn-sm mb-1" data-id="{{$field['attr']['id']}}" role="attachFiles" data-url="{{ isset($field['data-controller']) ? action($field['data-controller'], $postId) : $field['data-url']}}" data-type="{{$field['type']}}">Выбрать файл</button>
    @else
      <div role="attachFiles" class='upload-button media-file image' data-id="{{$field['attr']['id']}}" data-type='gallery' data-url="{{ isset($field['data-controller']) ? action($field['data-controller'], $postId) : $field['data-url']}}"></div>
    @endif
  </div>
  <?php
    array_unshift($field['value'], []);
  ?>
  <div class="conteiner" role="sortable">
  @foreach ($field['value'] as $key => $img)
    <div class="{{ ($field['type'] == 'files') ? 'file' : 'media-file image' }}" data-type="{{ $key ? 'item' : 'forClone' }}" data-id="{{ $key ? $img['id'] : '' }}">
      <a href='#' subrole='delete'>&times;</a>

      <input type="hidden" name="{{ $field['name'] }}[]" data-type='file-id' value="{{ $key ? $img['id']  : '' }}">
      <img src="{{ ($key && $img['file_type'] == 'image') ? $img['url'].'/150x150/crop/'.$img['path'].$img['file'] : '/images/system/file.png' }}" data-type="thumb-image" alt="">
      <div class="text" data-type="text">
        {{ isset($img['orig_name']) ? $img['orig_name'] : '' }}
      </div>
    </div>
  @endforeach
  </div>
  <div class="clearfix"></div>
</div>
