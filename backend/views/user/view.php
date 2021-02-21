<?php

/**
 * @var View $this
 * @var User $model
 */

use common\models\User;
use common\rbac\Permission;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="p-1">
        <?php
            if (\Yii::$app->user->can(Permission::PERMISSION_USER_UPDATE_ANY)) {
                echo Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
            }
        if (\Yii::$app->user->can(Permission::PERMISSION_USER_DELETE_ANY)) {
            echo Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы действительно хотите удалить пользователя?',
                    'method' => 'post',
                ],
            ]);
        } ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'email:email',
            'status' => [
                'attribute' => 'status',
                'value' => function(User $user) {
                    return User::getStatusLabel($user->status);
                },
            ],
            'created_at' => [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d.m.Y'],
            ],
            'updated_at' => [
                'attribute' => 'updated_at',
                'format' => ['date', 'php:d.m.Y'],
            ],
        ],
    ]) ?>

</div>
