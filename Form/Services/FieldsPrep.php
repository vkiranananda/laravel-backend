<?php

/**
 * 
 */
class FieldsPrep
{
	//Подготавливаем одно поле для вывода
	static public function prepField ($field, $post)
	{
		$name = $field['name'];
		//Set value
		if(( $val = Helpers::dataIsSetValue($post, $name ) ) !== false)
			$res[$name]['value'] = $val;
		elseif(!isset($res[$name]['value']) ) $res[$name]['value'] = '';

		//Get all attached files
        if($field['type'] == 'files' || $field['type'] == 'gallery'){
            if(!is_array($field['value'])) $field['value'] = [];
            elseif(count($field['value']) > 0) {
            	$files = MediaFile::whereIn('id', $field['value'])
            		->orderByRaw(DB::raw("FIELD(id, ".implode(',', $field['value']).")"))
            		->get();
                $field['value'] = app('UploadedFiles')->prepGaleryData( $files );
            }
        }
        return $field;
	}
	//Подготавливаем поля для вывода 



	//Подготавливаем поля для вывода
	static public function _prepEditFields($fieldsConf, $post)
    {
    	$res = [];

    	$fields = $fieldsConf ['fields'];

    	foreach ($fieldsConf['tabs'] as $tab) {
			foreach ($tab['fields'] as $field) {
				if( !isset($field['name'] )) continue;
				$name = $field['name'];
				if( ! isset($fields[$name]) ) continue;

				$res[$name] = FieldsPrep::prepField(
					array_replace_recursive($fields[$field['name']], $field),
					$post
				);

		    }
		}
		return $res;
    }

}