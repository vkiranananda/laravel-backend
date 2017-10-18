<?php

namespace Backend\Root\Upload\Controllers;

use Backend\Root\Core\Services\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Backend\Root\Upload\Models\MediaFile;
use Backend\Root\Upload\Services\Uploads;
use Content;
use GetConfig;
//type 0 deleted, 1 good, 2 hidden(for only field)

class UploadController extends Controller
{

	protected $module = '';
    protected $configFile = 'uploads';
    protected $controller = 'UploadController';
    protected $viewTemplate = 'Upload::list-modal';
    protected $params;

    public function __construct()
    {
       setlocale(LC_ALL, 'ru_RU.utf8');

       $this->params = GetConfig::backend($this->module."::".$this->configFile);
    }

    public function index($id = '')
    {
	$baseNamespace = (preg_match('/(^.+)\\\.+/', get_class($this), $mathces)) ? $mathces[1] : '' ;

        $list = MediaFile::where('imageable_type', $this->module)->where('imageable_id', $id)->orderBy('created_at', 'desc')->get();

        $this->params['upload-url'] = action('\\'.$baseNamespace.'\\'.$this->controller.'@store');
        $this->params['destroy-url'] = action('\\'.$baseNamespace.'\\'.$this->controller.'@destroy', '');

        return view($this->viewTemplate, [ 'data' => $list, 'params' => $this->params, ] );
    }

    public function store()
    {
        $this->validate(Request(), [ 'file' => $this->params['validate'] ] );
        $this->params['module'] = $this->module;
        $savedFile = Uploads::saveFile($this->params);
        
        if($savedFile->file_type == 'image'){
            $urls = Content::genImgLink($savedFile, [128, 128, 'fit']);
            $savedFile['thumb_url'] = $urls['thumb'];
            $savedFile['orig_url'] = $urls['orig'];
        }else {
            $savedFile['orig_url'] = Content::genFileLink($savedFile);
        }

        $savedFile['data_get_url'] = action('\Backend\Root\Upload\Controllers\EditController@getInfo', $savedFile['id']); 
        $savedFile['data_save_url'] = action('\Backend\Root\Upload\Controllers\EditController@saveInfo', $savedFile['id']);

        return $savedFile;
    }

    public function destroy($id)
    {
        Uploads::deleteFiles( [ MediaFile::findOrFail($id) ] );
        MediaFile::destroy($id);
    }
}
