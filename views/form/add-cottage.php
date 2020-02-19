<?php

use app\models\database\Cottage;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this View */
/* @var $matrix Cottage */


$form = ActiveForm::begin(['id' => 'addCottage', 'options' => ['class' => 'form-horizontal bg-default'], 'enableAjaxValidation' => false, 'validateOnSubmit' => false, 'action' => ['/form/cottage-add']]);


echo $form->field($matrix, 'num', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput();
echo $form->field($matrix, 'square', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput();
echo $form->field($matrix, 'membership', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput();
echo $form->field($matrix, 'rigths', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput();
echo $form->field($matrix, 'description', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textarea();

echo "<div class='clearfix'></div>";
echo Html::submitButton('Сохранить', ['class' => 'btn btn-success   ', 'id' => 'addSubmit', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-html' => 'true',]);
ActiveForm::end();
