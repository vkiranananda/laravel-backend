<?php


namespace Backend\Root\Option\Controllers;

use Option;
use Backend\Root\Form\Controllers\ResourceController;

class OptionResourcesController extends ResourceController
{
    public $model = '\Backend\Root\Option\Models\Option';
    public $optionName = false;

    public function __construct()
    {
        parent::__construct();

        if (!isset($this->config['options']['name']) || $this->config['options']['name'] == '') abort(418, 'не указан параметр option-name в конфиге');

        $this->optionName = $this->config['options']['name'];
    }

    public function edit($id = 0)
    {
        $this->createIfNotExist();
        return parent::edit($id);
    }

    public function update($id = 0)
    {
        $this->createIfNotExist();
        return parent::update($id);
    }

    /**
     * Создаем опцию если она не существует
     */
    protected function createIfNotExist()
    {
        $this->post = Option::getModel($this->optionName, true, $this->config['options']['default'] ??  []);
    }
}
