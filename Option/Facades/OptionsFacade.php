<?php

namespace Backend\Root\Option\Facades;

use Illuminate\Support\Facades\Facade;

class OptionsFacade extends Facade {

    protected static function getFacadeAccessor() { return 'Backend\Root\Option\Services\Options'; }

}

?>