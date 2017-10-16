<?php

namespace Backend\Option\Controllers;

use Backend\Option\Models\Option;
use Backend\Core\Services\Helpers;
use BackendConfig;

class OptionGeneralController extends \App\Http\Controllers\Controller
{
    use \Backend\Form\Services\Traits\Fields;
    private $params;
    function __construct()
    {
        $this->params = BackendConfig::get('Option::options-general');
        $this->params['controllerName'] = '\\'.get_class($this);
        $this->params['baseClass'] = 'Option';
    }

    public function edit()
    {
        $option = Option::where('name', 'general')->first();
        if(!$option) {
            $option = new Option();
            $option->name = 'general';
            $option->autoload = '1';
            $option->hidden = '1';
            $array_fields['fields']['robots-index-deny'] = '1';
            $option->array_data = $array_fields;

            $option->save();
        }
        $this->params['url'] = action($this->params['controllerName'].'@update');

        $this->prepEditFields($option, $this->params['fields']);

        return view('Form::edit',[ 'params' => $this->params, 'data' => $option ]  );
    }

    public function update()
    {
        $option = Option::where('name', 'general')->firstOrFail();
        $this->SaveFields($option, $this->params['fields']);
        $option->save();
      }
}
