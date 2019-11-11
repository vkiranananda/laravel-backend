<?php

namespace Backend\Root\Form\Services\Traits;
use Helpers;
use Request;
use Log;

trait ListSortable {

    protected function listSortable()
    {
        $this->resourceCombine('sortable');

      	$this->post = $this->post->orderBy('sort_num');

      	$fields = [];

      	foreach ($this->fields['sortable'] as $name) {
      		$fields[] = $this->fields['fields'][$name];
      	}

        $data = [];

        $this->post->chunk(200, function ($posts) 
        use (&$data, $fields)
        {
        	$data = array_merge ( $data, Helpers::getArrayItems($posts, $fields) );    
		});

        return [ 'fields' => $fields, 'data' => $data ];
    }

    protected function listSortableButton($url_postfix) 
    {
    	return [
			'label' => 'Сортировка',
			// Для сохранения использутеся тот же url но метод put
			'url'	=> isset($this->config['list']['url-sortable']) 
				? $this->config['list']['url-sortable'] . $url_postfix
				: action($this->config['controller-name'].'@listSortable') . $url_postfix,
			'type'	=> 'sortable'
		];
    }

    protected function listSortableSave()
    {
    	$this->resourceCombine('sortable-save');
    	
    	$sortArr = Request::input('items', []);
    	$sortArrRev = array_flip($sortArr);

        $this->post->whereIn('id', $sortArr)->chunk(200, function ($posts) 
        use (&$sortArrRev)
        {
        	foreach ($posts as $post) {
        		if ($post['sort_num'] != $sortArrRev[$post['id']]){
        			$post['sort_num'] = $sortArrRev[$post['id']];
        			$post->save();
        		}
        	}
		});

		$this->resourceCombineAfter('sortable-save');

		return $this->dataReturn;
    }
}