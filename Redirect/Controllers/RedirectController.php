<?php

namespace Backend\Redirect\Controllers;

use Backend\Redirect\Models\Redirect;
use Request;

class RedirectController extends \Backend\Form\Services\ResourceController
{
    function __construct(Redirect $post)
    {
       parent::init($post, 'Redirect::redirect');
    }


    public function update($id)
    {
        $this->params['fields']['from_url']['validate'] .= ','.$id;
        return parent::update($id);
    }

}