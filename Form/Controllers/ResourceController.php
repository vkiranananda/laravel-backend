<?php

namespace Backend\Root\Form\Controllers;

use App\Http\Controllers\Controller;
use Backend\Root\MediaFile\Models\MediaFile;
use Backend\Root\MediaFile\Models\MediaFileRelation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Auth;
use GetConfig;
use Request;
use Response;

class ResourceController extends Controller
{
  use \Backend\Root\Form\Services\Traits\Index;
  use \Backend\Root\Form\Services\Traits\ListSortable;
  use \Backend\Root\Form\Services\Traits\IndexEditable;

  //    use \Backend\Root\Form\Services\Traits\RelationFields;

  // Имя общего конфига, если false берется как config
  public $model = false;

  // Имя конфига для полей, если false берется как fields
  protected $configPath = false;

  // Основной конфиг  по умолчанию config
  protected $fieldsPath = false;

  // Если true то конфиг берется из плагина иначе из бэкенда
  protected $configRoot = false;

  // Конфиг полей по умолчанию fields
  protected $config = [];

  // Переменная где содержатся данные поста
  protected $fields = false;

  // Генерируемый массив с данными для веб
  protected $post = null;

  // Класс для работы с полями
  protected $dataReturn = [];

  // Модель данных с которой работаем
  protected $fieldsPrep;

  // Первый вызванный метод класса.
  private $_firstMethod = false;

  // Инитим данные.
  function __construct()
  {
    // Получаем путь до модуля.
    $baseNamespace = strstr(get_class($this), '\Controllers', true);
    // Получаем название модуля
    $moduleName = substr(strrchr($baseNamespace, '\\'), 1);

    // Получаем пути до конфигов
    if (!$this->configPath)
      $this->configPath = $moduleName . '::config';

    // Получаем конфиги
    $this->config = array_replace_recursive(
      GetConfig::backend('Form::config', true),
      GetConfig::backend($this->configPath, $this->configRoot)
    );


    // Получаем путь до модуля.
    $this->config['base-namespace'] = '\\' . $baseNamespace . '\\';
    // Получаем название модуля
    $this->config['module-name'] = $moduleName;
    // Текущий контроллер
    $this->config['controller-name'] = '\\' . get_class($this);
    // Проверям, если модель установлена в конфиге берем от туда, если нет то по умолчанию.
    $model = $this->config['model'] ?? $this->model;

    // Если модель нигде не установлена, пытемся сгенерить сами из имени модуля
    if (!$model)
      $model = $this->config['base-namespace'] . 'Models\\' . $moduleName;

    $this->post = new $model();

    $this->loadFields();

    $this->fieldsPrep = new \Backend\Root\Form\Services\Fields();
  }

  /**
   * Получаем массив fields
   */
  protected function loadFields()
  {
    if (!$this->fieldsPath)
      $this->fieldsPath = $this->config['module-name'] . '::fields';
    $this->fields = GetConfig::backend($this->fieldsPath, $this->configRoot);
  }

  // Создаем запись вебка

  /**
   * Сохраняем запись
   * @return array
   */
  public function store()
  {
    // Проверка на права доступа
    if (!$this->getUserAccess('create'))
      abort(403, 'Access deny!');

    $this->setFirstMethod('store');
    $this->resourceCombine('store');
    $this->saveData('store');
    $this->resourceCombineAfter('store');
    $this->storeRedirect();

    return $this->dataReturn;
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
    if (isset($this->config['user-id']) && $method == 'store')
      $this->post->user_id = Auth::user()->id;

    // Хук перед сохранением
    $this->preSaveData($method);

    $this->post->save();

    // Сохраняем медиафайлы
    $this->saveMediaRelations($this->fieldsPrep->getFilesToSave());

    // Сохраяняем связи
    if (method_exists($this, 'saveRelationFields')) {
      // Удаялем все записи
      if ($method != 'store')
        $this->destroyRelationFields($this->post);
      // Добавляем новые
      $this->saveRelationFields($this->post, $data['relations']);
    }
  }

  protected function preSaveData($type) {}

  public function saveMediaRelations($files, $imageable = false, $id = false)
  {
    // Возможность задать класс для сохранения файла
    if ($imageable == false)
      $imageable = class_basename($this->post);
    // Возможность задать id
    if ($id == false)
      $id = $this->post->id;

    // Удаляем все существующие связи для данного поста и типа
    MediaFileRelation::where('post_id', $id)
      ->where('post_type', $imageable)
      ->delete();

    // Сохраняем новые связи одним запросом
    $relations = [];
    foreach ($files as $fileId) {
      $relations[] = [
        'file_id' => $fileId,
        'post_id' => $id,
        'post_type' => $imageable,
      ];
    }
    
    if (!empty($relations)) {
      MediaFileRelation::insert($relations);
    }
  }

