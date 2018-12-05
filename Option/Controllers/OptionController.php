<?php

namespace Backend\Root\Option\Controllers;

use Backend\Root\Option\Models\Option;
use Backend\Root\Core\Services\Helpers;
use Cache;
use GetConfig;

class OptionController extends \Backend\Root\Form\Controllers\ResourceController
{
    function __construct(Option $post )
    {
        parent::init($post);
    }

    public function index()
    {
    	//Для скрытых полей, типа general, не показывать их в списке.
        $this->post = $this->post->where('hidden', '0');
        
        return parent::index();
    }

    protected function resourceCombineAfter($type)
    {
    	//Удаляем кэши в виджетах
        if ( array_search($type, ['store', 'update', 'destroy']) !== false ) {
            Cache::tags('option-widgets')->flush();
        }
    }

    public function update($id)
    {
    	//Игнорим текущую запись в валидации
        $this->fields['fields']['name']['validate'] .= ','.$id;
        
        return parent::update($id);
    }
}