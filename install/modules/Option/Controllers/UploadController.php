<?php

namespace Backend\Root\Option\Controllers;

class UploadController extends \Backend\Root\MediaFile\Controllers\UploadController
{
	protected $moduleName = 'Option';
    protected $configPath = 'Option::upload';
}
