<?php

namespace Backend\Category\Controllers;

class UploadController extends \Backend\Root\MediaFile\Controllers\UploadController
{
	protected $moduleName = 'Category';
    protected $configPath = 'Category::upload';
}
