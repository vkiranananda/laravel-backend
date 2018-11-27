<?php

namespace Backend\Root\Option\Controllers;

use Backend\Root\Option\Models\Option;
use Backend\Root\Core\Services\Helpers;
use Cache;
use GetConfig;

class OptionController extends \Backend\Root\Form\Services\ResourceController
{
    function __construct(Option $post )
    {
        parent::init($post, 'Option::options');
    }

    public function index()
    {
        $this->post = $this->post->where('hidden', '0');
        return parent::index();
    }

    protected function resourceCombine($type)
    {

        if($type == 'store' || $type == 'create'){
            unset($this->fields['fields']['autoload']);
        }elseif($type == 'update' || $type == 'edit') {
            
            $conf = GetConfig::backend('Option::options-'.$this->post['array_data']['fields']['type']);
            
            $this->params = array_merge_recursive($this->params, $conf);
            $this->params['fields']['value']['field-save'] = 'array';

            unset($this->params['fields']['type']);
        }
    }

    protected function resourceCombineAfter($type)
    {
        if( array_search($type, ['store', 'update', 'destroy']) !== false ){
            Cache::tags('option-widgets')->flush();
        }
    }

    public function update($id)
    {
        $this->params['fields']['name']['validate'] .= ','.$id;
        return parent::update($id);
    }
}