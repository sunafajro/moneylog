<?php

use common\rbac\Permission;
use yii\db\Migration;

/**
 * Class m210223_155300_add_rbac_use_role_and_permissions
 *
 * @property array $permissions
 */
class m210223_155300_add_rbac_use_role_and_permissions extends Migration
{
    /** @var array */
    public $permissions = [

    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $user = $auth->createRole(Permission::ROLE_USER);
        $user->description = Permission::getDescription($user->name);
        $auth->add($user);

        foreach ($this->permissions as $permission) {
            $item = $auth->createPermission($permission);
            $item->description = Permission::getDescription($item->name);
            $auth->add($item);
            $auth->addChild($user, $item);
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
        $items[] = $auth->getRole(Permission::ROLE_USER);
        foreach ($items as $item) {
            $auth->remove($item);
        }
    }
}