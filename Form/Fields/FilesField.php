<?php

namespace Backend\Root\Form\Fields;

use Log;
use UploadedFiles;
use \Backend\Root\MediaFile\Models\MediaFile;
use \Backend\Root\MediaFile\Models\MediaFileRelation;
use \Backend\Root\MediaFile\Services\Uploads;

class FilesField extends Field
{
  // Получаем значение для сохраниения
  public function save($value)
  {
    if (is_array($value) && count($value) > 0) {
      // Получаем все загруженные файлы
      $fileReq = MediaFile::whereIn('key', array_column($value, 'id'));

      // Если тип файла image, то фильтруем по типу
      if ($this->field['type'] == 'image')
        $fileReq = $fileReq->where('type', 'image');

      $keys = array_fill_keys(array_column($fileReq->get(['key'])->toArray(), 'key'), true);

      $result = [];

      foreach ($value as $file) {
        if (isset($keys[$file['id']]))
          $result[] = ['id' => $file['id'], 'name' => $file['name']];
      }

      Log::info($result);

      return $result;
    } else {
      return [];
    }
  }

  // Получаем сырое значние элемента для редактирования
  public function edit($value)
  {
    if (!is_array($value) || count($value) == 0)
      return [];

    // Получаем миниатюры и полные версии изображений
    $files = MediaFile::whereIn('key', array_column($value, 'id'))->get()->keyBy('key');

    $result = [];

    foreach ($value as $file) {
      if (isset($files[$file['id']]))
        $result[] = Uploads::getFileToList($files[$file['id']]);
    }

    return $result;
  }
}
