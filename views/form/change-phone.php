<?php

use app\models\database\Cottage;
use app\models\database\Phone;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this View */
/* @var $matrix Phone */

$existentPhones = Phone::getCottagePhones(Cottage::findOne($matrix->cottage_num));

if(!empty($existentPhones)){
    echo "<h4>Список существующих номеров телефонов</h4><ul>";
    foreach ($existentPhones as $existentPhone) {
        echo "<li>{$existentPhone->phone} : {$existentPhone->fio}</li>";
    }
    echo '</ul>';
}

$form = ActiveForm::begin(['id' => 'changePhone>', 'options' => ['class' => 'form-horizontal bg-default'], 'enableAjaxValidation' => false, 'validateOnSubmit' => false, 'action' => ['/form/phone-change/' . $matrix->id]]);


echo $form->field($matrix, 'id', ['template' => "{input}"])->hiddenInput()->label(false);
echo $form->field($matrix, 'cottage_num', ['template' => "{input}"])->hiddenInput()->label(false);

echo $form->field($matrix, 'fio', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput();
echo $form->field($matrix, 'phone', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput();

echo "<div class='clearfix'></div>";
echo Html::submitButton('Сохранить', ['class' => 'btn btn-success   ', 'id' => 'addSubmit', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-html' => 'true',]);
ActiveForm::end();