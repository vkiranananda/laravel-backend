<?php

namespace Backend\Root\Form\Fields;

use Backend\Root\MediaFile\Services\Uploads;
use Log;
use \Backend\Root\MediaFile\Models\MediaFile;

class EditorField extends Field
{
    private $files = [];

    // Получаем значение для сохраниения
    public function save($value)
    {

        // Ищем все значения атрибута file-id в тексте и создаём массив из них
        preg_match_all('/data-file-id="([^"]+)"/', $value, $matches);
        $fileIds = $matches[1];

        if (count($fileIds) > 0) {
            $fileReq = MediaFile::whereIn('key', $fileIds);
            $this->files = $fileReq->get(['id'])->pluck('id')->toArray();
        }

        return $value;
    }

    public function addProps()
    {
        return !isset($this->field['upload-url']) ? ['upload-url' => route('media-file.upload')] : [];
    }

    public function getFiles()
    {
        return $this->files;
    }
}
