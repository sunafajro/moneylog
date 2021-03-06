<?php

/**
 * @var $this View
 * @var $content string
 */

use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\bootstrap4\Breadcrumbs;
use yii\web\View;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
        NavBar::begin([
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-expand-lg  navbar-dark bg-dark'
            ],
        ]);
        $menuItems = [
            ['label' => 'Home', 'url' => ['site/index']],
            ['label' => 'About', 'url' => ['site/about']],
            ['label' => 'Contact', 'url' => ['site/contact']],
        ];
        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Signup', 'url' => ['site/signup']];
            $menuItems[] = ['label' => 'Login', 'url' => ['site/login']];
        } else {
            $menuItems[] =  Html::tag('li',
                Html::a(
                    'Выход (' . Yii::$app->user->identity->username . ')',
                    ['site/logout'],
                    ['class' => 'nav-link', 'data-method' => 'POST']
                ));
        }
        echo Nav::widget([
            'options' => ['class' => 'nav navbar-nav ml-auto'],
            'items' => $menuItems,
        ]);
        NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="float-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
        <p class="float-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
