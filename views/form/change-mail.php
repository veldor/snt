<?php

use app\models\database\Cottage;
use app\models\database\Mail;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this View */
/* @var $matrix Mail */



$existentMails = Mail::getCottageMails(Cottage::findOne($matrix->cottage_num));

if(!empty($existentMails)){
    echo '<h4>Список существующих адресов электронной почты</h4><ul>';
    foreach ($existentMails as $existentMail) {
        echo "<li>{$existentMail->email} : {$existentMail->fio}</li>";
    }
    echo '</ul>';
}

$form = ActiveForm::begin(['id' => 'changeMail>', 'options' => ['class' => 'form-horizontal bg-default'], 'enableAjaxValidation' => false, 'validateOnSubmit' => false, 'action' => ['/form/mail-change/' . $matrix->id]]);


echo $form->field($matrix, 'id', ['template' => '{input}'])->hiddenInput()->label(false);
echo $form->field($matrix, 'cottage_num', ['template' => '{input}'])->hiddenInput()->label(false);

echo $form->field($matrix, 'fio', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput();
echo $form->field($matrix, 'email', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput(['type' => 'email']);

echo "<div class='clearfix'></div>";
echo Html::submitButton('Сохранить', ['class' => 'btn btn-success   ', 'id' => 'addSubmit', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-html' => 'true',]);
ActiveForm::end();
