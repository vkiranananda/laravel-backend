<?php

namespace Backend\Root\News\Controllers;

class UploadController extends \Backend\Root\MediaFile\Controllers\UploadController
{
	protected $moduleName = 'News';
    protected $configPath = 'News::upload';
}
