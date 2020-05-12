<?php

namespace Backend\Option\Controllers;

class OptionController extends \Backend\Root\Form\Controllers\ResourceController
{
    public function update($id)
    {
        // Игнорим текущую запись в валидации
        $this->fields['fields']['name']['validate'] =
            str_replace('NULL,id', $id . ',id', $this->fields['fields']['name']['validate']);

        return parent::update($id);
    }

    public function index()
    {
        // Делаем полный запрет на выборку скрытых записей
        $this->post = $this->post->where('_hidden', false);
        return parent::index();
    }

    protected function resourceCombine($type)
    {
        // Делаем запрет на изменение записей если она скрыта
        if (array_search($type, ['store', 'update', 'edit', 'destroy']) !== false) {
            if ($this->post['_hidden'] == true) abort(403, 'Access deny');
        }

        parent::resourceCombine($type);
    }


}
