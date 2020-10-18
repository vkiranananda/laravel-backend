<?php

namespace Backend\Root\Option\Controllers;

class OptionController extends \Backend\Root\Form\Controllers\ResourceController
{
    public $model = 'Backend\Root\Option\Models\Option';
    public function index()
    {
        // Делаем полный запрет на выборку скрытых записей
        $this->post = $this->post->where('hidden', false);
        return parent::index();
    }

    protected function resourceCombine($type)
    {
        // Делаем запрет на изменение записей если она скрыта
        if (array_search($type, ['store', 'update', 'edit', 'destroy']) !== false) {
            if ($this->post['hidden'] == true) abort(403, 'Access deny');
        }

        parent::resourceCombine($type);
    }


}