  //    public function edit

  // Редактируем запись вебка

  /**
   * Обработка редиректов при создании записи.
   */
  public function storeRedirect()
  {
    $redirect = $this->config['store-redirect'] ?? $this->config['redirect'] ?? false;

    if ($redirect)
      $this->dataReturn['redirect'] = $redirect;
    else {
      // Выставляем дополнительные параметры.
      $this->dataReturn = $this->edit($this->post['id']);
      $this->dataReturn['replaceUrl'] = action($this->config['controller-name'] . '@edit', $this->post['id']);
    }
  }

  // Обновляем запись

  public function edit($id)
  {
    $this->getPost($id, 'edit-owner');

    $this->resourceCombine('edit');

    $this->dataReturn = [
      'config' => [
        'url' => action($this->config['controller-name'] . '@update', $id),
        'title' => $this->config['lang']['edit-title'] ?? null,
        'method' => 'put',
        'viewUrl' => $this->getViewUrl(),
        'upload' => $this->uploadUrls(),
        'postId' => $id,
        'buttons' => $this->formEditButtons(),
        'updated' => $this->getCurrentDateStr()
      ],
      'fields' => [
        'hidden' => $this->fieldsPrep->editHiddenFields($this->post, $this->fields['hidden'] ?? []),
      ]
    ];

    // Обновляем поля если reload-fields = true или первый метод не равен сохранию или обновлению записи
    if ($this->config['reload-fields'] || !in_array($this->getFirstMethod(), ['store', 'update'])) {
      $this->dataReturn['fields']['fields'] = $this->fieldsPrep->editFields($this->post, $this->fields['fields']);
      $this->dataReturn['fields']['tabs'] = $this->fields['edit'];
    }

    $this->resourceCombineAfter('edit');

    if (Request::ajax())
      return $this->dataReturn;

    return view($this->config['edit']['template'], $this->dataReturn);
  }

  /**
   * Получаем пост, если он не был получен и делает проверку на права доступа. При ошибке прерывает процесс.
   * @param $id
   * @param $access
   * @return void
   */
  public function getPost($id, $access)
  {
    if (!isset($this->post['id']))
      $this->post = $this->post->findOrFail($id);

    // Проверка на права доступа
    if (!$this->getUserAccess($access, $this->post['user_id']))
      abort(403, 'Access deny!');
  }

  // Получаем текущую дату в тексте

  protected function getViewUrl()
  {
    return '';
  }

  public function getCurrentDateStr()
  {
    if (!isset($this->post['updated_at']))
      return '';

    $date = !is_object($this->post['updated_at']) ? Carbon::create($this->post['updated_at']) : $this->post['updated_at'];

    return $date->toDateTimeString();
  }

  // !Показываем запись

