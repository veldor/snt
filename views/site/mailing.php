<?php

use app\assets\MailAsset;
use app\models\database\Mailing;
use app\models\selections\CottageMail;
use mihaildev\ckeditor\CKEditor;
use nirvana\showloading\ShowLoadingAsset;
use yii\web\View;


/* @var $this View */
/* @var $mails CottageMail[] */
/* @var $mailing Mailing|null */

$this->title = "Общая рассылка";

MailAsset::register($this);
ShowLoadingAsset::register($this);

?>
<div class="row">
    <div class="col-sm-12 margin">
        <div class="col-sm-5"><label for="mailingSubject" class="control-label">Тема рассылки</label></div>
        <div class="col-xs-7"><input class="form-control" id="mailingSubject" type="text" maxlength="100" value="<?= $mailing ? urldecode($mailing->title) : ''?>"/>
        </div>
    </div>
    <div class="col-sm-12 margin">
        <label for="mailingBody"></label><textarea title="mailing text" id="mailingBody" name="mailingBody"><?= $mailing ? urldecode($mailing->body) : ''?></textarea>
        <?php
        try {
            CKEditor::widget([
                'name' => 'mailing',
                'editorOptions' => [
                    'preset' => 'full', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
                    //'extraPlugins' => 'lexemes',
                ],
                'options' =>['id' => 'mailingBody']
            ]);
        } catch (Exception $e) {
        }
        ?>
    </div>

    <div class="col-sm-12 margin">
        <div class='btn-group-vertical margened'>
            <button id='selectAllActivator' type='button' class='btn btn-info'>Отправить всем</button>
            <button id='selectNoneActivator' type='button' class='btn btn-info'>Сбросить выделение</button>
            <button id='selectInvertActivator' type='button' class='btn btn-info'>Инвертировать выделение</button>
        </div>
    </div>

    <div class="col-sm-12 margin">
        <?php
        if(!empty($mails)){
            echo '<table class="table table-bordered table-striped table-condensed table-hover"><thead><tr><th>№ участка</th><th>Адрес почты</th><th>ФИО</th><th>Статус</th></thead><tbody>';
            foreach ($mails as $mail) {
                echo "<tr class='text-center'><td>{$mail->cottageNumber}</td><td>{$mail->mail}</td><td>{$mail->name}</td><td><label class='btn btn-success'><input type='checkbox' class='mail-target' data-mail-id='{$mail->mailId}'/>Отправить письмо</label></td></tr>";
            }
            echo '</tbody></table>';
        }
        ?>
    </div>

    <div class="col-sm-12 margened">
        <button id="createMailingActivator" class="btn btn-success">Создать рассылку</button>
    </div>
</div>
