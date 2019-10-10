<?php

namespace Backend\Root\Form\Services\Traits;
use Request;
use Helpers;
use Forms;
use GetConfig;
use Log;
use Validator;
use Response;
use Illuminate\Support\Facades\DB;

//Подготовка и сохарнение полей

trait Fields {

	// Базовые поля идущие в комплекте
	private $_fieldsClasses = [
		'default'	=>	'\Backend\Root\Form\Fields\Field',
		'date'		=>	'\Backend\Root\Form\Fields\DateField',
		'files'		=>	'\Backend\Root\Form\Fields\FilesField',
		'gallery'	=>	'\Backend\Root\Form\Fields\FilesField',
		'mce'		=>	'\Backend\Root\Form\Fields\MceField',
		'select'	=>	'\Backend\Root\Form\Fields\SelectField',
		'checkbox'	=>	'\Backend\Root\Form\Fields\SelectField',
		'radio'		=>	'\Backend\Root\Form\Fields\SelectField',
		'multiselect'=>	'\Backend\Root\Form\Fields\SelectField',
	];

	// Инитип поле
	public function initField ($field) 
	{
		$type = (isset($field['type'])) ? $field['type'] : 'default';
		// Подключаем классы обработки полей
		$type_field = (isset($this->_fieldsClasses[$type])) ? $type : 'default';
		// Инитим класс обработчик
		return new $this->_fieldsClasses[$type_field]($field);
	}

	//Подготавливаем все поля для отображения. 
    protected function prepEditFields()
    {
    	$arrayData = ( isset($this->post['array_data']['fields']) ) ? $this->post['array_data']['fields'] : [];

    	$fields = $this->fields['fields'];
    	$post = $this->post;
    	//Перебираем корневые поля и устанавлтваем значение по умолчанию
		foreach ($fields as &$field) {
			
			//Если нет данных полей пропускаем
			if( !isset($field['name']) || !isset($field['type']) ) continue;

			// $field['value'] = $this->_getFieldValue($field, $post, $arrayData);

    		//Обрабатываем конкретное поле устанавливая нужные значния
    		$field = $this->_prepEditField($field, $post, $arrayData);
	    }

		return $fields;
    }

	// Подготавливаем скрытые поля для отображения. 
    protected function prepHiddenFields()
    {
    	//Возвращаем пустой массив если значение не установлено
    	if (! isset ($this->fields['hidden']) || ! is_array($this->fields['hidden'])) return [];

    	$res = [];

		foreach ($this->fields['hidden'] as $field) {
			$res[ $field['name'] ] = Helpers::dataIsSetValue($this->post, $field['name'] );
	    }

		return $res;
    }

    //Получаем значение для поля, none - если значение
    private function _getFieldValue ($field, &$post, $arrayData, $none = false)
    {
    	if ( $none ) return '';

    	//Проверяем откуда брать значние. Если условие выполняеся берем из array_data
    	if ( isset($field['field-save']) ) {
    		if ( $field['field-save'] == 'array' || $field['field-save'] == 'relation' ) {
    			if ( isset($arrayData[ $field['name'] ]) ) return $arrayData[ $field['name'] ];
    		}
    	} 
    	//Проверяем есть ли значение в корне записи если field-save не установлен
    	if ( isset($post[ $field['name'] ]) ) return $post[ $field['name'] ];
    	 
    	// Выводим значение по умолчанию, если нет пустое значение.
    	return ( isset($field['value']) ) ? $field['value'] : '';
    }

