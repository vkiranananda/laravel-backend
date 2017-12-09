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


        $this->params['upload-url'] = action('\\'.$baseNamespace.'\\'.$this->controller.'@store');
        $this->params['destroy-url'] = action('\\'.$baseNamespace.'\\'.$this->controller.'@destroy', '');

        $list = MediaFile::where('imageable_type', $this->module)->where('imageable_id', $id)->orderBy('id', 'desc')->get();

        return  [ 'data' => app('UploadedFiles')->prepGaleryData( $list ), 'params' => $this->params, ];

        // return view($this->viewTemplate, [ 'data' => UploadedFiles::prepGaleryData( $list ), 'params' => $this->params, ] );
    }


    public function store()
    {
        $this->validate(Request(), [ 'file' => $this->params['validate'] ] );
        $this->params['module'] = $this->module;
        $savedFile[] = Uploads::saveFile($this->params);
        return app('UploadedFiles')->prepGaleryData( $savedFile )[0];
    }

    public function destroy($id)
    {
        Uploads::deleteFiles( [ MediaFile::findOrFail($id) ] );
        MediaFile::destroy($id);
    }
}
