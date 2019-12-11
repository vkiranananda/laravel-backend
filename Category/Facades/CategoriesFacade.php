<?php

namespace Backend\Root\Category\Facades;

use Illuminate\Support\Facades\Facade;

class CategoriesFacade extends Facade {

    protected static function getFacadeAccessor() { return 'Backend\Root\Category\Services\Categories'; }

}

?>