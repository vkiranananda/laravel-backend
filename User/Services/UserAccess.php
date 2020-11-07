<?php


namespace Backend\Root\User\Services;


use Auth;
use Backend\Root\User\Models\UserRole;
use Helpers;

class UserAccess
{

    private static function getRole()
    {
        static $role = false;

        // Получаем роль текущего пользователя если еще не получена
        if (!$role) $role = Helpers::getDataField(UserRole::findOrFail(Auth::user()->user_role_id), 'permissions', []);

        return $role;
    }

    /**
     * Проверяем права доступа
     * @param $access - тип доступа edit-all, edit-owner, read-all, read-owner, create, destroy-all, destroy-owner
     * @param $modKey - Ключ модуля по которому будем сверть с ролью
     * @param false $userId - Если указан будет учавствовать в типах read-owner, edit-owner, delete-owner,
     * если не указан вернет true
     * @return bool - Вернет true или false.
     */
    public static function checkAccess($access, $modKey, $userId = false)
    {
        $role = UserAccess::getRole();

        if (!isset($role[$modKey])) return false;

        $perm = $role[$modKey];


        $checkOwner = function ($key) use ($userId, $perm) {
            // Если не установлена запрещаем доступ
            if (!isset($perm[$key])) return false;

            // Разрешены все записи, разрешаем без дальнейших проверок
            if ($perm[$key] == 'all') return true;
            // Если тип owner
            // Если $userId не указан, считаем что в модуле он не используется и права owner приравниваются к all.
            // Иначе проверяем владельца записиси с текущим пользователем.
            if ($perm[$key] == 'owner' && (!$userId || $userId == '' || $userId == Auth::user()->id)) return true;
            // По умолчанию запрет
            return false;
        };

        $checkAll = fn($key) => (isset($perm[$key]) && $perm[$key] == 'all');

        switch ($access) {
            case 'create':
                return $checkAll('create');
            case 'read-all':
                return $checkAll('read');
            case 'edit-all':
                return $checkAll('edit');
            case 'destroy-all':
                return $checkAll('destroy');
            case 'read-owner':
                return $checkOwner('read');
            case 'edit-owner':
                return $checkOwner('edit');
            case 'destroy-owner':
                return $checkOwner('destroy');
            default:
                return false;
        }
    }
}
