<?php

namespace Backend\Option\Controllers;

use Backend\Option\Models\Option;
use Helpers;
use Cache;
use GetConfig;

class OptionController extends \Backend\Root\Form\Controllers\ResourceController
{
    public function update($id)
    {
    	// Игнорим текущую запись в валидации
    	$this->fields['fields']['name']['validate'] = 
    		str_replace ('NULL,id', $id . ',id', $this->fields['fields']['name']['validate']); 
        
        return parent::update($id);
    }
}