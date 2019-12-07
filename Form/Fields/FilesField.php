<?php

namespace Backend\Root\Form\Fields;

use \Backend\Root\MediaFile\Models\MediaFileRelation;
use \Backend\Root\MediaFile\Models\MediaFile;
use UploadedFiles;

class FilesField extends Field {

	// Получаем значение для сохраниения
	public function save($value)
	{
        if ( is_array($value) && count($value) > 0 ) {
        	//Получаем уникальные записи 
            $uniqueValue = array_unique($value);

            // Инитим запрос
            $imgReq = MediaFile::whereIn('id', $uniqueValue);

            if ($this->field['type'] == 'gallery' ) 
            	$imgReq = $imgReq->where('file_type', 'image');

            // Проверка на валидность, если количество записей не совпадает, значит пользователь мудрит
            if ( $imgReq->get()->count() != count($uniqueValue) ) {
            	abort (403, 'DateField не существуют какие то файлы ' . $this->field['type'].':'.$this->field['name']);
            }
        } else {
            $value = [];
        }
        return $value;
	}

	// Получаем сырое значние элемента для редактирования
	public function edit($value)
	{
        if ( !is_array($value) || count($value) == 0 ) return [];

        //Получаем миниатюры и полные версии изображений
    	$files = MediaFile::whereIn('id', $value)->get();

    	$filesGoodKey = [];
    	//Перебираем массив и создаем из свойства id ключ
        foreach (UploadedFiles::prepGaleryData( $files ) as $file) {
        	$filesGoodKey[ $file['id'] ] = $file;
        }

        // Теперь наполняем значние value. Весь этот сыр бор замучен для сортировки и если
        // в value есть одинаковые файлы.
        $res = [];
        foreach ($value as $fileId) {
        	if (isset($filesGoodKey[$fileId])) $res[] = $filesGoodKey[$fileId];
        }
   
		return $res;
	}
}