<?php



/* @var $this View */

use app\assets\MassMembershipAsset;
use app\models\MassTarget;
use nirvana\showloading\ShowLoadingAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = 'Целевые: выставление счетов';

/* @var $model MassTarget */


MassMembershipAsset::register($this);
ShowLoadingAsset::register($this);

$form = ActiveForm::begin(['id' => 'addMassTarget', 'options' => ['class' => 'form-horizontal bg-default'], 'enableAjaxValidation' => false, 'validateOnSubmit' => false, 'action' => [Url::toRoute(['site/mass-bill', 'type' => 'target'])]]);

echo $form->field($model, 'year', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput();
echo $form->field($model, 'amount', ['template' =>
    '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->textInput();

echo '<div class="form-group">
        <div class="col-lg-12">
            ' . Html::submitButton('Создать', ['class' => 'btn btn-primary']) . "
        </div>
    </div>";

ActiveForm::end();