<?php

use app\assets\MassMembershipAsset;
use app\models\MassMembership;
use nirvana\showloading\ShowLoadingAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = 'Членские: выставление счетов';
/* @var $this View */
/* @var $model MassMembership */

MassMembershipAsset::register($this);
ShowLoadingAsset::register($this);

$form = ActiveForm::begin(['id' => 'addMassMembership', 'options' => ['class' => 'form-horizontal bg-default'], 'enableAjaxValidation' => false, 'validateOnSubmit' => false, 'action' => [Url::toRoute(['site/mass-bill', 'type' => 'membership'])]]);

echo $form->field($model, 'quarter', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput();
echo $form->field($model, 'amount', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput();

echo "<div class=\"form-group\">
        <div class=\"col-lg-12\">
            " . Html::submitButton('Создать', ['class' => 'btn btn-primary']) . "
        </div>
    </div>";

ActiveForm::end();


