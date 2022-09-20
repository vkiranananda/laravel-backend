<?php

namespace Backend\Root\Form\Services\Traits;

trait IndexEditable
{
    protected function IndexEditableEdit($id, $fieldName)
    {
        $this->getPost($id, 'edit-owner');

        if (!$this->IndexEditableAccess($post, $field)) return false;
    }

    protected function IndexEditableUpdate($id, $fieldName)
    {
        $this->getPost($id, 'edit-owner');

        if (!$this->IndexEditableAccess($post, $field)) return false;
    }

    /**
     * Подготавливает список url для редактирования поля
     * @param $post
     * @param $field
     * @return mixed // Возвращаем массив ссылок или false если поле нельзя редактировать.
     */
    protected function IndexEditableLinks($post, $field, $urlPostfix)
    {
        if ($this->getUserAccess('edit-owner', $post['user_id']) && $this->IndexEditableAccess($post, $field)) {
            return [
                'edit' => action($this->config['controller-name'] . '@IndexEditableEdit', [$post['id'], $field['name']]) . $urlPostfix,
                'update' => action($this->config['controller-name'] . '@IndexEditableUpdate', [$post['id'], $field['name']]) . $urlPostfix
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
