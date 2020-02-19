<?php

use app\assets\HistoryAsset;
use app\models\database\Bill;
use app\models\database\Mailing;
use app\models\database\MassBill;
use nirvana\showloading\ShowLoadingAsset;
use yii\web\View;



/* @var $this View */
/* @var $mailing Mailing[] */
/* @var $bill MassBill[] */

$this->title = 'Массовые события';

HistoryAsset::register($this);
ShowLoadingAsset::register($this)

?>

<ul id="tabs" class="nav nav-tabs">
    <li id="bank_set_li" class="active"><a href="#mailings" data-toggle="tab">Рассылки</a></li>
    <li id="invoice_set_li"><a href="#invoices" data-toggle="tab">Выставления счетов</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane active" id="mailings">
        <?php
        if(!empty($mailing)){
            echo '<div class=\'col-sm-12\'><table class="table table-condensed table-striped"><thead><tr><th>Название рассылки</th><th>Действия</th></tr></thead><tbody>';
            foreach ($mailing as $item) {
                echo '<tr><td>' . urldecode($item->title) . '</td><td><div class="btn-group"><button class="btn btn-default delete-mailing" data-id="' . $item->id . '"><span class="text-danger glyphicon glyphicon-trash"></span></button><button class="btn btn-default info-mailing" data-id="' . $item->id . '"><span class="text-info glyphicon glyphicon-eye-open"></span></button><button class="btn btn-default resend-mailing" data-id="' . $item->id . '"><span class="text-info glyphicon glyphicon-refresh"></span></button></div></td></tr>';
            }
            echo '</tbody></table></div>';
        }
        else{
            echo '<h3 class="text-center">Рассылки не найдены</h3>';
        }
        ?>
    </div>
    <div class="tab-pane" id="invoices">
        <?php
        if(!empty($bill)){
            echo '<div class=\'col-sm-12\'><table class="table table-condensed table-striped"><thead><tr><th>Тип счёта</th><th>Период</th><th>Действия</th></tr></thead><tbody>';
            foreach ($bill as $item) {
                echo '<tr><td>' . Bill::getType($item->type) . '</td><td>' . $item->details . '</td><td><div class="btn-group"><button class="btn btn-default delete-mass-bill" data-id="' . $item->id . '"><span class="text-danger glyphicon glyphicon-trash"></span></button><button class="btn btn-default info-bill" data-id="' . $item->id . '"><span class="text-info glyphicon glyphicon-eye-open"></span></button></div></td></tr>';
            }
        }
        echo '</tbody></table></div>';
        ?>
    </div>
</div>
