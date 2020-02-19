<?php

namespace app\controllers;

use app\models\database\Bill;
use app\models\database\Cottage;
use app\models\database\Mail;
use app\models\database\Mailing;
use app\models\database\MailingSchedule;
use app\models\database\MassBill;
use app\models\database\Payer;
use app\models\database\Phone;
use app\models\Invoice;
use app\models\MassMembership;
use app\models\MassTarget;
use app\models\Migration;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SiteController extends Controller
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        //Migration::migrate();
        // получу список участков
        $cottages = Cottage::getCottages();
        return $this->render('index', ['cottages' => $cottages]);
    }

    public function actionRegisterCottage()
    {

        Yii::$app->response->format = Response::FORMAT_JSON;
        return Cottage::registerNew();
    }

    /**
     * @param $cottageNumber
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionShow($cottageNumber)
    {
        $cottage = Cottage::getCottage($cottageNumber);
        $payers = Payer::getCottagePayers($cottage);
        $mails = Mail::getCottageMails($cottage);
        $bills = Bill::getCottageBills($cottage);
        $phones = Phone::getCottagePhones($cottage);
        return $this->render('show', ['cottage' => $cottage, 'payers' => $payers, 'mails' => $mails, 'bills' => $bills, 'phones' => $phones]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionShowBill($id)
    {
        $billInfo = Bill::findOne($id);
        $invoice = Invoice::getInstance($billInfo);
        return $this->render('bill', ['bill' => $billInfo, 'invoice' => $invoice]);
    }

    /**
     * @return array
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionCottageDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Cottage::deleteCottage();
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionBillAdd(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Bill::addBill();
    }


    public function actionMailing($id = null)
    {
        $previousMailngInfo = null;
        $mailsList = Mail::getAllMailsByCottages();
        if($id !== null){
            $previousMailngInfo = Mailing::findOne($id);
        }
        return $this->render('mailing', ['mails' => $mailsList, 'mailing' => $previousMailngInfo]);
    }

    public function actionMailingSchedule()
    {
        $waitingMessages = MailingSchedule::getWaiting();
        return $this->render('mailing-schedule', ['waiting' => $waitingMessages]);
    }

    public function actionMassBill($type)
    {
        if (Yii::$app->request->isPost) {
            if ($type === 'membership') {
                // создам рассылку
                $model = new MassMembership();
                $model->load(Yii::$app->request->post());
                $model->create();
                // перенаправлю на страницу рассылки почты
                return $this->redirect(Url::toRoute(['site/mailing-schedule']));
            }
            if ($type === 'target') {
                // создам рассылку
                $model = new MassTarget();
                $model->load(Yii::$app->request->post());
                $model->create();
                // перенаправлю на страницу рассылки почты
                return $this->redirect(Url::toRoute(['site/mailing-schedule']));
            }
        }
        if ($type === 'membership'){
            $model = new MassMembership();
            return $this->render('mass-membership', ['model' => $model]);
        }
            $model = new MassTarget();
        return $this->render('mass-target', ['model' => $model]);
    }


    /**
     * @return Response
     */
    public function actionPrevious(): Response
    {
        $url = Cottage::getPreviousCottage();
        return $this->redirect($url, 301);
    }

    /**
     * @return Response
     */
    public function actionNext(): Response
    {
        $url = Cottage::getNextCottage();
        return $this->redirect($url, 301);
    }

    public function actionHistory(){
        $mailings = Mailing::getAll();
        $massBills = MassBill::getAll();
        return $this->render('history', ['mailing' => $mailings, 'bill' => $massBills]);
    }
}
