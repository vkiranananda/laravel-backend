<?php

namespace Backend\Root\Form\Fields;

use Helpers;

class SelectSearchField extends Field
{
    public function save($value)
    {

        if (isset($this->field['multiple'])) {
            if (!is_array($value)) $value = [];
            if (isset($this->field['max-items']) && count($value) > $this->field['max-items']) {
                abort(403, 'SelectSearchField has error. Value more max-items');
            }
        } else {
            if ($value === '') $value = null;
        }
        return $value;
    }
}


