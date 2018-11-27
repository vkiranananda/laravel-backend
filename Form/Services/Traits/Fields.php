<?php

namespace Backend\Root\Form\Services\Traits;
use Request;
use Helpers;
use Content;
use \Backend\Root\Upload\Models\MediaFile;
use Forms;
use GetConfig;
use Log;
use Validator;
use Response;

//Подготовка и сохарнение полей

trait Fields {


	//Подготавливаем все поля для отображения. 

    protected function prepEditFields($fields, $post)
    {
    	//Перебираем корневые поля и устанавлтваем значение по умолчанию
		foreach ($fields as $name => &$field) {
			//Устанавливае value
    		if (( $val = Helpers::dataIsSetValue($post, $name ) ) !== false) $field['value'] = $val;

    		//Обрабатываем конкретное поле устанавливая нужные значния
    		$field = $this->prepEditField($field, $post);
	    }

		return $fields;
    }

    //Подготавливаем поля для вывода
    private function prepEditField($field, &$post) 
    {
    	//Ставим умолчание если нет значения 

    	if ( !isset($field['value']) ) $field['value'] = '';
    	$value = $field['value'];

    	//group fields
    	if ($field['type'] == 'group') {
    		//Подгружаем поля
    		if ( isset($field['load-from']) ) $field['fields'] = GetConfig::backend($field['load-from']);

    		foreach ($field['fields'] as $groupFieldName => &$groupField) {
    			//Если поле не имеет name пропускаем
		    	if ( !isset($groupField['name']) ) continue;
    			
    			//Берем значения из корня поста, поля как бы без группы
    			if ( isset($field['root-data']) && $field['root-data'] ) {
    				$val = Helpers::dataIsSetValue($post, $groupFieldName);
    				if ( $val !== false) $groupField['value'] = $val;
    			} else {
    				//Значение из массива берем
    				if ( isset($value[ $groupFieldName ]) ) {
    					$groupField['value'] = $value[ $groupFieldName ];
    				}
    			}
    			$groupField = $this->prepEditField($groupField, $post);
    		}
    		unset($field['value']);
    	}

		//для повторителей.
	    if ($field['type'] == 'repeated') {
	    	
	    	//Если значение не установлено, создаем первую запись
	    	if ( !is_array($value) ) $value = [[]];

			//Уникальный индекc	    	
	    	$field['unique-index'] = 0;

	    	//Устанавливаем умолчания для базовых полей ...
	    	foreach ($field['fields'] as &$baseField) {
	    		$baseField = $this->prepEditField($baseField, $post);
	    	}

	    	//Перебираем массив с value-s
	    	foreach ($value as $valuesBlock) {
	    		//Копируем базовые поля
		    	$field['data'][ $field['unique-index'] ]['fields'] = $field['fields']; 
		    	$field['data'][ $field['unique-index'] ]['index'] = $field['unique-index']; //Задаем уникальный индекс, для vue

		    	//Перебираем поля которые есть и задаем им value
		    	foreach ($field['data'][ $field['unique-index'] ]['fields'] as &$oneRepField) {
		    		
		    		//Если поле не имеет name пропускаем
		    		if ( !isset($oneRepField['name']) ) continue;
							
					//Выставляем значение если уже есть в value, если нет ставим умолчание
    				if ( isset ($valuesBlock[ $oneRepField['name'] ]) )
        				$oneRepField['value'] = $valuesBlock[ $oneRepField['name'] ];

		    		//Обрабатываем разные типы и выставляем окончательное значение.
		    		$oneRepField = $this->prepEditField($oneRepField, $post);
		    	}
		    	$field['unique-index']++;
	    	}
	    	unset($field['value']);
	    }

	    //Gallery, files fields
        if ($field['type'] == 'files' || $field['type'] == 'gallery') {
           
            if ( !is_array($value) ) { //set default value
            	$field['value'] = [];
            	$field['data'] = [];
            } elseif ( count($value) > 0 ) { //Получаем миниатюры и полные версии изображений
            	$files = MediaFile::whereIn('id', $value)->orderByRaw(DB::raw("FIELD(id, ".implode(',', $value).")"))->get();
                $field['data'] = app('UploadedFiles')->prepGaleryData( $files );
            }
        }
        return $field;
    }



///////------------------------------Saving--------------------------------


