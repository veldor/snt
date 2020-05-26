<?php


namespace app\models\database;


use app\models\selections\CottageMail;
use Yii;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * Class Table_bank_invoices
 * @package app\models
 *
 * @property int $id [int(10) unsigned]
 * @property int $cottage_num [int(10) unsigned]
 * @property string $email [varchar(255)]
 * @property string $fio [varchar(255)]
 */

class Mail extends ActiveRecord
{

    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_EDIT = 'edit';

    public static function tableName():string
    {
        return 'mails';
    }

    public function scenarios():array
    {
        return [
            self::SCENARIO_CREATE => ['fio', 'email', 'cottage_num'],
            self::SCENARIO_EDIT => ['fio', 'email'],
        ];
    }

    public function attributeLabels():array
    {
        return [
            'fio' => 'ФИО',
            'email' => 'Адрес электронной почты',
        ];
    }


    /**
     * @return array
     */
    public function rules():array
    {
        return [
            [['fio', 'email'], 'required'],
            ['email', 'email'],
        ];
    }

    /**
     * @param Cottage $cottage
     * @return Mail[]
     */
    public static function getCottageMails(Cottage $cottage): array
    {
        return self::findAll(['cottage_num' => $cottage->id]);
    }

    public static function deleteMail(): array
    {
        $mailId = trim(Yii::$app->request->post('id'));
        if(!empty($mailId)){
            $mail = self::findOne(['id' => $mailId]);
            if($mail !== null){
                $mail->delete();
                Yii::$app->session->addFlash('success', "Почта удалёна.");
                return ['status' => 1];

            }
            return ['message' => 'Не найдена почта'];
        }
        return ['message' => 'Не найден идентификатор почты'];
    }

    /**
     * @return CottageMail[]
     */
    public static function getAllMailsByCottages(): array
    {
        $cottages = Cottage::getCottages();
        $result = [];
        foreach ($cottages as $cottage) {
            $mails = self::getCottageMails($cottage);
            if(!empty($mails)){
                foreach ($mails as $mail) {
                    $newMail = new CottageMail();
                    $newMail->cottageNumber = $cottage->num;
                    $newMail->mail = $mail->email;
                    $newMail->mailId = $mail->id;
                    $newMail->name = $mail->fio;
                    $result[] = $newMail;
                }
            }
        }
        return $result;
    }

    /**
     * @param $mail
     * @return Mail|null
     * @throws NotFoundHttpException
     */
    public static function getMailById($mail): ?Mail
    {
        $existentMail = self::findOne($mail);
        if($existentMail === null){
            throw new NotFoundHttpException();
        }
        return $existentMail;
    }

}