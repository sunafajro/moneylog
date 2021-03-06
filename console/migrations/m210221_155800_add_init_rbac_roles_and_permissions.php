<?php

use common\rbac\Permission;
use yii\db\Migration;

/**
 * Class m210221_155800_add_init_rbac_roles_and_permissions
 *
 * @property array $permissions
 */
class m210221_155800_add_init_rbac_roles_and_permissions extends Migration
{
    public $permissions = [
        Permission::PERMISSION_USER_VIEW_ANY,
        Permission::PERMISSION_USER_CREATE_NEW,
        Permission::PERMISSION_USER_UPDATE_ANY,
        Permission::PERMISSION_USER_DELETE_ANY,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->createRole(Permission::ROLE_ADMIN);
        $admin->description = Permission::getDescription($admin->name);
        $auth->add($admin);


        foreach ($this->permissions as $permission) {
            $item = $auth->createPermission($permission);
            $item->description = Permission::getDescription($item->name);
            $auth->add($item);
            $auth->addChild($admin, $item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $items = [];
        foreach ($this->permissions as $permission) {
            $items[] = $auth->getPermission($permission);
        }
        $items[] = $auth->getRole(Permission::ROLE_ADMIN);
        foreach ($items as $item) {
            $auth->remove($item);
        }
    }
}