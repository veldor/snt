<?php /** @noinspection PhpUnused */

/** @noinspection PhpUndefinedClassInspection */

namespace app\controllers;

use app\models\database\Bill;
use app\models\database\Mail;
use app\models\database\MailingSchedule;
use app\models\database\MassBill;
use app\models\database\Payer;
use app\models\database\Phone;
use app\models\Mailing;
use app\models\PDFHandler;
use app\models\utils\Misc;
use DateTime;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UtilsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionMailingCreate(): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return Mailing::createMailing();
        }
        throw new NotFoundHttpException();
    }

    /**
     * @return array
     */
    public function actionMailDelete(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Mail::deleteMail();
    }

    /**
     * @return array
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionPhoneDelete(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Phone::deletePhone();
    }

    /**
     * @return array
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionPayerDelete(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Payer::deletePayer();
    }

    /**
     * @return array
     * @throws Throwable
     */
    public function actionBillDelete(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Bill::deleteBill();
    }


    /**
     * Счёт сохранён как PDF
     */
    public function actionInvoiceSaved(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Bill::invoiceSaved();
    }

    /**
     * @param $billId
     * @return void
     * @throws NotFoundHttpException
     */
    public function actionDownloadPdf($billId): void
    {
        $filename = PDFHandler::saveBillPdf($billId);
        Yii::$app->response->sendFile($filename['url'], $filename['name']);
    }


    /**
     */
    public function actionInvoicePrinted(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Bill::invoicePrinted();
    }

    public function actionSendNotifications(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Mailing::sendBillNotifications();
    }

    /**
     * @return array|null
     * @throws NotFoundHttpException
     */
    public function actionCancelMailing(): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return Mailing::cancelMailing();
        }
        throw new NotFoundHttpException();
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionSendMessage(): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return Mailing::sendMessage();
        }
        throw new NotFoundHttpException();
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionGetUnsendedMessagesCount(): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['status' => 1, 'count' => MailingSchedule::countWaiting()];
        }
        throw new NotFoundHttpException();
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionSaveMailTemplate(): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return Mailing::saveMailTemplate();
        }
        throw new NotFoundHttpException();
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionSaveBillMailTemplate(): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return Bill::saveMailTemplate();
        }
        throw new NotFoundHttpException();
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionClearMailingSchedule(): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return MailingSchedule::clearSchedule();
        }
        throw new NotFoundHttpException();
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDeleteMailing(): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return \app\models\database\Mailing::deleteMailing();
        }
        throw new NotFoundHttpException();
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDeleteBills(): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return MassBill::deleteBills();
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param $id
     * @return array
     */
    public function actionMailingInfo($id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $info = \app\models\database\Mailing::getMailingInfo($id);
        return ['status' => 1, 'html' => $info];
    }

    /**
     * @param $id
     * @return array
     */
    public function actionBillInfo($id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $info = Bill::getBillInfo($id);
        return ['status' => 1, 'html' => $info];
    }

    public function actionUpdate(): array
    {
        $file = dirname($_SERVER['DOCUMENT_ROOT'] . './/') . '/updateFromGithub.bat';
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = exec($file);
        file_put_contents('update_result.txt', $result);
        Yii::$app->session->addFlash('success', 'Программа обновлена до последней версии.');
        return ['status' => 1];
    }

    /**
     *
     */
    public function actionBackupDb(): void
    {
       $backupPath = Misc::backupDatabase();
    }

    public function actionSaveRegister(){
        $path = Misc::getRegisterPath();
        $date = new DateTime();
        $d = $date->format('Y-m-d H:i:s');
        Yii::$app->response->sendFile($path, "Реестр садоводов СНТ Линда {$d}.xml");
    }
}