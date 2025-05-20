<?php

namespace Backend\Root\Form\Facades;

use Illuminate\Support\Facades\Facade;

class FormFieldFacade extends Facade {
    protected static function getFacadeAccessor() { return 'Backend\Root\Form\Services\FormField'; }
}

?>
