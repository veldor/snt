<?php
/**
 * Created by PhpStorm.
 * User: eldor
 * Date: 25.04.2019
 * Time: 8:43
 */

namespace app\models;


use app\models\database\Bill;
use Dompdf\Dompdf;
use Yii;
use yii\web\NotFoundHttpException;

class PDFHandler
{
    public static function renderPDF($text, $filename, $orientation): void
    {
        $dompdf = new Dompdf([
            'defaultFont' => 'times',//делаем наш шрифт шрифтом по умолчанию
        ]);
        $dompdf->loadHtml($text);
// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', $orientation);
        $dompdf->render();
        $output = $dompdf->output();
        file_put_contents($filename, $output);
    }

    /**
     * @param $billId
     * @return array
     * @throws NotFoundHttpException
     */
    public static function saveBillPdf($billId, $is_separated_file = false): array
    {
        $billInfo = Bill::findOne($billId);
        if($billInfo === null){
            die('Не найдена информация об участке');
        }
        $invoice = Invoice::getInstance($billInfo);
        $type = Bill::getType($billInfo->service_name);
        $text = Yii::$app->controller->renderPartial('/site/bank-invoice-pdf.php', ['bill' => $billInfo, 'invoice' => $invoice]);
        $dest = PowerBill::getDescription($billInfo);
        if($is_separated_file){
            self::renderPDF($text, 'bills/' . $billId . '.pdf', 'portrait');
            return ['url' => Yii::getAlias('@webroot') . '/bills/' . $billId . '.pdf', 'name' => 'Участок ' . $billInfo->cottageNumber . ' ' . $type . '_' . $dest . '.pdf'];
        }

        self::renderPDF($text, 'invoice.pdf', 'portrait');
        return ['url' => Yii::getAlias('@webroot') . '/invoice.pdf', 'name' => 'Участок ' . $billInfo->cottageNumber . ' ' . $type . '_' . $dest . '.pdf'];
    }
}