    // Подготавливаем поля для вывода
    private function _prepEditField ($field, &$post, $arrayData, $none = false) 
    {
		// //Устанавливае value	
		// $value = $field['value'];

    	//group fields
    	if ($field['type'] == 'group') {
    		
    		//Подгружаем поля
    		if ( isset($field['load-from']) ) 
    			$field['fields'] = GetConfig::backend($field['load-from']);

    		unset($field['load-from']);


    		// Присваиваем значение value. Бере
    		if ( isset($field['field-save']) ) {
				$value = ( isset($arrayData[ $field['name'] ] ) ) ? $arrayData[ $field['name'] ] : [];
    		} else $value = $arrayData;
    		
    		foreach ($field['fields'] as $groupFieldName => &$groupField) {
    			
    			// Если поле не имеет name и type пропускаем
				if( !isset($groupField['name']) || !isset($groupField['type']) ) continue;

				// Выставляем, что дальнейшие поля будут браться из массива если в корневом указана эта опция или
				if ( 
					(  isset($field['field-save']) && 
					   ( $field['field-save'] == 'array' || $field['field-save'] == 'relation' )) || 
					$groupField['type'] == 'group'
					) {
					$groupField['field-save'] = 'array'; 
				}
    			
    			//Выставляем значние, берем из value текущего поля
		    	$groupField['value'] = $this->_getFieldValue($groupField, $post, $value, $none);
    		
    			//Делаем дополнительные обработки по полям.
    			$groupField = $this->_prepEditField($groupField, $post, $value, $none);
    		}

    		unset($field['value']);
    	}

		//для повторителей.
	    elseif ($field['type'] == 'repeated') {
	    	
	    	//Если значение не установлено, создаем первую запись
	    	if ( !is_array($value) ) $value = [[]];

			//Уникальный индекc
	    	$field['unique-index'] = 0;
			
			//Обнуляем value для новых значений.
			$field['value'] = [];
	    	
	    	//Перебираем массив с value-s
	    	foreach ($value as $valuesBlock) {

	    		//Копируем базовые поля
		    	$field['value'][ $field['unique-index'] ]['fields'] = $field['fields']; 
		    	$field['value'][ $field['unique-index'] ]['key'] = $field['unique-index'];

		    	//Перебираем поля которые есть и задаем им value
		    	foreach ( $field['value'][ $field['unique-index'] ]['fields'] as &$oneRepField ) {
		    		
    				//Если поле не имеет name и type пропускаем
					if( !isset($oneRepField['name']) || !isset($oneRepField['type']) ) continue;
	    			
	    			//Полюбому значения в массиве
	    			$oneRepField['field-save'] = 'array';    						
					
					//Выставляем значение 
					$oneRepField['value'] = $this->_getFieldValue($oneRepField, $post, $valuesBlock, $none);

		    		//Обрабатываем разные типы и выставляем окончательное значение.
		    		$oneRepField = $this->_prepEditField($oneRepField, $post, $valuesBlock, $none);
		    	}
		    	$field['unique-index']++;
	    	}

	    	// Устанавливаем умолчания для базовых полей ... Выставляем значение перменной $none
	    	// Что бы лишний раз не искался value.
	    	foreach ($field['fields'] as &$baseField) {
    			
    			// Если поле не имеет name и type пропускаем
				if( !isset($baseField['name']) || !isset($baseField['type']) ) continue;

				$baseField['value'] = '';
	    		$baseField = $this->_prepEditField($baseField, $post, [], true);
	    	}
	    }

		$field['value'] = $this->initField($field)->edit($this->_getFieldValue($field, $post, $arrayData, $none));

        //Убираем лишние поля
        unset( $field['field-save'] );

        return $field;
    }



///////---------------------------------------Saving-----------------------------------------------


	protected function saveFields() 
	{

		$request = Request::input('fields', []);

		$newFields = [];
		$relationData = [];
		$post = $this->post;
		$arrayData = ( isset($post['array_data']) ) ? $post['array_data'] : [];

		$arrayDataFields = [];
		
		// Получаем все поля в табах которые не скрыты 
		foreach ($this->fields['edit'] as $tab) {
			// не скрытый
			if ( !isset($tab ['show']) || $this->showCheck($tab['show'], $request) !== false) {
				
				foreach ($tab['fields'] as $fieldName) { //Массив из доступных полей
					//Если поля нет в общем списке, например его удалили, то пропускаем обработку
					if (! isset ($this->fields['fields'][$fieldName]) ) continue; 

					$newFields[$fieldName] = $this->fields['fields'][$fieldName];
				}
			} 
		}

		$errors = $this->_saveFieldsList($newFields, $request, $post, $arrayDataFields, $relationData);

		// Сохраняем скрытые поля
    	if ( isset ($this->fields['hidden']) && is_array($this->fields['hidden'])){
    		// Проставляем всем полям тип
    		$hiddenFields = [];
    		foreach ($this->fields['hidden'] as $field) {
    			$field['type'] = 'hidden';
    			$hiddenFields[] = $field;
    		}
    		$this->_saveFieldsList(
    			$hiddenFields,	
    			Request::input('hidden', []), 
    			$post,
    			$arrayData,
    			$relationData
    		);
    	}

    	// Устанавливаем array_data
		if ( count($arrayDataFields) > 0 ) {
			$arrayData['fields'] = $arrayDataFields;
			$post['array_data'] = $arrayData;
		}

		return [
			'errors' => $errors,
			'relations' =>  $relationData,
			'post' => $post
		];
	}

