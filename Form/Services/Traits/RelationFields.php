<?php

namespace Backend\Root\Form\Services\Traits;

trait RelationFields {

	//Добавляет новую связь
    protected function saveRelationFields($post, $relationFields)
    {
        if ( count ($relationFields) == 0 ) return;

        //Перебираем все поля для сохранения
        foreach ($relationFields as $key => $value) {
        	//Если поле массив дробим его на отдельные записи и сохраняем каждую 
            if ( is_array($value) ) {
                foreach ($value as $lValue) {
                    $post->relationFields()->create([
                      'field_name' => $key, 'value' => $lValue,
                    ]);
                }
            } else { //Иначе просто сохраянем.
                $post->relationFields()->create([
                  'field_name' => $key, 'value' => $value,
                ]);
            }
        }
    }

    //Удаляет все связи
    protected function destroyRelationFields($post)
    {
        $post->relationFields()->delete();
    }
}