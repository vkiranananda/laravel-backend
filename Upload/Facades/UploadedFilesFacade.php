<?php

namespace Backend\Upload\Facades;

use Illuminate\Support\Facades\Facade;

class UploadedFilesFacade extends Facade {

    protected static function getFacadeAccessor() { return '\Backend\Upload\Services\UploadedFiles'; }

}

?>