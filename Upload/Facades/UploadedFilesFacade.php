<?php

namespace Backend\Root\Upload\Facades;

use Illuminate\Support\Facades\Facade;

class UploadedFilesFacade extends Facade {

    protected static function getFacadeAccessor() { return '\Backend\Root\Upload\Services\UploadedFiles'; }

}

?>