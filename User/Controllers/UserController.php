<?php

namespace Backend\Root\User\Controllers;

use Backend\User\Models\UserRole;
use Illuminate\Support\Str;
use Mail;
use Request;
use Helpers;

class UserController extends \Backend\Root\Form\Controllers\ResourceController
{
    use \Backend\Root\User\Services\UserAccessTrait;

    protected string $userAccessKey = 'User';

    public $model = "App\Models\User";

    public function create()
    {
        // Генерим пароль
        $this->fields['fields']['password']['value'] = Str::random(8);

        return parent::create();
    }

    public function store()
    {
        // добавляем валидацию
        $this->fields['fields']['password']['validate'] .= "|required";

        return parent::store();
    }

    public function edit($id)
    {
        $this->post = $this->post->findOrFail($id);

        // Убираем поле отправки на email. И очищаем хэш пароля
        unset($this->fields['fields']['send_mail']);
        $this->post->password = '';
        return parent::edit($id);
    }

    public function update($id)
    {
        $this->post = $this->post->findOrFail($id);

        // Для unique валидации добавляем id в исключение
        $this->fields['fields']['email']['validate'] .= "," . $this->post->id;

        // Если пароль не был задан, оставляем тот что бы ранее
        if (Request::input('fields')['password'] == '') {
            $this->fields['fields']['password']['field-save'] = 'none';
        }

        return parent::update($id);
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
    protected function indexLinks($post, $urlPostfix) {
        $res = parent::indexLinks($post, $urlPostfix);

        if ($post['user_role_id'] != 0) {
            $res['user-role'] = action('\Backend\User\Controllers\RoleController@edit', $post['user_role_id']);
        }

        return $res;
    }

    protected function resourceCombine($type)
    {
        if (array_search($type, ['store', 'update', 'edit', 'create', 'index']) !== false) {
            foreach (UserRole::orderBy('sort_num', 'desc')->get() as $role) {
                array_unshift($this->fields['fields']['user_role_id']['options'], [
                    'label' => $role->name, 'value' => $role->id
                ]);
            }
        }
    }

    // Криптуем пароль и отправляем email
    protected function preSaveData($type)
    {

        $fields = Request::input('fields', []);

        if ($type == 'store') {
            if (isset($fields['send_mail']) && $fields['send_mail'] == 'yes') {

                Mail::to($this->post['email'])
                    ->send(new \Backend\User\Mail\UserMail($this->post));

            }
        }

        // Криптуем пароль
        if ($fields['password'] != '') $this->post['password'] = bcrypt($fields['password']);
    }

    // todo Зделать запрет на удаление
}
