<?php

namespace Backend\Category\Facades;

use Illuminate\Support\Facades\Facade;

class CategoriesFacade extends Facade {

    protected static function getFacadeAccessor() { return 'Backend\Category\Services\Categories'; }

}

?>