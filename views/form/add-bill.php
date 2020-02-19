<?php

use app\models\database\Bill;
use app\models\database\Payer;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this View */
/* @var $matrix Bill */

$form = ActiveForm::begin(['id' => 'addBill', 'options' => ['class' => 'form-horizontal bg-default'], 'enableAjaxValidation' => false, 'validateOnSubmit' => false, 'action' => ['/form/bill-add']]);

echo $form->field($matrix, 'cottage', ['template' => "{input}"])->hiddenInput()->label(false);

echo $form->field($matrix, 'cottage', ['template' => "{input}"])->hiddenInput()->label(false);

echo $form->field($matrix, 'payer', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])->dropdownList(
    Payer::find()->select(['fio', 'id'])->indexBy('id')->where(['cottage' => $matrix->cottage])->column()
);

echo $form->field($matrix, 'service_name', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])->dropdownList(
    ['membership' => 'Членские взносы', 'power' => 'Электроэнергия', 'target' => 'Целевые взносы'],
    ['prompt'=>'Выберите тип платежа']
);

echo $form->field($matrix, 'bill_destination', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput()
    ->hint('Тут подробности платежа- период, данные');

echo $form->field($matrix, 'amount', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput(['placeholder' => '2012,13'])
    ->hint('Сумма платежа, с копейками, разделитель- точка или запятая, например 2012,13');


echo "<div class='clearfix'></div>";
echo Html::submitButton('Сохранить', ['class' => 'btn btn-success   ', 'id' => 'addSubmit', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-html' => 'true',]);
ActiveForm::end();