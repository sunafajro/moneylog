<?php

use common\rbac\Permission;
use yii\db\Migration;

/**
 * Class m210221_155800_add_init_rbac_roles_and_permissions
 */
class m210221_155800_add_init_rbac_roles_and_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->createRole(Permission::ROLE_ADMIN);
        $admin->description = Permission::getDescription($admin->name);
        $auth->add($admin);

        $permissions = [
            Permission::PERMISSION_USER_VIEW_ANY,
            Permission::PERMISSION_USER_CREATE_NEW,
            Permission::PERMISSION_USER_UPDATE_ANY,
            Permission::PERMISSION_USER_DELETE_ANY,
        ];
        foreach ($permissions as $permission) {
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

        $auth->removeAll();
    }
}