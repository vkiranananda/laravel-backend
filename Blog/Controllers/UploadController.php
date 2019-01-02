<?php

namespace Backend\Root\Blog\Controllers;

class UploadController extends \Backend\Root\MediaFile\Controllers\UploadController
{
	protected $moduleName = 'Blog';
    protected $configPath = 'Blog::upload';
}
