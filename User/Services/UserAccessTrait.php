<?php


namespace Backend\Root\User\Services;


trait UserAccessTrait
{
    /**
     * Функция заглушка для перегрузки на проверку прав доступа.
     * @param $access - тип доступа edit-all, edit-owner, read-all, read-owner, create, destroy-all, destroy-owner
     * @param $userId - Если указан будет учавствовать в типах read-own, edit-own, delete-own, если не указан
     * вернет true если разрешена хоть какая то запись.
     * @param $accessKey - Если нужно переопределить ключ
     * @return bool - Вернет true или false в зависимости от типа запроса.
     */
    protected function getUserAccess($access, $userId = false, $accessKey = false)
    {
        return UserAccess::checkAccess($access, $accessKey ? $accessKey : $this->userAccessKey, $userId);
    }
}
