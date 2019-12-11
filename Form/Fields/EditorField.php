<?php

namespace Backend\Root\Form\Fields;

use \Backend\Root\MediaFile\Models\MediaFile;
use UploadedFiles;

class EditorField extends Field {

	// Получаем значение для сохраниения
	public function save($value)
	{
        //Создаем миниатюры
        if ( isset($this->field['upload']) ) {
        	//Ищем картинки с тэгом data-id
            $value = preg_replace_callback("|<img.*?data-id=[\'\"]{1}(\d+)[\'\"]{1}.*?>|", function($matches)
            {
                $img = $matches[0];
                $imgId = $matches[1];

                //Смотрим задана ли им ширина и высота
                if ( preg_match('/width=[\'\"]{1}(\d+)[\'\"]{1}/', $img, $width) && preg_match('/height=[\'\"]{1}(\d+)[\'\"]{1}/', $img, $height) ){
                    $sizes = [ $width[1], $height[1] ];
                	
                	//Ищем эти картинки в базе и создаем на них миниатюры    
                    if ( ($imgObj = MediaFile::find($imgId)) ){
                        $imgUrl = UploadedFiles::genFileLink($imgObj, $sizes);

                        //Меняем на новую ссылку на картинку
                        $img = preg_replace("/src=[\'\"]{1}.*?[\'\"]{1}/", "src=\"".$imgUrl."\"", $img);
                    }
                }

                return $img;

            }, $value);
        }

		return $value;
	}
}

            

