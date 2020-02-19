<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\models\database\MailingSchedule;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

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
        'brandLabel' => "Линда",
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    try {
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [
                '<li><div id="goToCottageContainer"><label class="hidden" for="goToCottageInput"></label><div class="input-group">
                    <span class="input-group-btn">
                        <a class="btn btn-default" href="' . Url::toRoute(['site/previous']) . '">
                            <span class="glyphicon glyphicon-backward"></span>
                        </a>
                    </span>
                     <input
                    type="text"
                    id="goToCottageInput"
                    class="form-control">
                    <span
                    class="input-group-btn"><a class="btn btn-default"  href="' . Url::toRoute(['site/next']) . '"><span
                            class="glyphicon glyphicon-forward"></span></a></span>
        </div></div></li>',
                ['label' => 'Список участков', 'url' => ['/site/index']],
                ['label' => 'Рассылка', 'url' => ['/site/mailing']],
                ['label' => 'История', 'url' => ['/site/history']],
                ['label' =>  ' Очередь рассылки ' . "<span id='unsendedMessagesBadge' class='badge'> " . MailingSchedule::countWaiting() . "</span>" , 'url' => ['/site/mailing-schedule'], 'encode' => false],
                ['label' => 'Настройки', 'url' => ['/settings/index']],
            ],
        ]);
    } catch (Exception $e) {}
    NavBar::end();
    ?>

    <div id="alertsContentDiv"></div>
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

    <!--<footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; My Company <?/*= date('Y') */?></p>

            <p class="pull-right"><?/*= Yii::powered() */?></p>
        </div>
    </footer>-->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
