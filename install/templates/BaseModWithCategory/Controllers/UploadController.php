<?php

namespace Backend\BaseMod\Controllers;

class UploadController extends \Backend\Root\MediaFile\Controllers\UploadController
{
    protected $moduleName = 'BaseMod';
    protected $configPath = 'BaseMod::upload';
}