	protected function saveFields($post, $fields, $tabs = []) 
	{
		$newFields = [];
		$request = Request::all();
		$arrayData = [];
		$relationData = [];

		//Получаем все поля в табах которые не скрыты
		foreach ($tabs as $tab) {
			//Скрытый
			if ( !isset($tab ['show']) || $this->showCheck($tab['show'], $request) !== false) {
			
				foreach ($tab['fields'] as $fieldName) { //Массив из доступных полей
					$newFields[ $fieldName ] = $fields[ $fieldName ];
				}
			}
		}

		$result = [];
		
		$errors = $this->saveFieldsData($newFields, $request, $result, $arrayData, $relationData);

		$result['array_data'] = $arrayData;

		Log::info( print_r($result, true) );
		Log::info( print_r($errors, true) );

		if ($errors !== true ) {
			//echo Response::json([ 'errors'   =>  $errors], 422);
			// echo Response::json($errors)->setStatusCode(422);
			// // exit;
			abort(422, Response::json([ 'errors'   =>  $errors] ));
			exit;
		}


	}

	//Обходит массив полей и сохраняет данные. Рекурсивная функция
	private function saveFieldsData (
		$fields, //Список полей
		&$request, //Данные формы
		&$post, //Пост
		&$arrayData, //Массивные данны
		&$relationData, //Данные для сохранения в другую таблицу
		$fieldSave = null //Куда сохраняем поле
	){
		
		$res = [];
		$errors = [];

		foreach ($fields as $field) {
			//Если не установлены нужные параметры не обрабатываем
			if( !isset($field['type']) || ( isset($field['field-save']) && $field['field-save'] == 'none' ) ) continue;
		
			//Если поле скрыто, так же не обрабатываем
			if ( isset($field['show']) && $this->showCheck($field['show'], $request) === false) continue;

			$fieldName = $field['name'];

			//Выставляем value
			$field['value'] = ( isset($request[$fieldName]) ) ?  $request[$fieldName] : '';

			//Выставляем fieldSave
			if ( isset($fieldSave) ) $field['field-save'] = $fieldSave;

			$error = $this->saveFieldData($field, $post, $arrayData, $relationData);

			if ($error !== true) $errors [ $field['name'] ] = $error; 
		}

		return ( count($errors) > 0 ) ? $errors : true; 
	}

