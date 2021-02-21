<?php

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 */

use common\helpers\IconHelper;
use common\models\User;
use common\rbac\Permission;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;

$actionTemplate = ['{view}'];
if (\Yii::$app->user->can(Permission::PERMISSION_USER_UPDATE_ANY)) {
    $actionTemplate[] = '{update}';
}
if (\Yii::$app->user->can(Permission::PERMISSION_USER_DELETE_ANY)) {
    $actionTemplate[] = '{delete}';
}
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="pt-1 pb-1">
        <?php if (\Yii::$app->user->can(Permission::PERMISSION_USER_UPDATE_ANY)) {
            echo Html::a('Создать пользователя', ['create'], ['class' => 'btn btn-success']);
        } ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

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
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Действия',
                'template' => join(' ', $actionTemplate),
                'buttons' => [
                    'view' =>  function($url, $model) {
                        return Html::a(
                            IconHelper::icon('eye'),
                            $url,
                            ['title' => 'Просмотреть']
                        );
                    },
                    'update' =>  function($url, $model) {
                        return Html::a(
                            IconHelper::icon('edit'),
                            $url,
                            ['title' => 'Изменить']
                        );
                    },
                    'delete' => function($url, $model) {
                        return Html::a(
                            Html::tag('i', '', ['class' => 'fas fa-trash', 'aria-hidden' => 'true']),
                            $url,
                            ['title' => 'Удалить', 'data-method' => 'post']
                        );
                    }
                ],
            ],
        ],
    ]); ?>


</div>
