<?php

namespace Backend\Root\Form\Services\Traits;
use Helpers;
use Request;

trait ListSortable {

    protected function listSortable()
    {
        $this->resourceCombine('sortable');

        if( method_exists($this, 'checkCategory') ){
            $this->params['cat'] = Request::input('cat', false);
            if( $this->checkCategory($this->params['cat']) ) {
                $this->post = $this->post->where('category_id', $this->params['cat']);
            }
        }

      	$this->post = $this->post->orderBy('sort_num');

        $fields = Helpers::getFields($this->params['fields'], $this->params['sortable'], true);
        $data = [];

        $this->post->chunk(200, function ($posts) 
        use (&$data, $fields)
        {
        	$data = array_merge ( $data, Helpers::getArrayItems($posts, $fields) );    
		});

        return [ 'fields' => $fields, 'data' => $data ];
    }

    protected function listSortableSave()
    {
    	$this->resourceCombine('sortable-save');
    	
    	$sortArr = Request::input('list-items');
    	$sortArrRev = array_flip($sortArr);

        $this->post->whereIn('id', $sortArr)->chunk(200, function ($posts) 
        use (&$sortArrRev)
        {
        	foreach ($posts as $post) {
        		if($post['sort_num'] != $sortArrRev[$post['id']]){
        			$post['sort_num'] = $sortArrRev[$post['id']];
        			$post->save();
        		}
        	}
		});

		$this->resourceCombineAfter('sortable-save');
		
		return ['reload' => true];
    }
}