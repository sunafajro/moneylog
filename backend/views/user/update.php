<?php

/**
 * @var View $this
 * @var UserForm $model
 */

use common\models\forms\UserForm;
use yii\helpers\Html;
use yii\web\View;

$this->title = 'Изменить пользователя: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить пользователя';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
