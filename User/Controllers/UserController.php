<?php

namespace Backend\Root\User\Controllers;

use Backend\User\Models\UserRole;
use Illuminate\Support\Str;
use Auth;
use Helpers;
use Log;
use Mail;
use Request;

class UserController extends \Backend\Root\Form\Controllers\ResourceController
{
    use \Backend\Root\User\Services\UserAccessTrait;

    protected string $userAccessKey = 'User';

    public $model = 'App\Models\User';

    public function create()
    {
        // Генерим пароль
        $this->fields['fields']['password']['value'] = Str::random(8);

        return parent::create();
    }

    public function store()
    {
        // добавляем валидацию
        $this->fields['fields']['password']['validate'] .= '|required';

        return parent::store();
    }

    public function resourceCombineAfter($type)
    {
        // Если редактируем, то очищаем пароль и убираем поле отправки на email
        if ($type == 'edit') {
            if (isset($this->dataReturn['fields']['fields']['password'])) {
                $this->dataReturn['fields']['fields']['password']['value'] = '';
            }
            if (isset($this->dataReturn['fields']['fields']['send_mail'])) {
                unset($this->dataReturn['fields']['fields']['send_mail']);
            }
        }
        if ($type == 'show') {
            unset($this->dataReturn['fields']['fields']['send_mail']);
            unset($this->dataReturn['fields']['fields']['password']);
        }
    }

    public function resourceCombine($type)
    {
        if (array_search($type, ['store', 'update', 'edit', 'create', 'index', 'show']) !== false) {
            // Добавляем роли в список
            foreach (UserRole::orderBy('sort_num', 'desc')->get() as $role) {
                array_unshift($this->fields['fields']['user_role_id']['options'], [
                    'label' => $role->name, 'value' => $role->id
                ]);
            }
        }

        if ($type == 'update') {
            // Если пароль не был задан, оставляем тот что бы ранее
            if (Request::input('fields.password', '') == '') {
                // $this->fields['fields']['password']['field-save'] = 'none';
            }
        }
    }

    // Добавляем кнопку роли
    protected function indexListMenu($urlPostfix = '')
    {
        $res = parent::indexListMenu($urlPostfix);

        $res[] = [
            'label' => 'Роли',
            'url' => action('\Backend\User\Controllers\RoleController@index') . $urlPostfix,
            // Тип кнопка как в бутстрап
            'btn-type' => 'success'
        ];

        return $res;
    }

    // Обрабатываем ссылки в списке
    protected function indexLinks($post, $urlPostfix)
    {
        $res = parent::indexLinks($post, $urlPostfix);

        if ($post['user_role_id'] != 0) {
            $res['user-role'] = action('\Backend\User\Controllers\RoleController@edit', $post['user_role_id']);
        }

        return $res;
    }

    // Криптуем пароль и отправляем email
    protected function preSaveData($type)
    {
        if ($type == 'store') {
            if (Request::input('fields.send_mail', '') == 'yes') {
                Mail::to($this->post['email'])
                    ->send(new \Backend\User\Mail\UserMail($this->post));
            }
        }

        // Криптуем пароль
        $password = Request::input('fields.password', '');
        if ($password != '')
            $this->post['password'] = bcrypt($password);
    }

    // todo Зделать запрет на удаление
}
