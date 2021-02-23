<?php

namespace common\models\queries;

use common\models\User;

/**
 * Class UserQuery
 * @package common\queries
 *
 * @method User one($db = null)
 * @method User[] all($db = null)
 */
class UserQuery extends \yii\db\ActiveQuery
{
    /**
     * @param int $id
     * @return UserQuery
     */
    public function byId(int $id)
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $id]);
    }

    /**
     * @param array $ids
     * @return UserQuery
     */
    public function byIds(array $ids)
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $ids]);
    }

    /**
     * @param array $ids
     * @return UserQuery
     */
    public function byActive(): UserQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(['not', ["{$tableName}.status" => User::STATUS_DELETED]]);
    }
}