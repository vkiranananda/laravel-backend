<?php

namespace Backend\Core\Facades;

use Illuminate\Support\Facades\Facade;

class BackendFacade extends Facade {

    protected static function getFacadeAccessor() { return '\Backend\Core\Services\Backend'; }

}
