<?php

namespace Backend\Controllers;

class UploadController extends \Backend\Root\MediaFile\Controllers\UploadController
{
	protected $moduleName = 'Blog';
    protected $configPath = 'Blog::upload';
}
