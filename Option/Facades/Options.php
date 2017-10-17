<?php

namespace Backend\Root\Option\Facades;

use Illuminate\Support\Facades\Facade;

class Options extends Facade {

    protected static function getFacadeAccessor() { return 'Backend\Root\Option\Services\Options'; }

}

?>