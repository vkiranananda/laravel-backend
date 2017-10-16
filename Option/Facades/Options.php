<?php

namespace Backend\Option\Facades;

use Illuminate\Support\Facades\Facade;

class Options extends Facade {

    protected static function getFacadeAccessor() { return 'Backend\Option\Services\Options'; }

}

?>