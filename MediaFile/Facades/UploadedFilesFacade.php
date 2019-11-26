<?php

namespace Backend\Root\MediaFile\Facades;

use Illuminate\Support\Facades\Facade;

class UploadedFilesFacade extends Facade {

    protected static function getFacadeAccessor() { return '\Backend\Root\MediaFile\Services\UploadedFiles'; }

}
