<?php

namespace Backend\Root\Option\Controllers;

use Backend\Root\Option\Models\Option;
use Backend\Root\Core\Services\Helpers;
use GetConfig;
use Forms;
use Request;

class OptionGeneralController extends \Backend\Root\Form\Controllers\ResourceController
{
    protected $configPath = 'Option::config-general';
    protected $fieldsPath = 'Option::fields-general';

    function __construct(Option $post)
    {
        parent::init($post);
    }

    public function edit($id = '')
    {
        $this->post = Option::where('name', 'general')->first();
      
      	//Если запись не создана, ставим умолчания и создаем
        if (!$this->post) {
        	$this->post = new Option;
            $this->post->name = 'general';
            $this->post->autoload = '1';
            $this->post->hidden = '1';
            $this->post->type = '';
            $array_fields['fields']['robots-index-deny'] = '1';
            $this->post->array_data = $array_fields;

            $this->post->save();
        }
        return parent::edit($this->post['id']);
    }
}
