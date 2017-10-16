<?php

namespace Backend\MediaFile\Controllers;

class UploadController extends \Backend\Upload\Controllers\UploadController
{
    protected $module = 'MediaFile';
    protected $viewTemplate = 'MediaFile::list';

    public function index($id = '')
    {
    	$this->params['title'] = 'Медиа файлы';
        return parent::index(NULL);
    }
}
