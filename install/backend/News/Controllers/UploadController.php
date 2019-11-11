<?php

namespace Backend\News\Controllers;

class UploadController extends \Backend\Root\MediaFile\Controllers\UploadController
{
	protected $moduleName = 'News';
    protected $configPath = 'News::upload';
}
