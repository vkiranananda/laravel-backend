<?php

namespace Backend\Root\Form\Services\Traits;

use Helpers;
use Log;
use Request;

trait ListSortable
{

    protected function listSortable()
    {
        if (! $this->getUserAccess('edit-all')) abort(403, 'Access deny!');

        $this->resourceCombine('sortable');

        $this->post = $this->post->orderBy('sort_num');

        $fields = [];

        if (!isset($this->fields['sortable'])) {
            abort('418', 'Sortable error: необходимо задать список полей в конфиге fields для sortable');
        }

        foreach ($this->fields['sortable'] as $name) {
            $fields[] = $this->fields['fields'][$name];
        }

        $data = [];

        $this->post->chunk(200, function ($posts)
        use (&$data, $fields) {
            $data = array_merge($data, Helpers::getArrayItems($posts, $fields));
        });

        return ['fields' => $fields, 'data' => $data];
    }

    protected function listSortableButton($url_postfix)
    {
        if (isset($this->config['list']['sortable']) && $this->config['list']['sortable'] && $this->getUserAccess('edit-all')) {
            return [
                'label' => 'Сортировка',
                // Для сохранения использутеся тот же url но метод put
                'url' => isset($this->config['list']['url-sortable'])
                    ? $this->config['list']['url-sortable'] . $url_postfix
                    : action($this->config['controller-name'] . '@listSortable') . $url_postfix,
                'type' => 'sortable'
            ];
        } else return false;
    }

    protected function listSortableSave()
    {
        if (! $this->getUserAccess('edit-all')) abort(403, 'Access deny!');

        $this->resourceCombine('sortable-save');

        $sortArr = Request::input('items', []);
        $sortArrRev = array_flip($sortArr);

        $this->post->whereIn('id', $sortArr)->chunk(200, function ($posts)
        use (&$sortArrRev) {
            foreach ($posts as $post) {
                if ($post['sort_num'] != $sortArrRev[$post['id']]) {
                    $post['sort_num'] = $sortArrRev[$post['id']];
                    $post->save();
                }
            }
        });

        $this->resourceCombineAfter('sortable-save');

        return $this->dataReturn;
    }
}