  public function create()
  {
    // Проверка на права доступа
    if (!$this->getUserAccess('create'))
      abort(403, 'Access deny!');

    $this->resourceCombine('create');

    // Если стоит опция клонирования получаем запись
    if (($clone = Request::input('clone', false))) {
      $this->post = $this->post->findOrFail($clone);

      // Проверка на права доступа клонируемой записи
      if (!$this->getUserAccess('read-owner', $this->post['user_id']))
        abort(403, 'Access deny!');
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

  // Удаляем запись

  /**
   * Функция заглушка для перегрузки на проверку прав доступа.
   * @param $access - тип доступа edit-all, edit-owner, read-all, read-owner, create, destroy-all, destroy-owner
   * @param $userId - Если указан будет учавствовать в типах read-owner, edit-owner, delete-owner, если не указан
   * вернет true если разрешена хоть какая то запись.
   * @param $accessKey - Если нужно переопределить ключ
   * @return bool - Вернет true или false в зависимости от типа запроса.
   */
  protected function getUserAccess($access, $userId = false, $accessKey = false)
  {
    return true;
  }

  protected function resourceCombine($type) {}

  // Сахраняем загруженные данные.

  private function uploadUrls($clone = false)
  {
    if (isset($this->config['upload']['route-list'])) {
      // $urlPostfix = ($clone == true) ? "?clone=" . $clone : '';

      return [
        'listUrl' => action($this->config['upload']['route-list'])
      ];
    } else
      return false;
  }

  /**
   * Генерируем кнопки внизу формы
   * @return array Массив кнопок
   */
  protected function formEditButtons()
  {
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
        } else
          $res[] = $item;
      }
      return $res;
    }
    return $this->config['edit']['buttons-default'];
  }

  // Получаем url для загрузки, $clone для включения клонирования в урл

  protected function cloneGetFiles($id)
  {
    $list = ($id) ? MediaFile::join('media_file_relations as rel', 'rel.file_id', '=', 'media_files.id')
      ->where('rel.post_id', '=', $id)
      ->where('rel.post_type', '=', class_basename($this->post))
      ->select('media_files.id')
      ->get() : [];

    $res = [];

    foreach ($list as $file)
      $res[] = $file['id'];

    return $res;
  }

  protected function resourceCombineAfter($type) {}

  protected function getFirstMethod()
  {
    return $this->_firstMethod;
  }

  protected function setFirstMethod($method)
  {
    if ($this->_firstMethod === false)
      $this->_firstMethod = $method;
  }

  public function update($id)
  {
    $this->setFirstMethod('update');

    $this->getPost($id, 'edit-owner');

    $this->checkUpdateDate();

    $this->processStep = 'update';

    $this->resourceCombine('update');
    $this->saveData('update');

    $this->updateRedirect();

    // Вызываем хук
    $this->resourceCombineAfter('update');

    return $this->dataReturn;
  }

  // Функция специально  для перегрузки, когда нужно выполнять различне групповые операции перед
  // Сохранием, обновлением, создание или редактированием

  public function checkUpdateDate()
  {
    if ($this->getCurrentDateStr() != Request::input('updated', '')) {
      Log::debug($this->getCurrentDateStr());
      Log::debug(Request::input('updated', ''));
      abort(403, 'Кто то внес изменения в эту запись пока вы ее редактировали, вам необходимо обновить страницу и внести изменения по новой');
    }
  }

  // Тоже но в конце функции перед return. Удобно кэши чистить и прочее..

  /**
   * Обработка редиректов при обновлении записи.
   */
  public function updateRedirect()
  {
    $redirect = $this->config['update-redirect'] ?? $this->config['redirect'] ?? false;

    if ($redirect)
      $this->dataReturn['redirect'] = $redirect;
    else
      $this->dataReturn = $this->edit($this->post['id']);
  }

  // Вызывается перед сохранением данных. Что бы была возможность поменять что то в модели, после всех обработок. В параметре type указывается, store update

  public function show($id)
  {
    $this->getPost($id, 'read-owner');

    $this->resourceCombine('show');

    $this->dataReturn = [
      'config' => [
        'title' => $this->config['lang']['show-title'] ?? null,
        'viewUrl' => $this->getViewUrl(),
        'buttons' => $this->formShowButtons(),
        'readonly' => true
      ],
      'fields' => [
        'fields' => $this->fieldsPrep->readFields($this->post, $this->fields['fields']),
        'tabs' => $this->fields['edit']
      ]
    ];

    $this->resourceCombineAfter('show');

    if (Request::ajax())
      return $this->dataReturn;

    return view($this->config['show']['template'], $this->dataReturn);
  }

  /**
   * Генерируем кнопки внизу формы
   * @return array Массив кнопок
   */
  protected function formShowButtons()
  {
    // Если доступ есть добавляем ссылку в кнопку редактирования
    if ($this->config['show']['edit'] === true && $this->getUserAccess('edit-owner', $this->post['user_id'])) {
      $this->config['show']['buttons-default']['edit']['url'] = action($this->config['controller-name'] . '@edit', $this->post['id']);
    } else {
      unset($this->config['show']['buttons-default']['edit']);
    }
    if (isset($this->config['show']['buttons'])) {
      $res = [];
      foreach ($this->config['show']['buttons'] as $item) {
        // Если есть опция default, то берем значения из дефолтного
        if (isset($item['default'])) {
          // объединяем массивы
          $newItem = array_replace($this->config['show']['buttons-default'][$item['default']], $item);
          // Удаляем опцию дефаулт что бы не передавать в админку
          unset($newItem['default']);
          $res[] = $newItem;
        } else
          $res[] = $item;
      }
      return $res;
    }
    return $this->config['show']['buttons-default'];
  }

  // Функция возвращает урл поста

  public function destroy($id)
  {
    $this->getPost($id, 'destroy-owner');

    $this->resourceCombine('destroy');

    $this->post->destroy($id);

    $this->resourceCombineAfter('destroy');

    return $this->dataReturn;
  }
}
