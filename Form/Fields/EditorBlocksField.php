<?php

namespace Backend\Root\Form\Fields;

use \Backend\Root\MediaFile\Models\MediaFile;
use UploadedFiles;
use Log;

class EditorBlocksField extends Field {

    // Получаем значение для сохраниения
    public function save($value)
    {
//        //Создаем миниатюры
//        if ( isset($this->field['upload']) ) {
//            //Ищем картинки с тэгом data-id
/*            $value = preg_replace_callback("|<img.*?data-id=[\'\"]{1}(\d+)[\'\"]{1}.*?>|", function($matches)*/
//            {
//                $img = $matches[0];
//                $fileId = $matches[1];
//
//                // Если не найден файл.
//                if (! ($file = MediaFile::find($fileId)) ) return $img;
//
//                // Получаем размеры
//                $sizes[0] = (preg_match('/width=[\'\"]{1}(\d+)(px)?[\'\"]{1}/i', $img, $width))
//                    ? $width[1] : 'auto';
//                $sizes[1] = (preg_match('/height=[\'\"]{1}(\d+)(px)?[\'\"]{1}/i', $img, $height))
//                    ? $height[1] : 'auto';
//
//                // Если какой то из размеров задан. Иначе берем оригинал. (Если были сделаны правки ранее, надо вернуть оригинал)
//                $imgUrl = ( $sizes[0] != 'auto' ||  $sizes[1] != 'auto')
//                    ? UploadedFiles::genFileLink($file, $sizes)
//                    : UploadedFiles::getOrigUrl($file);
//
//                // Меняем на новую ссылку на картинку
//                return preg_replace("/src=[\'\"]{1}.*?[\'\"]{1}/", "src=\"".$imgUrl."\"", $img);
//
//            }, $value);
//        }
//        Log::debug($value);
        $arr = $value;
//        $arr = json_decode($value, true);
        if (!isset($arr['blocks'])) return [];

        foreach ($arr['blocks'] as $key => $block) {
            if ($block['type'] == 'image'){
                // Удаляем пустой блок
                if (!isset($block['data']['url'])) unset($arr['blocks'][$key]);
            }
        }

        return $arr;
    }
}



