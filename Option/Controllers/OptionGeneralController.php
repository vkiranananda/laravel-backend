<?php

namespace Backend\Root\Option\Controllers;

use Backend\Root\Option\Models\Option;
use Backend\Root\Core\Services\Helpers;
use GetConfig;
use Forms;
use Request;

class OptionGeneralController extends \App\Http\Controllers\Controller
{
    use \Backend\Root\Form\Services\Traits\Fields;

    private $fields;

    function __construct()
    {
        $this->fields = GetConfig::backend('Option::fields-general');
    }

    public function edit()
    {
        $option = Option::where('name', 'general')->first();
      
      	//Если запись не создана, ставим умолчания и создаем
        if (!$option) {
            $option = new Option();
            $option->name = 'general';
            $option->autoload = '1';
            $option->hidden = '1';
            $option->type = '';
            $array_fields['fields']['robots-index-deny'] = '1';
            $option->array_data = $array_fields;

            $option->save();
        }
        return view('Form::edit', [ 
	        	'config'	=> [
	        		'url' 		=> action('\Backend\Root\Option\Controllers\OptionGeneralController@update'),
	        		'title'		=> GetConfig::backend('Option::config-general')['lang']['title'],
	        		'method'	=> 'put',
	        	], 
	        	'fields'	=>	[
	        		'fields'	=> $this->prepEditFields( $this->fields['fields'], $option ),
	        		'tabs'		=> $this->fields['edit']
	        	]
        ]);
    }

    public function update()
    {
        $option = Option::where('name', 'general')->firstOrFail();
        
        $data = $this->SaveFields($option, $this->fields['fields'], Request::input('fields', []), $this->fields['edit']);

        if ( $data['errors'] !== true ) return Response::json([ 'errors' => $data['errors'] ], 422);

        $data['post']->save();
      }
}
