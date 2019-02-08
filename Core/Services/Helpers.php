<?php
namespace Backend\Root\Core\Services;

class Helpers {
    //Получаем все поля где есть массив options, передается параметр табов
    static public function changeFieldsOptions($fields)
    {
        $res = [];

        foreach ($fields as $key => &$field) {
            if(isset($field['options']) && is_array($field['options'])){
                $field['options'] = Helpers::optionsToArr($field['options']);
            }
        }
        return $fields;
    }


    // Получаем массив полей из массива getFields с объедиенением результатов
    static public function getFields($allFields, $getFields, $changeOptions = false)
    {
    	$res = [];
    	foreach ($getFields as $field) {
    		$res[$field['name']] = array_merge($allFields[$field['name']], $field);

            if($changeOptions && isset($res[$field['name']]['options']) && is_array($res[$field['name']]['options'])){
                $res[$field['name']]['options'] = Helpers::optionsToArr($res[$field['name']]['options']);
            }
    	}
    	return $res;
    }

    // Получаем массив списка записей с нужными полями
    static public function getArrayItems(&$data, $fields, $colums = ['id'])
    {
    	$res = [];
    	foreach ($data as $el) {
    		$item = [];
    		foreach ($fields as $field) {
    			$item[$field['name']] = Helpers::dataIsSetValue($el, $field['name']);
    			if(isset($field['options']) 
    				&& is_array($field['options']) 
    				&& isset($field['options'][$item[$field['name']]])){
    				$item[$field['name']] = $field['options'][$item[$field['name']]];
    			}
    		}
    		foreach ($colums as $col) {
    			$item[$col] = Helpers::dataIsSetValue($el, $col);
    		}
    		$res[] = $item;
    	}
    	return $res;
    }

    //Заполняем массив значиениями keys
    static public function setArray(&$from, $keys)
    {
    	$res = [];
    	foreach ($keys as $key) {
    		$res[$key] = (isset($from[$key])) ? $from[$key] : '' ;
    	}
    	return $res;
    }

    //Поиск в массиве по ключу и значению
    static public function searchArray($arr, $key, $val)
    {
        foreach ($arr as $vArr) {
            if(isset($vArr[$key]) && $vArr[$key] == $val) return $vArr;
        }
        return false;
    }

    //Генерит список value label для селектов и прочего из списка данных
    static public function getHtmlOptions(&$from)
    {
    	$res = [];
    	if (is_array($from)) {
    		foreach ($from as $el) {
    			$r['value'] = $el['id'];
				$r['label'] = $el['name'];
				$res[] = $r;
    		}
    	}
    	return $res;
    }

    //Фукнция генерит из списка опций ассиотивный массив
    static public function optionsToArr($options)
    {
        $res = [];
        foreach ($options as $option) {
            $res[$option['value']] = $option['label'];
        }
        return $res;
    }

    //Ищем по опциям (value) можно передать и простой массив,тогда будет искаться по значению. Первый параметр список опиций, второ искомая строка или массив значний
    static public function optionsSearch($arr, $str)
    {
        if(!is_array($arr))return false;

        $count = 0;

        foreach ($arr as $el) {
            if(is_array($el)) {if(!isset($el['value'])) {$el['value'] = '';}}
            else $el = ['value' => $el];
            
            if(is_array($str)){
                foreach ($str as $lastStr) {
                    
                    if($el['value'] == $lastStr) {
                        $count++;
                    }
                }
            }elseif($el['value'] == $str) return true;
        }
        
        if(is_array($str) && count($str) == $count)return true;

        return false;
    }
    
    //Получает список айдишников из списка записей
    static public function getListIds(&$from)
    {
    	$res = [];
    	if (is_array($from)) {
    		foreach ($from as $el) {
				$res[] = $el['id'];
    		}
    	}
    	return $res;    	
    }


    // Выводим данные поля, если данных нет выводим возвращаем заничение 3 параметр, по умолчанию false
    static public function getDataField(&$data, $id, $res = false) {
        if ( isset($data[$id]) ) return $data[$id];

        if ( isset($data['array_data']['fields'][$id] ) ) return $data['array_data']['fields'][$id];
 
        return $res;
    }

    static public function dataIsSetValue(&$data, $id, $res = false) {
        return Helpers::getDataField($data, $id, $res);
    }

    //То же самое что предыдущее но выводим false если значиение пустое.
    static public function getDataFieldEmpty(&$data, $id){
        $value = Helpers::getDataField($data, $id);

        if($value != '' && $value !== false) return $value;
      
        return false;
    }

    //Генерит список атрибутов для хтмл поля из массива key=value
    static public function getAttrs($attr = [])
    {
    	$res = '';
    	foreach ($attr as $key => $value) {
    		$res .= ' '.$key.'="'.htmlspecialchars($value).'"';
    	}
    	return $res;
    }

}
