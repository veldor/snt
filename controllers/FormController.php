<?php /** @noinspection PhpUnused */

namespace app\controllers;

use app\models\CashHandler;
use app\models\database\Bill;
use app\models\database\Cottage;
use app\models\database\Mail;
use app\models\database\Payer;
use app\models\database\Phone;
use app\models\PowerBill;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ErrorAction;

/** @noinspection PhpUnused */

class FormController extends Controller
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
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionPayerChange($id): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = Payer::findOne($id);
            $view = $this->renderAjax('change-payer', ['matrix' => $form]);
            return ['status' => 1,
                'header' => 'Изменение данных о плательщике',
                'data' => $view,
            ];
        }
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = Payer::findOne($id);
            if ($form === null) {
                die('Не наден плательщик');
            }
            $form->setScenario(Payer::SCENARIO_EDIT);
            $form->load(Yii::$app->request->post());
            $form->save();
            Yii::$app->session->addFlash('success', 'Данные плательщика изменены.');
            return ['status' => 1];
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionMailChange($id): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = Mail::findOne($id);
            $view = $this->renderAjax('change-mail', ['matrix' => $form]);
            return ['status' => 1,
                'header' => 'Изменение данных адреса электронной почты',
                'data' => $view,
            ];
        }
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = Mail::findOne($id);
            if ($form !== null) {
                $form->setScenario(Mail::SCENARIO_EDIT);
                $form->load(Yii::$app->request->post());
                $form->save();
                Yii::$app->session->addFlash('success', 'Данные адреса электронной почты изменены.');
                return ['status' => 1];
            }
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionPhoneChange($id): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = Phone::findOne($id);
            $view = $this->renderAjax('change-phone', ['matrix' => $form]);
            return ['status' => 1,
                'header' => 'Изменение данных номера телефона',
                'data' => $view,
            ];
        }
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = Phone::findOne($id);
            if ($form !== null) {
                $form->setScenario(Phone::SCENARIO_EDIT);
                $form->load(Yii::$app->request->post());
                $form->save();
                Yii::$app->session->addFlash('success', 'Данные номера телефона изменены.');
                return ['status' => 1];
            }
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionBillChange($id = false): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = Bill::findOne($id);
            if($form !== null){
                $form->amount = CashHandler::toFloat($form->amount);
                $view = $this->renderAjax('change-bill', ['matrix' => $form]);
                return ['status' => 1,
                    'header' => 'Изменение данных счёта',
                    'data' => $view,
                ];
            }
        }
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = Bill::findOne($id);
            $form->setScenario(Bill::SCENARIO_EDIT);
            $form->load(Yii::$app->request->post());
            $form->payer = Payer::findOne($form->payerId)->fio;
            $form->amount = CashHandler::toDBCash($form->amount);
            $form->save();
            Yii::$app->session->addFlash('success', 'Данные счёта изменены.');
            return ['status' => 1];
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param null $cottageNumber
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionPayerAdd($cottageNumber = null): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $cottage = Cottage::getCottage($cottageNumber);
            $form = new Payer(['scenario' => Payer::SCENARIO_CREATE]);
            $form->cottage = $cottage->id;
            $view = $this->renderAjax('add-payer', ['matrix' => $form]);
            return ['status' => 1,
                'header' => 'Добавление плательщика',
                'data' => $view,
            ];
        }
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = new Payer(['scenario' => Payer::SCENARIO_CREATE]);
            $form->load(Yii::$app->request->post());
            $form->save();
            Yii::$app->session->addFlash('success', 'Добавлен новый плательщик.');
            return ['status' => 1];
        }
        throw new NotFoundHttpException();
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionCottageAdd(): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = new Cottage(['scenario' => Cottage::SCENARIO_CREATE]);
            $view = $this->renderAjax('add-cottage', ['matrix' => $form]);
            return ['status' => 1,
                'header' => 'Добавление участка',
                'data' => $view,
            ];
        }
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = new Cottage(['scenario' => Payer::SCENARIO_CREATE]);
            $form->load(Yii::$app->request->post());
            // проверю, не зарегистрирован ли ещё участок с таким же номером
            if (Cottage::exist($form->num)) {
                return ['message' => 'Участок с таким номером уже зарегистрирован'];
            }
            $form->save();
            Yii::$app->session->addFlash('success', 'Добавлен новый участок.');
            return ['status' => 1];
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionCottageChange($id): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = Cottage::getCottage($id);
            $form->setScenario(Cottage::SCENARIO_EDIT);
            $view = $this->renderAjax('change-cottage', ['matrix' => $form]);
            return ['status' => 1,
                'header' => 'Изменение данных об участке',
                'data' => $view,
            ];
        }
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = Cottage::findOne($id);
            $form->setScenario(Cottage::SCENARIO_EDIT);
            $form->load(Yii::$app->request->post());
            $form->save();
            Yii::$app->session->addFlash('success', 'Данные об участке изменены.');
            return ['status' => 1];
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param null $cottageNumber
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionMailAdd($cottageNumber = null): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $cottage = Cottage::getCottage($cottageNumber);
            $form = new Mail(['scenario' => Mail::SCENARIO_CREATE]);
            $form->cottage_num = $cottage->id;
            $view = $this->renderAjax('add-mail', ['matrix' => $form]);
            return ['status' => 1,
                'header' => 'Добавление адреса электронной почты',
                'data' => $view,
            ];
        }
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = new Mail(['scenario' => Mail::SCENARIO_CREATE]);
            $form->load(Yii::$app->request->post());
            if ($form->validate()) {
                $form->save();
                Yii::$app->session->addFlash('success', 'Добавлен адрес электронной почты.');
                return ['status' => 1];
            }

            return ['message' => $form->getErrors()];
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param null $cottageNumber
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionPhoneAdd($cottageNumber = null): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $cottage = Cottage::getCottage($cottageNumber);
            $form = new Phone(['scenario' => Phone::SCENARIO_CREATE]);
            $form->cottage_num = $cottage->id;
            $view = $this->renderAjax('add-phone', ['matrix' => $form]);
            return ['status' => 1,
                'header' => 'Добавление номера телефона',
                'data' => $view,
            ];
        }
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = new Phone(['scenario' => Phone::SCENARIO_CREATE]);
            $form->load(Yii::$app->request->post());
            if ($form->validate()) {
                $form->save();
                Yii::$app->session->addFlash('success', 'Добавлен номер телефона.');
                return ['status' => 1];
            }

            return ['message' => $form->getErrors()];
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param null $cottageNumber
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionBillAdd($cottageNumber = null): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $cottage = Cottage::getCottage($cottageNumber);
            $form = new Bill(['scenario' => Bill::SCENARIO_CREATE]);
            $form->cottage = $cottage->id;
            $form->cottageNumber = $cottage->num;
            $view = $this->renderAjax('add-bill', ['matrix' => $form]);
            return ['status' => 1,
                'header' => 'Создание платежа',
                'data' => $view,
            ];
        }
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = new Bill(['scenario' => Bill::SCENARIO_CREATE]);
            $form->load(Yii::$app->request->post());
            if ($form->validate() && $form->fillMore()) {
                $form->save();
                Yii::$app->session->addFlash('success', 'Добавлен счёт.');
                return ['status' => 1];
            }

            return ['message' => $form->getErrors()];
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param null $cottageNumber
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionPowerBillAdd($cottageNumber = null): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $cottage = Cottage::getCottage($cottageNumber);
            $form = new PowerBill();
            $form->setScenario(PowerBill::SCENARIO_CREATE);
            $form->cottageId = $cottage->id;
            $view = $this->renderAjax('add-power-bill', ['matrix' => $form]);
            return ['status' => 1,
                'header' => 'Создание платежа',
                'data' => $view,
            ];
        }
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $form = new PowerBill();
            $form->setScenario(PowerBill::SCENARIO_CREATE);
            $form->load(Yii::$app->request->post());
            if ($form->validate()) {
                $form->save();
                Yii::$app->session->addFlash('success', 'Добавлен счёт.');
                return ['status' => 1];
            }

            return ['message' => $form->getErrors()];
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param $cottageNumber
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionGetCottageBills($cottageNumber): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cottage = Cottage::getCottage($cottageNumber);
        $bills = Bill::getCottageBills($cottage);
        $view = $this->renderAjax('show-cottage-bills', ['bills' => $bills]);
        return ['status' => 1,
            'header' => 'Массовая отправка квитанций',
            'data' => $view,
        ];
    }
}
