<?php
namespace common\models;

use common\models\queries\UserQuery;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\rbac\Role;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%users}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'integer'],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            [['username', 'email', 'password_hash', 'password_reset_token', 'verification_token', 'auth_key'], 'string'],
            [['username', 'email'], 'unique'],
            [['username', 'email'], 'required'],
        ];
    }

    /**
     * @return UserQuery
     */
    public static function find(): UserQuery
    {
        return new UserQuery(get_called_class(), []);
    }

    /**
     * @param $id
     * @return User|null
     */
    public static function findIdentity($id): ?User
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @param string $value
     * @return User|null
     */
    public static function findByUsername(string $value): ?User
    {
        return static::findOne(['username' => $value, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @param string $value
     * @return User|null
     */
    public static function findByEmail(string $value): ?User
    {
        return static::findOne(['email' => $value, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @param string $value
     * @return User|null
     */
    public static function findByUsernameOrEmail(string $value): ?User
    {
        return static::findByUsername($value) ?: static::findByEmail($value);
    }

    /**
     * @param string $token password reset token
     * @return User|null
     */
    public static function findByPasswordResetToken(string $token): ?User
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * @param string $token verify email token
     * @return User|null
     */
    public static function findByVerificationToken(string $token): ?User
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @param string $password
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @param string $password
     * @throws \yii\base\Exception
     */
    public function setPassword(string $password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @throws \yii\base\Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @throws \yii\base\Exception
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * @throws \yii\base\Exception
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => 'Имя пользователя',
            'email' => 'E-mail',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert) {
            try {
                $this->generatePasswordResetToken();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(): bool
    {
        // запрещаем удалять самого себя
        if (\Yii::$app->user->identity->id !== $this->id) {
            $this->status = static::STATUS_DELETED;
            $this->deleted_at = time();
            return $this->save('true', ['status', 'updated_at', 'deleted_at']);
        }
        return false;
    }

    /**
     * @return string[]
     */
    public static function getStatusLabels(): array
    {
        return [
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_INACTIVE => 'Не активен',
            self::STATUS_DELETED => 'Удален',
        ];
    }

    /**
     * @param string $value
     * @return string
     */
    public static function getStatusLabel(string $value): string
    {
        $statuses = self::getStatusLabels();
        return $statuses[$value] ?? '';
    }

    /**
     * @return Role|null
     */
    public function getRole(): ?Role
    {
        $auth = \Yii::$app->authManager;
        $roles = $auth->getRolesByUser($this->id);
        if (!empty($roles)) {
            $role = reset($roles);
            return !empty($role) ? $role : null;
        }

        return null;
    }
}
