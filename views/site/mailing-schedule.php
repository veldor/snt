<?php

use app\assets\MailingScheduleAsset;
use app\models\database\Bill;
use app\models\database\Cottage;
use app\models\database\Mail;
use app\models\database\Mailing;
use nirvana\showloading\ShowLoadingAsset;
use yii\web\NotFoundHttpException;
use yii\web\View;


/* @var $this View */
/* @var $waiting app\models\database\MailingSchedule[] */
MailingScheduleAsset::register($this);
 ShowLoadingAsset::register($this);

$this->title = 'Очередь сообщений';

if (!empty($waiting)) {
    echo "<h1 class='margin text-center'>Рассылка</h1>";
    echo "<div class='margin text-center'><span>Сообщений в очереди- <span id='unsendedMessagesCounter'>" . count($waiting) . '</span></span></div>';
    echo "<div class='text-center margin'><div class='btn-group-vertical'><button class='btn btn-default' id='beginSendingBtn'><span class='text-success'>Начать рассылку</span></button><button class='btn btn-default' id='clearSendingBtn'><span class='text-danger'>Очистить список</span></button></div></div>";
    echo '<table class="table table-bordered table-striped table-hover"><thead><tr><th>Тип</th><th>Номер участка</th><th>Заголовок</th><th>Адрес почты</th><th>ФИО</th><th>Статус</th><th>Действия</th></thead><tbody>';
    foreach ($waiting as $item) {
        // найду информацию о почте и о рассылке
        try {
            $mailInfo = Mail::getMailById($item->mailId);
            $cottage = Cottage::findOne($mailInfo->cottage_num);
            if (!empty($item->mailingId)) {
                $mailingInfo = Mailing::findOne($item->mailingId);
                // покажу информацию о ожидающем сообщении
                echo "<tr class='text-center align-middle'><td><b class='text-info'>Рассылка</b></td><td>{$cottage->num}</td><td>" . urldecode($mailingInfo->title) . "</td><td>{$mailInfo->email}</td><td>{$mailInfo->fio}</td><td><b class='text-info mailing-status' data-schedule-id='{$item->id}'>Ожидает отправки</b></td><td><button class='mailing-cancel btn btn-default' data-schedule-id='{$item->id}'><span class='text-danger'>Отменить отправку</span></button></td></tr>";
            } elseif (!empty($item->billId)) {
                $billInfo = Bill::findOne($item->billId);
                $type = Bill::getType($billInfo->service_name);
                echo "<tr class='text-center align-middle'><td><b class='text-success'>Счёт</b></td><td>{$cottage->num}</td><td>{$type} : {$billInfo->bill_destination}</td><td>{$mailInfo->email}</td><td>{$mailInfo->fio}</td><td><b class='text-info mailing-status' data-schedule-id='{$item->id}'>Ожидает отправки</b></td><td><button class='mailing-cancel btn btn-default' data-schedule-id='{$item->id}'><span class='text-danger'>Отменить отправку</span></button></td></tr>";
            }
            elseif(!empty($item->bills)){
                echo "<tr class='text-center align-middle'><td><b class='text-success'>Счёта</b></td><td>{$cottage->num}</td><td>Несколько счетов : {$item->bills}</td><td>{$mailInfo->email}</td><td>{$mailInfo->fio}</td><td><b class='text-info mailing-status' data-schedule-id='{$item->id}'>Ожидает отправки</b></td><td><button class='mailing-cancel btn btn-default' data-schedule-id='{$item->id}'><span class='text-danger'>Отменить отправку</span></button></td></tr>";
            }
        } catch (NotFoundHttpException $e) {
            echo $e->getMessage();
            die;
        }
    }
    echo '</tbody></table>';
} else {
    echo "<h1 class='text-center'>Неотправленных сообщений не найдено</h1>";
}