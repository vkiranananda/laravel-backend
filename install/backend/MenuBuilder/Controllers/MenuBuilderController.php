<?php

namespace Backend\MenuBuilder\Controllers;

class MenuBuilderController extends \Backend\Root\Form\Controllers\ResourceController	
{
    public function update($id)
    {
    	// Игнорим текущую запись в валидации имени
        $this->fields['fields']['name']['validate'] = str_replace ('NULL,id', $id.',id', $this->fields['fields']['name']['validate']); 

        return parent::update($id);
    }
}