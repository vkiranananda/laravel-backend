<?php

namespace Backend\Root\Form\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Backend\Root\MediaFile\Models\MediaFile;
use Backend\Root\MediaFile\Models\MediaFileRelation;
use GetConfig;
use Illuminate\Support\Facades\Log;
use Request;
use Response;

class ResourceController extends Controller
{

    use \Backend\Root\Form\Services\Traits\Index;
    use \Backend\Root\Form\Services\Traits\ListSortable;

    //    use \Backend\Root\Form\Services\Traits\RelationFields;

    // Имя общего конфига, если false берется как config
    protected $configPath = false;

    // Имя конфига для полей, если false берется как fields
    protected $fieldsPath = false;

    // Конфиг общий
    protected $config = [];

    // Конфиг для полей
    protected $fields = [];

    // Переменная где содержатся данные поста
    protected $post = null;

    // Генерируемый массив с данными для веб
    protected $dataReturn = [];

    // Класс для работы с полями
    protected $fieldsPrep;

    // Модель данных с которой работаем
    public $model = false;


    // Инитим данные.
    function __construct()
    {

        // Получаем путь до модуля.
        $baseNamespace = strstr(get_class($this), '\\Controllers', true);
        // Получаем название модуля
        $moduleName = substr(strrchr($baseNamespace, '\\'), 1);

        // Получаем пути до конфигов
        if (!$this->configPath) $this->configPath = $moduleName . "::config";
        if (!$this->fieldsPath) $this->fieldsPath = $moduleName . "::fields";

        // Получаем конфиги
        $this->config = array_replace_recursive(
            GetConfig::backend('Form::config', true),
            GetConfig::backend($this->configPath)
        );

        $this->fields = GetConfig::backend($this->fieldsPath);

        // Получаем путь до модуля.
        $this->config['base-namespace'] = '\\' . $baseNamespace . '\\';
        // Получаем название модуля
        $this->config['module-name'] = $moduleName;
        // Текущий контроллер
        $this->config['controller-name'] = '\\' . get_class($this);
        // Проверям, если модель установлена в конфиге берем от туда, если нет то по умолчанию.
        $model = $this->config['model'] ?? $this->model;

        // Если модель нигде не установлена, пытемся сгенерить сами из имени модуля
        if (!$model) $model = $this->config['base-namespace'] . 'Models\\' . $moduleName;

        $this->post = new $model();

        $this->fieldsPrep = new \Backend\Root\Form\Services\Fields();

    }

    // Создаем запись вебка
    public function create()
    {
        $this->resourceCombine('create');

        // Если стоит опция клонирования получаем запись
        if (($clone = Request::input('clone', false))) {
            $this->post = $this->post->findOrFail($clone);
        }

        $this->dataReturn = [
            'config' => [
                'url' => action($this->config['controller-name'] . '@store'),
                'title' => $this->config['lang']['create-title'],
                'method' => 'post',
                'upload' => $this->uploadUrls($clone),
                'buttons' => $this->formEditButtons(),
                'clone-files' => ($this->cloneGetFiles($clone))
            ],
            'fields' => [
                'fields' => $this->fieldsPrep->editFields($this->post, $this->fields['fields']),
                'hidden' => $this->fieldsPrep->editHiddenFields($this->post, $this->fields['hidden'] ?? []),
                'tabs' => $this->fields['edit']
            ],
        ];
        $this->resourceCombineAfter('create');

        return view($this->config['edit']['template'], $this->dataReturn);
    }


    //Сохраняем запись
    public function store()
    {
        //Вызываем хук
        $this->resourceCombine('store');

        $this->saveData('store');

        // Обработка редиректов
        if (isset($this->config['store-redirect'])) {
            $this->dataReturn['redirect'] = $this->config['store-redirect'];
        } else {
            // Выставляем дополнительные параметры.
            $this->dataReturn = $this->edit($this->post['id']);
            $this->dataReturn['replaceUrl'] = action($this->config['controller-name'] . '@edit', $this->post['id']);
        }

        // Вызываем хук
        $this->resourceCombineAfter('store');

        return $this->dataReturn;
    }

    //Редактируем запись вебка
    public function edit($id)
    {
        //Если пост еще не получен, получаем его
        if (!isset($this->post['id'])) $this->post = $this->post->findOrFail($id);
        $this->resourceCombine('edit');

        $this->dataReturn = [
            'config' => [
                'url' => action($this->config['controller-name'] . '@update', $id),
                'title' => $this->config['lang']['edit-title'] ?? null,
                'method' => 'put',
                'viewUrl' => $this->getViewUrl(),
                'upload' => $this->uploadUrls(),
                'postId' => $this->post['id'],
                'buttons' => $this->formEditButtons(),
            ],
            'fields' => [
                'fields' => $this->fieldsPrep->editFields($this->post, $this->fields['fields']),
                'hidden' => $this->fieldsPrep->editHiddenFields($this->post, $this->fields['hidden'] ?? []),
                'tabs' => $this->fields['edit']
            ]
        ];

        $this->resourceCombineAfter('edit');

        if (Request::ajax()) return $this->dataReturn;

        return view($this->config['edit']['template'], $this->dataReturn);
    }


