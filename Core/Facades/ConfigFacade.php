<?php

namespace Backend\Core\Facades;

use Illuminate\Support\Facades\Facade;

class ConfigFacade extends Facade {

    protected static function getFacadeAccessor() { return '\Backend\Core\Services\Config'; }

}
