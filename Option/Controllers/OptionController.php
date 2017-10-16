<?php

namespace Backend\Option\Controllers;

use Backend\Option\Models\Option;
use Backend\Core\Services\Helpers;
use Cache;
use BackendConfig;

class OptionController extends \Backend\Form\Services\ResourceController
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
            unset($this->params['fields']['autoload']);
        }elseif($type == 'update' || $type == 'edit') {
            
            $conf = BackendConfig::get('Option::options-'.$this->post['array_data']['fields']['type']);
            
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