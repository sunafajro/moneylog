<?php


namespace console\controllers;

use common\models\User;
use common\rbac\Permission;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class SeedController
 * @package console\controllers
 */
class SeedController extends Controller
{
    /**
     * @param string $username
     * @param string $email
     * @param string $roleName
     * @param string $password
     *
     * @return int
     */
    public function actionCreateUser(
        string $username = 'admin',
        string $email = 'admin@site.local',
        string $roleName = Permission::ROLE_ADMIN,
        string $password = '12345'
    ): int
    {
        try {
            // добавление нового пользователя
            $user = new User([
                'username' => $username,
                'email' => $email,
            ]);
            $user->setPassword($password);
            if (!$user->save()) {
                throw new \Exception('Не удалось создать пользователя.');
            }

            // Присвоение роли
            $auth = \Yii::$app->authManager;
            $role = $auth->getRole($roleName);
            $auth->assign($role, $user->id);

            Console::output('Пользователь успешно создан.');
            return 0;
        } catch (\Exception $e) {
            Console::output("Error. " . $e->getMessage());
            return 1;
        }
    }
}