	//Обработка конечного поля
	private function saveFieldData ($field, &$post, &$arrayData, &$relationData) 
	{

        $value = $field['value'];

        if ( !isset($field['validate']) ) $field['validate'] = '';

        $field['field-save'] = ( isset($field['field-save']) ) ? $field['field-save'] : null;

		//------------------------------Group------------------------------------

		if ($field['type'] == 'group') {

			//Подгружаем данные если нужно
			if ( isset($field['load-from']) ) {
				$field['fields'] = GetConfig::backend($field['load-from']);
			}

			$error = $this->saveFieldsData($field['fields'], $value, $post, $arrayData[ $field['name'] ], $relationData, $field['field-save']);
		
			return $error;
		}	

		//------------------------------Repeated---------------------------------

		elseif ($field['type'] == 'repeated') {
			
			if ( !is_array($value) ) return;  //Если данных нет выходим

			$indexRepBlock = 0;
			$errors = [];
			//Перебираем блоки репитед полей
			foreach ($value as $repBlockData) {
				// $errors[ $indexRepBlock ] = []; //Создаем переменную под ошибки
				//Продолжаем обработку полей.
				$error = $this->saveFieldsData($field['fields'], $repBlockData, $post, $arrayData[ $field['name'] ][ $indexRepBlock ], $relationData, 'array');

				$indexRepBlock ++;
				//Обрабатываем ошибки
				if ($error !== true) $errors [ $indexRepBlock ] = $error; 
			}
			
			return ( count($errors) > 0 ) ? $errors : true;

		}
            

        //---------------------Проверки на select radio checkbox---------------------
        if ( array_search($field['type'], ['select', 'radio', 'checkbox']) ) {
           
            if ( !Helpers::optionsSearch( $field['options'], $value ) ) { 
                abort(403, 'saveFields has error in '.$field['type'].':'.$field['name']);
            }
            
        } 
        //-----------------------------Gallery Files----------------------------------
        elseif ($field['type'] == 'gallery' || $field['type'] == 'files') {
            if ( is_array($value) && count($value) > 0 ) {

            	//Получаем уникальные записи 
                $uniqueValue = array_unique($value);

                //Инитим запрос
                $imgReq = \Backend\Root\Upload\Models\MediaFile::whereIn('id', $uniqueValue );

                if ($field['type'] == 'gallery' ) $imgReq = $imgReq->where('file_type', 'image');

                //Проверка на валидность, если количество записей не совпадает, значит пользователь мудрит
                if ( $imgReq->get()->count() != count($uniqueValue) ) {
                	abort (403, 'saveFields не существуют какие то файлы '.$field['type'].':'.$field['name']);
                }
            } else {
                $value = [];
            }
        }
        //--------------------------------MCE Editor-----------------------------------
        elseif ($field['type'] == 'mce') {
            
            //Создаем миниатюры
            if ( isset($field['upload']) ) {
            	//Ищем картинки с тэгом data-id
                $value = preg_replace_callback("|<img.*?data-id=[\'\"]{1}(\d+)[\'\"]{1}.*?>|", function($matches)
                {
                    $img = $matches[0];
                    $imgId = $matches[1];

                    //Смотрим задана ли им ширина и высота
                    if ( preg_match('/width=[\'\"]{1}(\d+)[\'\"]{1}/', $img, $width) && preg_match('/height=[\'\"]{1}(\d+)[\'\"]{1}/', $img, $height) ){
                        $sizes = [ $width[1], $height[1] ];
                    	
                    	//Ищем эти картинки в базе и создаем на них миниатюры    
                        if( ($imgObj = \Backend\Root\Upload\Models\MediaFile::find($imgId)) ){
                            $imgUrl = Content::uFile()->genFileLink($imgObj, $sizes)['thumb'];

                            //Меняем на новую ссылку на картинку
                            $img = preg_replace("/src=[\'\"]{1}.*?[\'\"]{1}/", "src=\"".$imgUrl."\"", $img);
                        }
                    }

                    return $img;

                }, $value);
            }
        }

        //Правила валидации ДОДЕЛАТЬ
        if ($field['validate'] != '') {
        	
        	$v = Validator::make( [ 'value' => $value ], [ 'value' => $field['validate'] ] );
        	
        	if ( $v->fails() ) {
        		// Log::info( print_r($v->errors()->all(), true) );
        		return $v->errors()->all();
        	}
        }

        //Сохраняем данные в массив
        if ( ($field['field-save'] == 'array' || $field['field-save'] == 'relation') ) {
            $arrayData[ $field['name'] ] = $value;
            //Добавляем в связи
            if ($field['field-save'] == 'relation') $relationData[ $field['name'] ] = $value;
        } else $post[ $field['name'] ] = $value;

        return true;
		// return [
		// 	'array' => $data
		// ];
	}


	//Проверка условий на видимость.
	private function showCheck ($show, &$data)
	{	    
	    $res = false;
	    
	    foreach ($show as $key => $showBlock) {
		    if($key != 0) { //не первая запись
	            //Оператор &&, если предыдущее условие ошибка тогда сл тоже ошибка, проверку не делаем
	            if ($showBlock['operator'] == '&&' && $res == false) continue; 
	            //Опертор ||, если предыдущее истинно, тогда возвращем истину, если ложно делаем проверки дальше.
	            if($showBlock['operator'] == '||' && $res == true) return $res;    
	        }
	  
	        //Проверяем соответсвия условиям
	        if ($showBlock['type'] == '==') {
	            if ( $data[ $showBlock['field'] ] == $showBlock['value']) $res = true;
	            else $res = false;  
	        } else { //!=
	            if ( $data[ $showBlock['field'] ] != $showBlock['value']) $res = true;
	            else $res = false;  
	        }    
	    }

	    return $res;
	}
}
