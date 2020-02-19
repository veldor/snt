<?php

use app\models\database\Payer;
use app\models\PowerBill;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this View */
/* @var $matrix PowerBill */


$form = ActiveForm::begin(['id' => 'addPowerBill', 'options' => ['class' => 'form-horizontal bg-default'], 'enableAjaxValidation' => false, 'validateOnSubmit' => false, 'action' => ['/form/power-bill-add']]);

echo $form->field($matrix, 'cottageId', ['template' => "{input}"])->hiddenInput()->label(false);

echo $form->field($matrix, 'payer', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])->dropdownList(
    Payer::find()->select(['fio', 'id'])->indexBy('id')->where(['cottage' => $matrix->cottageId])->column()
);


echo $form->field($matrix, 'period', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput();

echo $form->field($matrix, 'oldData', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput(['type' => 'number']);

echo $form->field($matrix, 'newData', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput(['type' => 'number']);
echo $form->field($matrix, 'no_limit', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->checkbox();

echo $form->field($matrix, 'limit', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput(['readonly' => true]);
echo $form->field($matrix, 'cost', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput(['readonly' => true]);
echo $form->field($matrix, 'overcost', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput(['readonly' => true]);
echo $form->field($matrix, 'countedSumm', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput(['readonly' => true]);


echo "<div class='clearfix'></div>";
echo Html::submitButton('Сохранить', ['class' => 'btn btn-success   ', 'id' => 'addSubmit', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-html' => 'true',]);
ActiveForm::end();
?>
<script>
    $(function () {
        function roundRubles(num) {
            return Math.round(num * 100) / 100;
        }

        let oldDataInput = $('input#powerbill-olddata');
        let newDataInput = $('input#powerbill-newdata');
        let ignoreLimitCheckbox = $('input#powerbill-no_limit');

        // static fields
        let limit = $('input#powerbill-limit').val();
        let cost = parseFloat($('input#powerbill-cost').val());
        let overcost = parseFloat($('input#powerbill-overcost').val());

        let inputForResult = $('input#powerbill-countedsumm');
        let hint = inputForResult.parent().find('div.help-block');

        function countCost(){
            let oldData = oldDataInput.val();
            let newData = newDataInput.val();
            if(oldData && newData && oldData < newData){
                // посчитаю разницу
                let difference = newData - oldData;
                let finishCost;
                if(ignoreLimitCheckbox.prop('checked')){
                    finishCost = roundRubles(difference * overcost);
                    hint.html("<span class='text-info'>" + difference + " квт.ч </span> * <span class='text-danger'>" + overcost + " Р</span> = <span class='text-success'>" + finishCost + " Р</span>");
                }
                else{
                    if(difference > limit){
                        let overLimit = difference - limit;
                        finishCost = roundRubles(limit * cost + overLimit * overcost);
                        hint.html("<span class='text-info'>" + limit + " квт.ч </span> * <span class='text-danger'>" + cost + " Р</span> + <span class='text-info'>" + overLimit + " квт.ч </span> * <span class='text-danger'>" + overcost + " = <span class='text-success'>" + finishCost + " Р</span>");
                    }
                    else{
                        finishCost = roundRubles(difference * cost);
                        hint.html("<span class='text-info'>" + difference + " квт.ч </span> * <span class='text-danger'>" + cost + " Р</span> = <span class='text-success'>" + finishCost + " Р</span>");
                    }
                }
                inputForResult.val(finishCost);
            }
        }

        oldDataInput.on('input.count', function () {
            countCost();
        });
        newDataInput.on('input.count', function () {
            countCost();
        });
        ignoreLimitCheckbox.on('change.count', function () {
            countCost();
        });
    });
</script>