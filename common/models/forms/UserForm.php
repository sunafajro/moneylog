<?php

namespace common\models\forms;

use common\models\User;
use yii\base\Model;

/**
 * Class UserForm
 * @package common\models\forms
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $role
 * @property int $status
 */
class UserForm extends Model
{
    /** @var int */
    public $id;
    /** @var string */
    public $username;
    /** @var string */
    public $email;
    /** @var string */
    public $password;
    /** @var string */
    public $role;
    /** @var int */
    public $status;

    /** @var User|null */
    private $_model;

    /**
     * {@inheritDoc}
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->_model = new User();
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['username', 'email', 'password', 'role'], 'string'],
            [['email'], 'email'],
            [['status'], 'integer'],
            [['username', 'email', 'role', 'status'], 'required'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return array_merge(
            $this->_model->attributeLabels(),
            ['password' => 'Пароль', 'role' => 'Роль']
        );
    }

    /**
     * @param User $user
     * @return UserForm
     */
    public static function loadFromModel(User $user): UserForm
    {
        $form = new self();
        if (!$user->isNewRecord) {
            $form->id = $user->id;
        }
        $form->setAttributes(array_filter($user->getAttributes(), function($value, $key) {
            return in_array($key, ['username', 'email', 'status']);
        }, ARRAY_FILTER_USE_BOTH));

        if (!$user->isNewRecord) {
            $role = $user->getRole();
            $form->role = $role->name ?? null;
        }
        $form->_model = $user;

        return $form;
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function save(): bool
    {
        $attributes = $this->_model->isNewRecord ? null : ['username', 'email', 'status', 'updated_at'];
        if (!$this->validate()) {
            return false;
        }
        $this->_model->setAttributes(array_filter($this->getAttributes(), function($value, $key) {
            return in_array($key, ['username', 'email', 'status']);
        }, ARRAY_FILTER_USE_BOTH));
        if (!empty($this->password)) {
            $this->_model->setPassword($this->password);
            if (!$this->_model->isNewRecord) {
                $attributes[] = 'password_hash';
            }
        }
        if (!$this->_model->save(true, $attributes)) {
            $this->addErrors($this->_model->getErrors());

            return false;
        } else {
            $this->id = $this->_model->id;
            $auth = \Yii::$app->authManager;
            $role = $auth->getRole($this->role);
            $auth->revokeAll($this->id);
            $auth->assign($role, $this->id);

            return true;
        }
    }
}