	// Обходит массив полей и сохраняет данные. Рекурсивная функция
	private function _saveFieldsList (
		$fields, //Список полей
		$request, //Данные формы
		&$post, //  Пост
		&$arrayData, //Массивные данные
		&$relationData, //Данные для сохранения в другую таблицу
		$fieldSave = null //Куда сохраняем поле
	){
		
		$res = [];
		$errors = [];

		foreach ($fields as $field) {
			//Если не установлены нужные параметры не обрабатываем
			if( !isset($field['name']) || !isset($field['type']) || ( isset($field['field-save']) && $field['field-save'] == 'none' ) ) continue;
		
			//Если поле скрыто, так же не обрабатываем
			if ( isset($field['show']) && $this->showCheck($field['show'], $request) === false) continue;

			$fieldName = $field['name'];

			//Выставляем value
			$field['value'] = ( isset($request[$fieldName]) ) ?  $request[$fieldName] : '';

			//Выставляем fieldSave
			if ( isset($fieldSave) ) $field['field-save'] = $fieldSave;

			$error = $this->_saveFieldData($field, $post, $arrayData, $relationData);

			if ($error !== true) $errors [ $field['name'] ] = $error; 
		}

		return ( count($errors) > 0 ) ? $errors : true; 
	}

	// Обработка конечного поля
	private function _saveFieldData ($field, &$post, &$arrayData, &$relationData) 
	{

        $value = $field['value'];

        $field['field-save'] = ( isset($field['field-save']) ) ? $field['field-save'] : null;

		//------------------------------Group------------------------------------

		if ($field['type'] == 'group') {

			// Подгружаем данные если нужно
			if ( isset($field['load-from']) ) {
				$field['fields'] = GetConfig::backend($field['load-from']);
			}

			$error = $this->_saveFieldsList($field['fields'], $value, $post, $arrayData[ $field['name'] ], $relationData, $field['field-save']);
		
			return $error;
		}	

		//------------------------------Repeated---------------------------------

		elseif ($field['type'] == 'repeated') {
			
			if ( !is_array($value) ) return;  //Если данных нет выходим

			$indexRepBlock = 0;
			$errors = [];
			//Перебираем блоки репитед полей
			foreach ($value as $repData) {

				//Тут важно что переменная с сервера идет не значением, а объектом, где указан ключ
				//группы репитед, оно надо для отображения ошибок и при сортировке что бы формы не 
				//рендерились поновой.
				//Продолжаем обработку полей.
				$error = $this->_saveFieldsList(
					$field['fields'], 
					$post,
					$repData['value'], 
					$arrayData[ $field['name'] ][ $indexRepBlock ], 
					$relationData, 'array'
				);

				$indexRepBlock ++;
				//Обрабатываем ошибки
				if ($error !== true) $errors [ $repData['key'] ] = $error; 
			}
			
			return ( count($errors) > 0 ) ? $errors : true;

		}
            
		$value = $this->initField($field)->save($value);

		// Хук обработки полей перед сохранением
        $value = $this->preSaveFieldValue($field, $value);

        //Правила валидации
        if ( isset($field['validate']) ) {
        	
        	$v = Validator::make( [ 'value' => $value ], [ 'value' => $field['validate'] ] );
        	
        	if ( $v->fails() ) return implode(' ', $v->errors()->all() );
        }

        //Сохраняем данные в массив
        if ( ($field['field-save'] == 'array' || $field['field-save'] == 'relation') ) {
            $arrayData[ $field['name'] ] = $value;
            //Добавляем в связи
            if ($field['field-save'] == 'relation') { 
            	$relationData[ $field['name'] ] = $value;
            }
        } else {
        	$post[ $field['name'] ] = $value;
        }

        return true;
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

	// Хук обработки полей перед сохранением
	protected function preSaveFieldValue ($field, $value) { return $value; }
}
