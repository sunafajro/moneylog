<?php

namespace common\rbac;

/**
 * Class Permission
 * @package common\rbac
 *
 * @property-read array $descriptions
 */
class Permission
{
    const PERMISSION_USER_VIEW_ANY = 'user_view_any';
    const PERMISSION_USER_CREATE_NEW = 'user_create_new';
    const PERMISSION_USER_UPDATE_ANY = 'user_update_any';
    const PERMISSION_USER_DELETE_ANY = 'user_delete_any';

    const ROLE_ADMIN = 'admin';

    public static function getDescriptions(): array
    {
        return [
            self::ROLE_ADMIN => 'Администратор системы',
            self::PERMISSION_USER_VIEW_ANY => 'Просмотр любого пользователя',
            self::PERMISSION_USER_CREATE_NEW => 'Создание нового пользователя',
            self::PERMISSION_USER_UPDATE_ANY => 'Изменение любого пользователя',
            self::PERMISSION_USER_DELETE_ANY => 'Удаление любого пользователя',
        ];
    }

    /**
     * @param string $key
     * @return string
     */
    public static function getDescription(string $key): string
    {
        $descriptions = self::getDescriptions();
        return $descriptions[$key] ?? '';
    }
}