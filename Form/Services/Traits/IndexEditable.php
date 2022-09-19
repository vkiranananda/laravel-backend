<?php

namespace Backend\Root\Form\Services\Traits;

trait IndexEditable
{
    public function IndexEditableEdit($id, $fieldName)
    {
        $this->getPost($id, 'edit-owner');
    }

    public function IndexEditableUpdate($id, $fieldName)
    {
        $this->getPost($id, 'edit-owner');
    }

    public function IndexEditablePrep(&$post, &$field)
    {
        if (!$this->getUserAccess('edit-owner', $post['user_id'])) {
            unset($field['editable']);
        } else {

        }

    }
}
