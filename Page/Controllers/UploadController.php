<?php

namespace Backend\Root\Page\Controllers;

class UploadController extends \Backend\Root\MediaFile\Controllers\UploadController
{
    protected $moduleName = 'Page';
    protected $configPath = 'Page::upload';
}
