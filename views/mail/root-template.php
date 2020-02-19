<?php

use app\models\database\Bill;
use app\models\database\Mail;
use app\models\GrammarHandler;
use app\models\Mailing;
use yii\web\View;

$rootTemplate = Mailing::getMailingTemplate();

/* @var $this View */
/* @var $mail Mail */
/* @var $bill Bill */
/* @var $mailing app\models\database\Mailing */

$acceptorIO = GrammarHandler::handlePersonals($mail->fio);

if(!empty($bill)){
    $text = Bill::getMailingTemplate();
}

if(!empty($mailing)){
    $text = urldecode($mailing->body);
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

        .text-center {
            text-align: center;
        }

        .social-icon {
            width: 30px;
            height: 30px;
            position: relative;
            margin-top: 10px;
            top: 10px;
        }

        img.logo-img {
            width: 50%;
            margin-left: 25%;
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



