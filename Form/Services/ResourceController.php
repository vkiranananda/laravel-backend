<?php

namespace Backend\Root\Form\Services;

use Request;
use App\Http\Controllers\Controller;
use Backend\Root\Core\Services\Helpers;
use Categories;
use App;
use Auth;
use URL;
use Backend\Root\Upload\Services\Uploads;
use \Backend\Root\Upload\Models\MediaFile;
use Content;
use DB;
use GetConfig;

class ResourceController extends Controller {

    use \Backend\Root\Form\Services\Traits\Fields;

    protected $params = [];
    protected $post;
    protected $cat = false;
    protected $pagination = 30;
    private $editTemplate = 'Form::edit';

    public function init(&$post, $config)
    {
        $this->post = &$post;
        // dd($config);
        $this->params = GetConfig::backend($config);
        $this->params['controllerName'] = '\\'.get_class($this);
        $this->params['baseClass'] = class_basename($this->post);
        $this->params['baseNamespace'] = (preg_match('/(^.+)\\\.+\\\.+/', get_class($this), $mathces)) ? $mathces[1] : '' ;
        if(isset($this->params['conf']['edit-template'])){
            $this->editTemplate = $this->params['conf']['edit-template'];
        }
    }

    public function index()
    {
        //Если подключен трейт с категориями
        if(method_exists($this, 'setCategoryList')){
            if( ($this->params['cat'] = Request::input('cat', false) )){
                $this->post = $this->post->where('category_id', $this->params['cat']);
                $this->params['url'] = '?cat='.$this->params['cat'];
            }else {
                abort(403, 'PostController категория не установлена');
            }
        }

        //Сортировка по умолчанию
        if(isset($this->params['conf']['order-by'])) {
            if(!isset($this->params['conf']['order-by-type']) || $this->params['conf']['order-by-type'] != 'desc'){
                $this->params['conf']['order-by-type'] = 'asc';
            }
            $this->post = $this->post->orderBy($this->params['conf']['order-by'], $this->params['conf']['order-by-type']);
        }else {
            $this->post = $this->post->orderBy('id', 'DESC');
        }
        
        if(method_exists($this, 'localize')) $this->localize();

        $this->params['lang']['title'] = $this->params['lang']['list-title'];

        $tmplite = (isset($this->params['conf']['list-template'])) ? $this->params['conf']['list-template'] : 'Form::list' ;

        //Подготавливаем поля
        $this->params['fields'] = Helpers::changeFieldsOptions($this->params['fields']);

        return view($tmplite, [ 'data' => $this->post->paginate($this->pagination), 'params' => $this->params ]);
    }

    public function create()
    {
        $this->resourceCombine('create');
        //For categories if trait enable
        if(method_exists($this, 'setCategoryList')) {
            $this->setCategoryList('create');
            $this->localize();
        }
        $this->params['fields'] = Forms::prepAllFields($this->post, $this->params['fields']);
        $this->params['url'] = action($this->params['controllerName'].'@store');
        $this->params['lang']['title'] = $this->params['lang']['create-title'];
        // if(!isset($this->params['previousUrl']))$this->params['previousUrl'] = URL::previous();
        $this->resourceCombineAfter('create');
        return view($this->editTemplate,[ 'params' => $this->params, 'data' => $this->post ]);
    }


    public function store()
    {
        if(method_exists($this, 'setCategoryList')) $this->setCategoryList('store');
        $this->resourceCombine('store');
        $relationFields = $this->SaveFields($this->post, $this->params['fields']);
        if(isset($this->params['conf']['user-id']) )$this->post->user_id = Auth::user()->id;
        $this->post->save();
        if(method_exists($this, 'saveRelationFields'))$this->saveRelationFields($relationFields);
        $this->saveMediaRelations();

        $this->resourceCombineAfter('store');
        
        if(isset($this->params['conf']['store-redirect'])){
            return ['redirect' => action($this->params['controllerName'].'@edit', $this->post->id)];
        }
        return [ 
            'url' => action($this->params['controllerName'].'@edit', $this->post->id), 
            'id' => $this->post->id, 
            'type' => 'save', 
            'updateUrl' => action($this->params['controllerName'].'@update', $this->post->id),
            'viewUrl' => $this->getViewUrl(),
        ];
    }

    public function edit($id)
    {
        if(!isset($this->post['id'])) $this->post = $this->post->findOrFail($id);
        $this->resourceCombine('edit');
        if(method_exists($this, 'setCategoryList')) {
            $this->setCategoryList('edit');
            $this->localize();
        }
        $this->params['fields'] = Forms::prepAllFields($this->post, $this->params['fields']);
        $this->params['url'] = action($this->params['controllerName'].'@update', $id);
        $this->params['lang']['title'] = $this->params['lang']['edit-title'];
        $this->params['viewUrl'] = $this->getViewUrl();
        // if(!isset($this->params['previousUrl']))$this->params['previousUrl'] = URL::previous();
        $this->resourceCombineAfter('edit');
        return view($this->editTemplate,[ 'params' => $this->params, 'data' => $this->post ]  );
    }

    public function update($id)
    {
        if(!isset($this->post['id'])) $this->post = $this->post->findOrFail( $id );
        $this->resourceCombine('update');
        if(method_exists($this, 'setCategoryList')) $this->setCategoryList('update');
        $relationFields = $this->SaveFields($this->post, $this->params['fields']);
        $this->post->save();

        if(method_exists($this, 'saveRelationFields'))$this->saveRelationFields($relationFields, true);

        $this->saveMediaRelations();
        $this->resourceCombineAfter('update');

        if(isset($this->params['conf']['update-redirect'])){
            return ['redirect' => action($this->params['controllerName'].'@edit', $this->post->id)];
        }

        return [ 
            'viewUrl' => $this->getViewUrl(), 
        ];
    }


    public function destroy($id)
    {
        $this->post = $this->post->findOrFail( $id );
        
        $this->resourceCombine('destroy');

        if(method_exists($this, 'destroyRelationFields'))$this->destroyRelationFields();

        if(isset($this->params['conf']['media-files']) && $this->params['conf']['media-files'] == 'hidden'){
            Uploads::deleteFiles( MediaFile::where('imageable_type', $this->params['baseClass'])->where('imageable_id', $id)->get() );

            MediaFile::where('imageable_type', $this->params['baseClass'])->where('imageable_id', $id)->delete();
        }
        $this->post->destroy($id);
        $this->resourceCombineAfter('destroy');
    }

    public function saveMediaRelations($imageable = false, $id = false, $type = 1)
    {
        $mediaFiles = Request::input('_media-file-uploaded-id', false);

        if( is_array($mediaFiles) && count($mediaFiles) > 0 ){
            
            if(isset($this->params['conf']['media-files']) && $this->params['conf']['media-files'] == 'hidden') $type = '2';

            if( $imageable == false ) $imageable = $this->params['baseClass'];
            if( $id == false ) $id = $this->post->id;

            MediaFile::whereIn('id', $mediaFiles)->where('imageable_type', $imageable)->update(['imageable_id' => $id, 'type' => $type ]);
        }
    }

    // Функция специально  для перегрузки, когда нужно выполнять различне групповые операции перед
    //Сохранием, обновлением, создание или редактированием
    protected function resourceCombine($type){ }

    //Тоже но после сохранения записи. Удобно кэши чистить и прочее..
    protected function resourceCombineAfter($type){ }


    //Функция возвращает урл поста
    protected function getViewUrl()
    {
        if(method_exists($this, 'setCategoryList')) {
            return Content::getUrl($this->post);
        }else {
            return '';
        }
    }
}