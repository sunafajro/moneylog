<?php

/**
 * @var View $this
 * @var UserForm $model
 * @var ActiveForm $form
 */

use common\models\forms\UserForm;
use common\models\User;
use common\rbac\Permission;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'role')->dropDownList(Permission::getRoles()) ?>

    <?= $form->field($model, 'status')->dropDownList(User::getStatusLabels()) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
