<?php

namespace Backend\Root\Form\Services\Traits;
use Request;
use Log;
use Response;

trait IndexEditable
{
    protected function IndexEditableEdit($id, $fieldName)
    {
        if (!isset($this->fields['fields'][$fieldName])) abort(403, 'IndexEditable::IndexEditableEdit: field not found');

        $this->getPost($id, 'edit-owner');

        if (!$this->IndexEditableAccess($this->post, $this->fields['fields'][$fieldName])) abort(403, 'IndexEditable::IndexEditableEdit: access deny');

        $this->resourceCombine('index-edit');

        $res = [
            'config' => [
                'updateUrl' => action($this->config['controller-name'] . '@IndexEditableUpdate', [$id, $fieldName]),
            ],
            'field' => $this->fieldsPrep->editFields($this->post, [$this->fields['fields'][$fieldName]])[0],
        ];

        $this->resourceCombineAfter('index-edit');

        return $res;
    }

    protected function IndexEditableUpdate($id, $fieldName)
    {
        $this->getPost($id, 'edit-owner');

        if (!$this->IndexEditableAccess($this->post, $this->fields['fields'][$fieldName])) abort(403, 'IndexEditable::IndexEditableUpdate: access deny');

        $this->resourceCombine('index-update');

        // Сохраняем данные в запись
        $data = $this->fieldsPrep->saveField($this->post, $this->fields['fields'][$fieldName], Request::input('value', ''));

        // Если ошибка валидации
        if ($data['error'] !== true) {
            Response::json(['error' => $data['error']], 422)->send();
            die();
        }

        // Устанавливаем новое значние поста
        $this->post = $data['post'];

        // Хук перед сохранением
        $this->preSaveData('index-update');

        $this->post->save();

        $this->resourceCombineAfter('index-update');
    }

    /**
     * Подготавливает список url для редактирования поля
     * @param $post
     * @param $field
     * @return mixed // Возвращаем массив настроек
     */
    protected function IndexEditableConf($post, $field, $urlPostfix)
    {
        if ($this->getUserAccess('edit-owner', $post['user_id']) && $this->IndexEditableAccess($post, $field)) {
            return [
                'editUrl' => action($this->config['controller-name'] . '@IndexEditableEdit', [$post['id'], $field['name']]) . $urlPostfix,
            ];
        } else return false;
    }

    /**
     * Функция кастомной проверки доступа на запись. Если все ок, то true .
     * @param $post
     * @param $field
     * @return bool
     */
    protected function IndexEditableAccess($post, $field)
    {
        return true;
    }
}
