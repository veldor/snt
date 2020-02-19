<?php

namespace app\controllers;

use app\models\database\Bill;
use app\models\database\Cottage;
use app\models\database\Mail;
use app\models\database\MailingSchedule;
use app\models\database\MassBill;
use app\models\database\Payer;
use app\models\database\Phone;
use app\models\Invoice;
use app\models\Mailing;
use app\models\PDFHandler;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\web\Controller;
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
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionMailingCreate()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return Mailing::createMailing();
        }
    }

    /**
     * @return array
     */
    public function actionMailDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Mail::deleteMail();
    }

    /**
     * @return array
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionPhoneDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Phone::deletePhone();
    }

    /**
     * @return array
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionPayerDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Payer::deletePayer();
    }

    /**
     * @return array
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionBillDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Bill::deleteBill();
    }


    /**
     * Счёт сохранён как PDF
     * @throws NotFoundHttpException
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
     * @throws NotFoundHttpException
     */
    public function actionInvoicePrinted(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Bill::invoicePrinted();
    }

    public function actionSendNotifications()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Mailing::sendBillNotifications();
    }

    public function actionCancelMailing()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return Mailing::cancelMailing();
        }
    }

    public function actionSendMessage()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return Mailing::sendMessage();
        }
    }

    public function actionGetUnsendedMessagesCount()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['status' => 1, 'count' => MailingSchedule::countWaiting()];
        }
    }

    public function actionSaveMailTemplate()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return Mailing::saveMailTemplate();
        }
    }

    public function actionSaveBillMailTemplate()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return Bill::saveMailTemplate();
        }
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionClearMailingSchedule()
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

    public function actionUpdate()
    {
        $file = dirname($_SERVER['DOCUMENT_ROOT'] . './/') . '/updateFromGithub.bat';
        Yii::$app->response->format = Response::FORMAT_JSON;
        exec($file);
        Yii::$app->session->addFlash('success', 'Программа обновлена до последней версии.');
        return ['status' => 1];
    }
}
