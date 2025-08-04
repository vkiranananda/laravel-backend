<?php

namespace Backend\Root\Form\Fields;

use Log;
use \Backend\Root\MediaFile\Models\MediaFile;
use \Backend\Root\MediaFile\Services\Uploads;

class ImageField extends Field
{
  private $files = [];

  // Получаем значение для сохраниения
  public function save($value)
  {
    $result = [];
    if (is_array($value) && isset($value['id'])) {
      $fileReq = MediaFile::where('key', $value['id'])->where('type', 'image')->first(['key','id']);
      if ($fileReq) {
        $result = ['id' => $fileReq->key];
        $this->files = [$fileReq->id];
      }
    }
    return $result;
  }

  public function addProps()
  {
    return !isset($this->field['upload-url']) ? ['upload-url' => route('media-file.upload')] : [];
  }

  public function getFiles()
  {
    return $this->files;
  }

  // Получаем сырое значние элемента для редактирования
  public function edit($value)
  {
    if (!is_array($value) || !isset($value['id']))
      return [];

    $file = MediaFile::where('key', $value['id'])->first();
    return $file ? Uploads::getFileToList($file) : [];
  }
}
