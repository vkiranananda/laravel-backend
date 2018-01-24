<?php

namespace Backend\Root\Upload\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Backend\Root\Upload\Models\MediaFile;
use Forms;
use GetConfig;

class EditController extends Controller
{
	use \Backend\Root\Form\Services\Traits\Fields;

    public function getInfo($id)
    {
    	$file = MediaFile::findOrFail($id);
    	$config = GetConfig::backend("Upload::edit");

    	$this->_getFields($config, $file);

    	return [
    		'fields' 	=> Forms::prepAllFields( $file, $config['fields'] ), 
    		'saveUrl'	=> $config['conf']['save-info-url']
    	];
    }

    private function _getFields(&$config, &$file)
    {
    	if($file['file_type'] != 'image'){
    		unset($config['fields']['img_title'], $config['fields']['img_alt']);
    	}
    }

    public function saveInfo(Request $request, $id)
    {
    	$file = MediaFile::findOrFail($id);

    	$config = GetConfig::backend("Upload::edit");

    	$this->_getFields($config, $file);

    	$this->saveFields($file, $config['fields']);

    	$file->save();
    }

}
