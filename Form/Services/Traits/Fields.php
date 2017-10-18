<?php

namespace Backend\Root\Form\Services\Traits;
use Request;
use Helpers;
use Content;
use \Backend\Root\Upload\Models\MediaFile;
use Forms;

//Подготовка и сохарнение полей

trait Fields {

   protected function saveFields(&$post, &$fields)
    {
        $relationFields = [];
        $validate = [];
        $arrayData = (is_array($post['array_data'])) ? $post['array_data'] : [] ;

        foreach ($fields as $field) 
        {
            //Если не установлено имя или тип, а так же если выставлен тип сохранения none не сохраняем это поле.
            if( ! is_array($field) || ! isset($field['type']) || (isset($field['field-save']) && $field['field-save'] == 'none') )continue;

            if(!isset($field['validate']))$field['validate'] = '';

            $value = Request::input($field['name'], '');

            //Проверки на select radio checkbox
            if(array_search($field['type'], ['select', 'radio', 'checkbox']) ){
                //Пропускаем проверку если пустой чекбокс
                if($field['type'] == 'checkbox' && $value == '') goto skipCheck;
               
                if(!isset($field['options']) || !Helpers::optionsSearch($field['options'], $value )){ 
                    abort(403, 'saveFields has error in '.$field['type'].':'.$field['name']);
                }
                skipCheck:
                
            }elseif($field['type'] == 'gallery' || $field['type'] == 'files'){ //Галлерею сохраняем
                if(is_array($value) && count($value) > 1){
                    unset($value[0]);
                    $uniqueValue = array_unique($value);

                    $imgReq = \Backend\Root\Upload\Models\MediaFile::whereIn('id', $uniqueValue )->where('file_type', 'image');

                    if($field['type'] == 'gallery' )$imgReq = $imgReq->where('file_type', 'image');

                    if($imgReq->get()->count() != count($uniqueValue) )abort(403, $field['type'].' field не существуют какие то объекты');
                }else {
                    $value = [];
                }

            }elseif ($field['type'] == 'mce') {
                if( isset($field['upload'])){

                    $value = preg_replace_callback("|<img.*?data-id=[\'\"]{1}(\d+)[\'\"]{1}.*?>|", function($matches)
                    {
                        $img = $matches[0];
                        $imgId = $matches[1];

                        if(preg_match('/width=[\'\"]{1}(\d+)[\'\"]{1}/', $img, $width) && preg_match('/height=[\'\"]{1}(\d+)[\'\"]{1}/', $img, $height)){
                            $sizes = [ $width[1], $height[1] ];
                            
                            if( ($imgObj = \Backend\Root\Upload\Models\MediaFile::find($imgId)) ){
                                $imgUrl = Content::genImgLink($imgObj, $sizes)['thumb'];

                                $img = preg_replace("/src=[\'\"]{1}.*?[\'\"]{1}/", "src=\"".$imgUrl."\"", $img);
                            }
                        }

                        return $img;

                    }, $value);
                }
            }

            //Правила валидации
            if($field['validate'] != ''){
                $validate[$field['name']] = $field['validate'];
            }

            //Сохраняем данные
            if(isset($field['field-save']) && ($field['field-save'] == 'array' || $field['field-save'] == 'relation') ){
                $arrayData['fields'][$field['name']] = $value;
                if($field['field-save'] == 'relation'){
                    $relationFields[$field['name']] = $value;
                }
            }else{
                $post[$field['name']] = $value;
            }
        }
  
        // print_r($validate);
        if(count($arrayData) > 0) $post['array_data'] = $arrayData;

        // print_r($validate);
        if(count($validate) > 0) $this->validate(Request(), $validate );

        return $relationFields;

    }

    //Подготоваливаем поля для формы редактирования.
    protected function prepEditFields(&$post, &$fields)
    {
        foreach ($fields as $name => &$field) {
            $field = Forms::prepField($post, $field);
        }
    }
}
