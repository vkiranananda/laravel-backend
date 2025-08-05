<?php

namespace Backend\Root\Form\Fields;

use Log;
use UploadedFiles;
use \Backend\Root\MediaFile\Models\MediaFile;
use \Backend\Root\MediaFile\Models\MediaFileRelation;
use \Backend\Root\MediaFile\Services\Uploads;

class FilesField extends Field
{
  private $files = [];

  // Получаем значение для сохраниения
  public function save($value)
  {
    $result = [];
    if (is_array($value) && count($value) > 0) {
      // Если указан максимальное количество файлов, то обрезаем массив
      if (isset($this->field['max-files']) && is_numeric($this->field['max-files'])) {
        $value = array_slice($value, 0, $this->field['max-files']);
      }

      // Получаем все загруженные файлы
      $fileReq = MediaFile::whereIn('key', array_column($value, 'id'));

      // Если тип файла image, то фильтруем по типу
      if ($this->field['type'] == 'image')
        $fileReq = $fileReq->where('type', 'image');

      $keys = $fileReq->get(['key', 'id'])->keyBy('key')->toArray();

      foreach ($value as $file) {
        if (is_array($file) && isset($keys[$file['id']])) {
          $result[] = ['id' => $file['id'], 'name' => $file['name']];
          $this->files[] = $keys[$file['id']]['id'];
        }
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
    if (!is_array($value) || count($value) == 0)
      return [];

    // Получаем миниатюры и полные версии изображений
    $files = MediaFile::whereIn('key', array_column($value, 'id'))->get()->keyBy('key');

    $result = [];

    foreach ($value as $file) {
      if (isset($files[$file['id']]))
        $resFile = Uploads::getFileToList($files[$file['id']]);
      $resFile['name'] = $file['name'];
      $result[] = $resFile;
    }

    return $result;
  }
}
