<?php

use app\models\database\Bill;
use app\models\database\Mail;
use app\models\GrammarHandler;
use app\models\Mailing;
use yii\web\View;

$rootTemplate = Mailing::getMailingTemplate();

/* @var $this View */
/* @var $mail Mail */
/* @var $bill ?Bill */
/* @var $bills ?string */

/* @var $mailing ?app\models\database\Mailing */

$acceptorIO = GrammarHandler::handlePersonals($mail->fio);

$text = '';

if (!empty($bill)) {
    $text = Bill::getMailingTemplate($bill);
}

if (!empty($mailing)) {
    $text = urldecode($mailing->body);
}

if(!empty($bills)){
    // для каждого счёта найду подробности
    /*$billsArray = explode(',', $bills);
    foreach ($billsArray as $billItem) {
        $billInfo = Bill::findOne($billItem);
        if($billInfo !== null){
            $text .= Bill::getMailingTemplate($billInfo);
        }
    }*/
    $text = 'Вам выставлены счета за услуги СНТ. Квитанции находятся во вложении к письму.';
}

$text = GrammarHandler::handleMailText($rootTemplate, $acceptorIO, $text);

?>
<!DOCTYPE HTML>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <style type="text/css">
        #main-table {
            max-width: 600px;
            width: 100%;
            margin: auto;
            padding: 0;
        }

        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 20px;
        }
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
    </style>
    <title></title>
</head>
<body>
<table id="main-table">
    <tbody>
    <tr>
        <td colspan="2">
            <?= $text ?>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>



