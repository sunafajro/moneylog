<?php


namespace console\controllers;

use common\models\User;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class SeedController
 * @package console\controllers
 */
class SeedController extends Controller
{
    /**
     * @param $username
     * @param $email
     * @param $password
     *
     * @return bool
     */
    public function actionCreateUser($username, $email, $password): bool
    {
        try {
            $user = User::findByUsername($username);
            if (!empty($user)) {
                throw new \Exception("Пользователь с таким username уже существует.");
            }
            $user = User::findByEmail($email);
            if (!empty($user)) {
                throw new \Exception("Пользователь с таким email уже существует.");
            }
            $user = new User([
                'username' => $username,
                'email' => $email,
            ]);
            $user->setPassword($password);
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                throw new \Exception('Не удалось создать пользователя.');
            }
            Console::output('Пользователь успешно создан.');
            return 0;
        } catch (\Exception $e) {
            Console::output("Error. " . $e->getMessage());
            return 1;
        }
    }
}