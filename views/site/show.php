<?php

use app\assets\ShowAsset;
use app\models\CashHandler;
use app\models\database\Bill;
use app\models\database\Cottage;
use app\models\database\Mail;
use app\models\database\Payer;
use app\models\database\Phone;
use app\models\PowerBill;
use nirvana\showloading\ShowLoadingAsset;
use yii\web\View;


/* @var $this View */
/* @var $cottage Cottage */
/* @var $payers Payer[] */
/* @var $mails Mail[] */
/* @var $phones Phone[] */
/* @var $bills Bill[] */

ShowAsset::register($this);
ShowLoadingAsset::register($this);

$this->title = "$cottage->num участок";

?>

<h1 class="text-center margin">Участок №<?= $cottage->num ?></h1>

<div class="row">
    <div class="col-sm-12 text-center margin">
        <div class="btn-group">
            <button class="btn btn-default" id="addBill"><span class="text-success">Выставить счёт</span></button>
            <button class="btn btn-default" id="addPowerBill"><span class="text-success">Выставить счёт за электроэнергию</span></button>
            <button class="btn btn-default" id="changeCottageBtn"><span class="text-info">Изменить данные участка</span></button>
            <button class="btn btn-default" id="deleteCottageBtn"><span class="text-danger">Удалить участок</span></button>
        </div>
    </div>

    <?php
    // покажу данные по участку
    echo "<div class='col-sm-12'><table class=\"table table-condensed table-striped\"><caption>Общие сведения об участке</caption><tbody>";
    echo '<tr><td>Площадь участка</td><td>' . ($cottage->square ? '<b class="text-success">' . $cottage->square . ' M<sup>2</sup></b>' : '<b class="text-danger">не задана</b>') . '</td>';
    echo '<tr><td>Членство собственника</td><td>' . ($cottage->membership ? '<b class="text-success">' . $cottage->membership . '</b>' : '<b class="text-danger">не задано</b>') . '</td>';
    echo '<tr><td>Данные о праве собственности</td><td>' . ($cottage->rigths ? '<b class="text-success">' . $cottage->rigths . '</b>' : '<b class="text-danger">не задано</b>') . '</td>';
    echo '<tr><td>Дополнительные сведения об участке</td><td>' . ($cottage->description ? '<b class="text-success">' . $cottage->description . '</b>' : '<b class="text-danger">не указаны</b>') . '</td>';
    echo '</tbody></table></div>';

    echo "<div class='col-sm-12'>";

    if(!empty($payers)){
        echo "<div class='col-sm-12'><h2 class='text-center text-success'>Плательщики</h2></div>";
        echo '<div class=\'col-sm-12\'><table class="table table-condensed table-striped"><thead><tr><th>ФИО</th><th>Адрес</th><th>Доля</th><th>Действия</th></tr></thead><tbody>';
        foreach ($payers as $payer){
            echo "<tr><td>{$payer->fio}</td><td>{$payer->address}</td><td>{$payer->part}</td><td><div class='btn-group'><button class='btn btn-default payer-delete' data-id='{$payer->id}'><span class='text-danger'>Удалить</span></button><button class='btn btn-default payer-change' data-id='{$payer->id}'><span class='text-info'>Изменить</span></button></div></td></tr>";
        }
        echo '</tbody></table></div>';
    }
    else{
        echo "<h2 class='text-center'>Плательщики не зарегистрированы</h2>";
    }
    echo '<div class="col-sm-12"><button class="btn btn-default" id="addPayer"><span class="text-success"><span class="glyphicon glyphicon-plus"></span> Добавить плательщика</span></button></div>';


    if(!empty($mails)){
        echo "<div class='col-sm-12'><h2 class='text-center text-info'>Адреса электронной почты</h2></div>";
        echo '<div class=\'col-sm-12\'><table class="table table-condensed table-striped"><thead><tr><th>ФИО</th><th>Адрес</th><th>Действия</th></tr></thead><tbody>';
        foreach ($mails as $mail){
            echo "<tr><td>{$mail->fio}</td><td><a href='malito:{$mail->email}'>{$mail->email}</a></td><td><div class='btn-group'><button class='btn btn-default mail-delete' data-id='{$mail->id}'><span class='text-danger'>Удалить</span></button><button class='btn btn-default mail-change' data-id='{$mail->id}'><span class='text-info'>Изменить</span></button></div></td></tr>";
        }
        echo '</tbody></table></div>';
    }
    else{
        echo "<h2 class='text-center'>Адреса электронной почты не зарегистрированы</h2>";
    }
    echo '<div class="col-sm-12"><button class="btn btn-default" id="addMailBtn"><span class="text-success"><span class="glyphicon glyphicon-plus"></span> Добавить электронную почту</span></button></div>';


    if(!empty($phones)){
        echo "<div class='col-sm-12'><h2 class='text-center text-warning'>Номера телефонов</h2></div>";
        echo '<div class=\'col-sm-12\'><table class="table table-condensed table-striped"><thead><tr><th>ФИО</th><th>Номер</th><th>Действия</th></tr></thead><tbody>';
        foreach ($phones as $phone){
            echo "<tr><td>{$phone->fio}</td><td><a href='tel:{$phone->phone}'>{$phone->phone}</a></td><td><div class='btn-group'><button class='btn btn-default phone-delete' data-id='{$phone->id}'><span class='text-danger'>Удалить</span></button><button class='btn btn-default phone-change' data-id='{$phone->id}'><span class='text-info'>Изменить</span></button></div></td></tr>";
        }
        echo '</tbody></table></div>';
    }
    else{
        echo "<h2 class='text-center'>Номера телефонов не зарегистрированы</h2>";
    }
    echo '<div class="col-sm-12"><button class="btn btn-default" id="addPhoneBtn"><span class="text-success"><span class="glyphicon glyphicon-plus"></span> Добавить номер телефона</span></button></div>';


    if(!empty($bills)){

        echo "<div class='col-sm-12'><h2 class='text-center'>Счета участка</h2></div>";

        echo '<div class=\'col-sm-12\'><table class="table table-condensed table-striped"><thead><tr><th>Назначение</th><th>Детали</th><th>Плательщик</th><th>Сумма счёта</th><th>Действия</th></tr></thead><tbody>';
        foreach ($bills as $bill){
            if($bill->service_name === 'power'){
                $description = PowerBill::getDescription($bill);
            }
            else{
                $description = $bill->bill_destination;
            }
            $billType = Bill::getType($bill->service_name);
            $amount = CashHandler::toSmooth($bill->amount);
            $saveBtn = $bill->is_saved ? "<a target='_blank' href='/pdf/{$bill->id}' class='btn btn-default tooltip-enabled save-pdf' data-toggle='tooltip' data-placement='auto' title='Сохранить как PDF (уже сохранялось)' data-bill-id='{$bill->id}'><span class='text-success glyphicon glyphicon-saved'></span></a>": "<a target='_blank' href='/pdf/{$bill->id}' class='btn btn-default tooltip-enabled save-pdf' data-toggle='tooltip' data-placement='auto' title='Сохранить как PDF' data-bill-id='{$bill->id}'><span class='text-info glyphicon glyphicon-saved'></span></a>";

            $sendBtn = $bill->is_saved ? "<button class='btn btn-default tooltip-enabled send-bill'  data-toggle='tooltip' data-placement='auto' title='Отправить счёт по электронной почте (уже отправлялось)' data-bill-id='{$bill->id}'><span class='text-success glyphicon glyphicon-send'></span></button>" : "<button class='btn btn-default tooltip-enabled send-bill'  data-toggle='tooltip' data-placement='auto' title='Отправить счёт по электронной почте' data-bill-id='{$bill->id}'><span class='text-info glyphicon glyphicon-send'></span></button>";

            $printBtn = $bill->is_printed ? "<a href='/bill/{$bill->id}' target='_blank' class='btn btn-default tooltip-enabled print-bill' data-toggle='tooltip' data-placement='auto' title='Распечатать (уже печаталось)' data-bill-id='{$bill->id}'><span class='text-success glyphicon glyphicon-print'></span></a>" : "<a href='/bill/{$bill->id}' target='_blank' class='btn btn-default tooltip-enabled print-bill' data-toggle='tooltip' data-placement='auto' title='Распечатать' data-bill-id='{$bill->id}'><span class='text-info glyphicon glyphicon-print'></span></a>";

            echo "<tr><td>$billType</td><td>$description</td><td>{$bill->payer}</td><td>{$amount}</td><td><div class='btn-group'><button class='btn btn-default bill-delete' data-id='{$bill->id}'><span class='text-danger glyphicon glyphicon-trash'></span></button><button class='btn btn-default bill-change' data-id='{$bill->id}'><span class='text-info glyphicon glyphicon-pencil'></span></button>$printBtn $sendBtn $saveBtn</div></td></tr>";
        }
        echo '</tbody></table></div>';

        echo '<div class="col-sm-12 text-center"><button class="btn btn-default" id="multipleInvoiceSendButton"><span class="text-success">Отправить несколько квитанций в одном письме</span></button></div>';

/*        foreach ($bills as $bill){
            echo "<div class='col-sm-12 margin'><a target='_blank' href='/bill/{$bill->number}'><b class='text-success'>{$bill->number}</b></a> <span>{$bill->amount}</span> <button class='btn btn-default'><span class='glyphicon glyphicon-print'></span></button> <button class='btn btn-default'><span class='glyphicon glyphicon-send'></span></button> <button class='btn btn-default'><span class='glyphicon glyphicon-saved'></span></button></div>";
        }*/
    }
    else{
        echo "<h2 class='text-center'>Счета не зарегистрированы</h2>";
    }
    ?>
</div>