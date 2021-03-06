<?php

namespace app\controllers;

use app\models\database\Bill;
use app\models\database\Cottage;
use app\models\database\Mail;
use app\models\database\Payer;
use app\models\Invoice;
use app\models\Mailing;
use app\models\MailSettings;
use app\models\PowerBill;
use app\models\utils\DbRestore;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class SettingsController extends Controller
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
    public function actionIndex(): string
    {
        // если переданы данные постом- это бекап базы данных
        if(Yii::$app->request->isPost){
            $restoreModel = new DbRestore();
            $restoreModel->file = UploadedFile::getInstance($restoreModel, 'file');
            $restoreModel->restore();
        }

        // получу общие данные о счёте
        $bankInfo = Invoice::getBankInfo();
        $mailSettings = new MailSettings();
        $mailTemplate = Mailing::getMailingTemplate();
        $billMailTemplate = Bill::getMailingTemplate();
        $powerSettings = new PowerBill();
        $dbRestore = new DbRestore();
        return $this->render('index', ['bankInfo' => $bankInfo, 'mailSettings' => $mailSettings, 'mailTemplate' => $mailTemplate, 'billMailTemplate' => $billMailTemplate, 'powerSettings' => $powerSettings, 'dbRestore' => $dbRestore]);
    }

    public function actionEditBankSettings(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new Invoice();
        $form->load(Yii::$app->request->post());
        return $form->saveSettings();
    }
    public function actionEditMailSettings(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new MailSettings();
        $form->load(Yii::$app->request->post());
        return $form->saveSettings();
    }
    public function actionEditPowerSettings(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new PowerBill();
        $form->setScenario(PowerBill::SCENARIO_EDIT);
        $form->load(Yii::$app->request->post());
        return $form->saveSettings();
    }

}
