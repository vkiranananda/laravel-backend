<?php


namespace Backend\Root\User\Controllers;


use Backend\Root\Form\Controllers\ResourceController;
use GetConfig;

class RoleController extends ResourceController
{
    protected $configPath = "User::config-role";
    protected $fieldsPath = "User::fields-role";

    // Добавляем кнопку пользователи
    protected function indexListMenu($urlPostfix = '')
    {
        $res = parent::indexListMenu($urlPostfix);

        array_splice($res, 1, 0, [[
            'label' => 'Пользователи',
            'url' => action('\Backend\User\Controllers\UserController@index') . $urlPostfix,
            // Тип кнопка как в бутстрап
            'btn-type' => 'success'
        ]]);

        return $res;
    }

    // write-all, write-own, read-all, read-own
    // true false
    // getUserAccess($access, $userId)


    // return all (запрос на общие права без указания user_id), own (запрос на общие права без указания user_id),
    // true (запрос для конкретной записи с указанием user_id), false (запрет)
    protected function resourceCombine($type)
    {
        if (array_search($type, ['store', 'update', 'edit', 'create']) !== false) {
            foreach (GetConfig::backend('User::role-modules') as $mod) {
                $clone = $this->fields['fields-for-clone'];

                if (isset($mod['type'])) {
                    if ($mod['type'] == 'only-read') {
                        unset($clone['fields']['create'], $clone['fields']['edit'], $clone['fields']['destroy'], $clone['fields']['read']['options']['owner']);
                    }
                }

                $clone['name'] = $mod['mod-key'];
                $clone['fields']['title']['html'] = "<hr><b>" . $mod['label'] . "</b><hr>";
                $this->fields['fields']['permissions']['fields'][$mod['mod-key']] = $clone;
            }
        }
    }
}
