<div role="formAttachFiles" class="file-upload">
  
  <?php
    if(!isset($field['value']) || !is_array($field['value']))$field['value'] = [];
    else $mediaFiles = \Backend\Root\Upload\Models\MediaFile::whereIn('id' ,$field['value'])->get();
    array_unshift($field['value'], []);
  ?>
  <div class="conteiner" role="sortable">
  @foreach ($field['value'] as $key => $id)
    <?php
      if($key){
        if(false == ($img = Helpers::searchArray($mediaFiles, 'id', $id) ) )continue;
      }
    ?>
    <div class='media-file image' data-type="{{ $key ? 'item' : 'forClone' }}" data-id="{{ $key ? $id : '' }}">
      <a href='#' subrole='delete'>&times;</a>
      <div data-type='imgCon'>
        <input type="hidden" name="{{ $field['name'] }}[]" data-type='file-id' value="{{ $key ? $id  : '' }}">
        <img src="{{ $key ? $img['url'].'/150x150/crop/'.$img['path'].$img['file'] : '' }}" data-type="thumb-image" alt="">
      </div>
    </div>
  @endforeach
  </div>
  <div class="clearfix"></div>
</div>
