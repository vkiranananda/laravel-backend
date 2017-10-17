<?php

namespace Backend\Root\Form\Services\Traits;

trait RelationFields {

    protected function saveRelationFields($relationFields, $del = false)
    {
        if(count ($relationFields) > 0) {
            if($del) {
                $this->post->relationFields()->whereIn('field_name', array_keys($relationFields))->delete();
            }
            foreach ($relationFields as $key => $value) {
                if(is_array($value)){
                    foreach ($value as $lValue) {
                        $this->post->relationFields()->create([
                          'field_name' => $key, 'value' => $lValue,
                        ]);
                    }
                }else {
                    $this->post->relationFields()->create([
                      'field_name' => $key, 'value' => $value,
                    ]);
                }
            }
        }
    }

    protected function destroyRelationFields()
    {
        $this->post->relationFields()->delete();
    }
}