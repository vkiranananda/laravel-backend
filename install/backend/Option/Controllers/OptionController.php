<?php

namespace Backend\Option\Controllers;

class OptionController extends \Backend\Root\Option\Controllers\OptionController
{
    public function update($id)
    {
        // Игнорим текущую запись в валидации
        $this->fields['fields']['name']['validate'] =
            str_replace('NULL,id', $id . ',id', $this->fields['fields']['name']['validate']);

        return parent::update($id);
    }
}