    //Обновляем запись
    public function update($id)
    {
        if (!isset($this->post['id'])) $this->post = $this->post->findOrFail($id);

        $this->resourceCombine('update');

        $data = $this->saveData('update');

        //Редиректы
        if (isset($this->config['update-redirect'])) $this->dataReturn['redirect'] = $this->config['update-redirect'];
        else $this->dataReturn = $this->edit($id);


        //Вызываем хук
        $this->resourceCombineAfter('update');

        return $this->dataReturn;
    }

    //!Показываем запись
    public function show($id)
    {
        if (!isset($this->post['id'])) $this->post = $this->post->findOrFail($id);

        $this->resourceCombine('show');

        $this->resourceCombineAfter('show');

        return view($this->config['show']['template'], [
            'config' => $this->config,
            'fields' => $this->fields,
            'data' => $this->post
        ]);
    }

    //Удаляем запись
    public function destroy($id)
    {
        if (!isset($this->post['id'])) $this->post = $this->post->findOrFail($id);

        $this->resourceCombine('destroy');

        $this->post->destroy($id);

        $this->resourceCombineAfter('destroy');

        return $this->dataReturn;
    }

    // Получаем список файлов для клонирования
    protected function cloneGetFiles($id)
    {
        $list = ($id) ? MediaFile
            ::join('media_file_relations as rel', 'rel.file_id', '=', 'media_files.id')
            ->where('rel.post_id', '=', $id)
            ->where('rel.post_type', '=', class_basename($this->post))
            ->select('media_files.id')
            ->get() : [];

        $res = [];

        foreach ($list as $file) $res[] = $file['id'];

        return $res;
    }

    // Сахраняем загруженные данные.
    public function saveMediaRelations($files, $imageable = false, $id = false)
    {
        if (is_array($files) && count($files) > 0) {

            // Возможность задать класс для сохранения файла
            if ($imageable == false) $imageable = class_basename($this->post);
            // Возможность задать id
            if ($id == false) $id = $this->post->id;

            foreach ($files as $fileId) {
                // Проверит есть ли запись firstOrCreate
                MediaFileRelation::firstOrCreate([
                    'file_id' => $fileId,
                    'post_id' => $id,
                    'post_type' => $imageable,
                ]);
            }
        }
    }


    /**
     * Сохраняем данные
     * @param string $method store | update
     */
    protected function saveData(string $method = 'store')
    {
        // Сохраняем данные в запись
        $data = $this->fieldsPrep->saveFields($this->post, $this->fields);

        // Если ошибка валидации
        if ($data['errors'] !== true) {
            Response::json(['errors' => $data['errors']], 422)->send();
            die();
        }
        // Устанавливаем новое значние поста
        $this->post = $data['post'];

        // Сохрням юзер id если запись новая
        if (isset($this->config['user-id']) && $method == 'store') $this->post->user_id = Auth::user()->id;

        // Хук перед сохранением
        $this->preSaveData($method);

        $this->post->save();

        //Сохраняем медиафайлы
        if ($this->config['upload']['enable']) $this->saveMediaRelations(Request::input('files', []));

        //Сохраяняем связи
        if (method_exists($this, 'saveRelationFields')) {
            // Удаялем все записи
            if ($method != 'store') $this->destroyRelationFields($this->post);
            // Добавляем новые
            $this->saveRelationFields($this->post, $data['relations']);
        }
    }

    // Получаем url для загрузки, $clone для включения клонирования в урл
    private function uploadUrls($clone = false)
    {
        if ($this->config['upload']['enable']) {

            $urlPostfix = ($clone == true) ? "?clone=" . $clone : '';

            return [
                'uploadUrl' => action($this->config['base-namespace'] . 'Controllers\\' . $this->config['upload']['controller'] . '@index', $this->post['id']) . $urlPostfix,
                'editUrl' => action($this->config['base-namespace'] . 'Controllers\\' . $this->config['upload']['controller'] . '@edit')
            ];
        } else return false;
    }

    /**
     * Генерируем кнопки внизу форму
     * @return array Массив кнопок
     */
    protected function formEditButtons() {
        if (isset($this->config['edit']['buttons'])) {
            $res = [];
            foreach ($this->config['edit']['buttons'] as $item) {
                // Если есть опция default, то берем значения из дефолтного
                if (isset($item['default'])) {
                    // объединяем массивы
                    $newItem = array_replace($this->config['edit']['buttons-default'][$item['default']], $item);
                    // Удаляем опцию дефаулт что бы не передавать в админку
                    unset($newItem['default']);
                    $res[] = $newItem;
                }
                else $res[] = $item;
            }
            return $res;
        }
        return $this->config['edit']['buttons-default'];
    }

    // Функция специально  для перегрузки, когда нужно выполнять различне групповые операции перед
    //Сохранием, обновлением, создание или редактированием
    protected function resourceCombine($type)
    {
    }

    //Тоже но после сохранения записи. Удобно кэши чистить и прочее..
    protected function resourceCombineAfter($type)
    {
    }

    // Вызывается перед сохранением данных. Что бы была возможность поменять что то в модели, после всех обработок. В параметре type указывается, store update
    protected function preSaveData($type)
    {
    }

    //Функция возвращает урл поста
    protected function getViewUrl()
    {
        return '';
    }
}
