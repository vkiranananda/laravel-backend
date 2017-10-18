<?php

namespace Backend\Root\Core\Facades;

use Illuminate\Support\Facades\Facade;

class GetConfigFacade extends Facade {

    protected static function getFacadeAccessor() { return '\Backend\Root\Core\Services\GetConfig'; }

}
