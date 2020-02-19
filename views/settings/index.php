<?php

use app\assets\SettingsAsset;
use app\models\Invoice;
use app\models\MailSettings;
use app\models\PowerBill;
use mihaildev\ckeditor\CKEditor;
use nirvana\showloading\ShowLoadingAsset;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $bankInfo Invoice */
/* @var $mailSettings MailSettings */
/* @var $mailTemplate string */
/* @var $billMailTemplate string */
/* @var $powerSettings PowerBill */

SettingsAsset::register($this);
ShowLoadingAsset::register($this);

$this->title = 'Настройки';

?>


<ul class="nav nav-tabs">
    <li id="bank_set_li" class="active"><a href="#bank_set" data-toggle="tab" class="active">Настройки банка</a></li>
    <li><a href="#email_set" data-toggle="tab">Настройки почты</a></li>
    <li><a href="#email_template" data-toggle="tab">Стандартный шаблон письма</a></li>
    <li><a href="#bill_email_template" data-toggle="tab">Шаблон сообщения о выставленном счёте</a></li>
    <li><a href="#power_settings" data-toggle="tab">Настройки электроэнергии</a></li>
    <li><a href="#misc_settings" data-toggle="tab">Разные настройки</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane active" id="bank_set">
        <?php
        $form = ActiveForm::begin([
            'id' => 'bank-settings-form',
            'options' => ['class' => 'form'],
            'enableAjaxValidation' => false,
            'validateOnSubmit' => false
        ]);
        ?>
        <?= $form->field($bankInfo, 'st') ?>
        <?= $form->field($bankInfo, 'snt_name') ?>
        <?= $form->field($bankInfo, 'personalAcc') ?>
        <?= $form->field($bankInfo, 'bankName') ?>
        <?= $form->field($bankInfo, 'bik') ?>
        <?= $form->field($bankInfo, 'correspAcc') ?>
        <?= $form->field($bankInfo, 'payerInn') ?>
        <?= $form->field($bankInfo, 'kpp') ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php
        ActiveForm::end();
        ?>
    </div>
    <div class="tab-pane" id="email_set">
        <?php
        $form = ActiveForm::begin([
            'id' => 'mail-settings-form',
            'options' => ['class' => 'form'],
            'enableAjaxValidation' => false,
            'validateOnSubmit' => false
        ]);

        echo $form->field($mailSettings, 'address', ['template' =>
            '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput()
            ->hint('Введите адрес почты, с коротого будет осуществляться отправка почты');

        echo $form->field($mailSettings, 'user_name', ['template' =>
            '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput()
            ->hint('Введите имя пользователя почты, с коротой будет осуществляться отправка почты');

        echo $form->field($mailSettings, 'user_pass', ['template' =>
            '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->passwordInput()
            ->hint('Введите пароль почты, с коротой будет осуществляться отправка почты');

        echo $form->field($mailSettings, 'snt_name', ['template' =>
            '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput()
            ->hint('Введите название СНТ, которое будет отображаться в заголовке письма');
        echo $form->field($mailSettings, 'is_test', ['template' =>
            '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->checkbox()
            ->hint('Если активно- почта будет отправляться на указанный ниже адрес вместо отправки реальным получателям');
        echo $form->field($mailSettings, 'test_mail', ['template' =>
            '<div class="col-sm-4">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput()
            ->hint('Введите адрес почты, на который почта будет отправляться в тестовом режиме');
        ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php
        ActiveForm::end();
        ?>
    </div>
    <div class="tab-pane" id="email_template">
        <div class="row">

            <div class="col-sm-12 margin">
                <label for="mailTemplateBody"></label><textarea title="mail template text" id="mailingBody"
                                                                name="mailTemplateBody"><?= $mailTemplate ?></textarea>
                <?php
                try {
                    CKEditor::widget([
                        'name' => 'mailing',
                        'editorOptions' => [
                            'preset' => 'full', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
                            //'extraPlugins' => 'lexemes',
                        ],
                        'options' => ['id' => 'mailTemplateBody']
                    ]);
                } catch (Exception $e) {
                }
                ?>
            </div>
            <div class="col-sm-12 text-center">
                <button id="saveMailTemplateBtn" class="btn btn-default"><span
                            class="text-success">Сохранить шаблон</span></button>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="bill_email_template">
        <div class="row">
            <div class="col-sm-12 margin">
                <label for="billMailTemplateBody"></label><textarea title="mail template text" id="billMailTemplateBody"
                                                                    name="billMailTemplateBody"><?= $billMailTemplate ?></textarea>
                <?php
                try {
                    CKEditor::widget([
                        'name' => 'mailing',
                        'editorOptions' => [
                            'preset' => 'full', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
                            //'extraPlugins' => 'lexemes',
                        ],
                        'options' => ['id' => 'billMailTemplateBody']
                    ]);
                } catch (Exception $e) {
                }
                ?>
            </div>
            <div class="col-sm-12 text-center">
                <button id="saveBillMailTemplateBtn" class="btn btn-default"><span
                            class="text-success">Сохранить шаблон</span></button>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="power_settings">
        <div class="row">
            <?php
            $form = ActiveForm::begin([
                'id' => 'power-settings-form',
                'options' => ['class' => 'form'],
                'enableAjaxValidation' => false,
                'validateOnSubmit' => false
            ]);

            echo $form->field($powerSettings, 'limit', ['template' =>
                '<div class="col-sm-4">{label}</div><div class="col-sm-8"><div class="input-group">{input}<span class="input-group-addon">Квт.ч</span></div>{error}{hint}</div>'])
                ->textInput()
                ->hint('Льготный лимит,  киловатт*часах');

            echo $form->field($powerSettings, 'cost', ['template' =>
                '<div class="col-sm-4">{label}</div><div class="col-sm-8"><div class="input-group">{input}<span class="input-group-addon">Руб.</span></div>{error}{hint}</div>'])
                ->textInput()
                ->hint('В рублях');

            echo $form->field($powerSettings, 'overcost', ['template' =>
                '<div class="col-sm-4">{label}</div><div class="col-sm-8"><div class="input-group">{input}<span class="input-group-addon">Руб.</span></div>{error}{hint}</div>'])
                ->textInput()
                ->hint('В рублях');
            echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
            ActiveForm::end();
            ?>
        </div>
    </div>
    <div class="tab-pane" id="power_settings">
        <div class="row">
           <div class="btn-group">
               <button id="doUpdate" class="btn btn-default"><span class="text-success">Обновить до последней версии</span></button>
           </div>
        </div>
    </div>
</div>

