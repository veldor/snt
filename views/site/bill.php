<?php

use app\assets\BankInvoiceAsset;
use app\models\CashHandler;
use app\models\database\Bill;
use app\models\Invoice;
use app\models\PowerBill;
use nirvana\showloading\ShowLoadingAsset;
use yii\web\View;

BankInvoiceAsset::register($this);
ShowLoadingAsset::register($this);

/* @var $this View */
/* @var $bill Bill */
/* @var $invoice Invoice */

if($bill->service_name === 'power'){
    $description = PowerBill::getDescription($bill);
}
else{
    $description = $bill->bill_destination;
}

$category = Bill::getType($bill->service_name);
$amount = CashHandler::toSmooth($bill->amount);

$text = "
<div class='description margened'><span>ПАО СБЕРБАНК</span><span class='pull-right''>Форма №ПД-4</span></div>

<div class='text-center bottom-bordered'><b>{$invoice->snt_name}</b></div>
<div class='text-center description margened'><span>(Наименование получателя платежа)</span></div>
<div class='bottom-bordered'><span><b>ИНН</b> {$invoice->payerInn} <b>КПП</b> {$invoice->kpp}</span><span class='pull-right'>{$invoice->personalAcc}</span></div>
<div class='description margened'><span>(инн получателя платежа)</span><span class='pull-right'>(номер счёта получателя платежа)</span></div>
<div class='bottom-bordered text-center'><span><b>БИК</b> {$invoice->bik} ({$invoice->bankName})</span></div>
<div class='text-center description margened'><span>(Наименование банка получателя платежа)</span></div>
<div class='bottom-bordered text-underline'>
        <b>Номер участка:</b> {$invoice->persAcc};
        <b> ФИО:</b> {$invoice->lastname};
        <b> Адрес:</b> {$invoice->payerAddress};
        <b>Наименование услуги:</b> {$category};
        <b>Назначение:</b> {$description};</div>
<div class='description margened text-center'><span>(назначение платежа)</span></div>
<div class='text-center bottom-bordered'><b>Сумма: {$amount}</b></div>
<div class='description margened text-center'><span>(сумма платежа)</span></div>

<div class='description margened'><span>С условиями приёма указанной в платёжном документе суммы, в т.ч. с суммой взимаемой платы за услуги банка, ознакомлен и согласен. </span><span class='pull-right'>Подпись плательщика <span class='sign-span bottom-bordered'></span></span></div>
";

$qr = $invoice->drawQR();

?>


<div id="invoiceWrapper">
    <table class="table">
        <tr>
            <td class="leftSide">
                <h3>Извещение</h3>
            </td>
            <td class="rightSide">
                <?=$text?>
            </td>
        </tr>
        <tr>
            <td class="leftSide">
                <h3>Квитанция</h3>
                <img class="qr-img" src="<?=$qr?>" alt=""/>
            </td>
            <td class="rightSide">
                <?=$text?>
            </td>
        </tr>
    </table>
</div>
