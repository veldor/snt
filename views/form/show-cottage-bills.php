<?php

use app\models\CashHandler;
use app\models\database\Bill;
use yii\web\View;



/* @var $this View */
/* @var $bills Bill[] */

if(!empty($bills)){
    ?>
    <div class="row">
        <div class="col-sm-12"><h3 class="text-center">Выберите счета для отправки</h3></div>
        <div class="col-sm-12">
            <form id="selectBillsToSend">
                <table class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <th>Номер счёта</th>
                            <th>Назначение платежа</th>
                            <th>Сумма платежа</th>
                            <th>Отправить в квитанции</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($bills as $bill) {
                        $destination = '';
                        switch ($bill->service_name){
                            case 'membership':
                                $destination .= 'Членские взносы ';
                                break;
                            case 'target':
                                $destination .= 'Целевые взносы ';
                                break;
                            case 'power':
                                $destination .= 'Электроэнергия ';
                                break;
                        }
                        $destination .= $bill->bill_destination;
                        echo "<tr>
                                    <td>{$bill->id}</td>
                                    <td>{$destination}</td>
                                    <td>" . CashHandler::toSmooth($bill->amount) . "</td>
                                    <td><input type='checkbox' name='Send[{$bill->id}]' /></td>
                            </tr>";
                    }
                    ?>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-success">Отправить счета</button>
            </form>
        </div>
    </div>
<?php
}
else{
    echo '<h1 class="text-center">Счетов не найдено</h1>';
}