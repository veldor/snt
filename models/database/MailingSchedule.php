<?php


namespace app\models\database;


use app\models\selections\CottageMail;
use Throwable;
use Yii;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

/**
 * Class Mailing
 * @package app\models\database
 *
 * @property int $id [int(10) unsigned]
 * @property int $mailingId [int(10) unsigned]
 * @property int $mailId [int(10) unsigned]
 * @property int $billId [int(10) unsigned]
 * @property string $bills Счета для отправки
 */

class MailingSchedule extends ActiveRecord
{

    public static function tableName():string
    {
        return 'mailing_schedule';
    }

    /**
     * @return MailingSchedule[]
     */
    public static function getWaiting(): array
    {
        return self::find()->orderBy('cast(mailingId as unsigned) desc')->all();
    }

    public static function countWaiting()
    {
        return self::find()->count();
    }

    /**
     * @return array
     * @throws StaleObjectException
     * @throws Throwable
     */
    public static function clearSchedule(): array
    {
        $allMessages = self::find()->all();
        if(!empty($allMessages)){
            foreach ($allMessages as $message) {
                $message->delete();
            }
        }
        return ['status' => 1];
    }

    /**
     * @param $mailing
     * @return MailingSchedule[]
     */
    public static function getMailingMessages($mailing): array
    {
        return self::find()->where(['mailingId' => $mailing->id])->all();
    }
    /**
     * @param $billing MassBill
     * @return MailingSchedule[]
     */
    public static function getBillingMessages($billing): array
    {
        return self::find()->where(['mailingId' => $billing->id])->all();
    }

    /**
     * @param array $data
     * @return bool
     * @throws NotFoundHttpException
     */
    public static function addBills(array $data): bool
    {
        if($data !== null && count($data) > 0){
            $keys = array_keys($data);
            // найду данные об участке
            $cottage = Cottage::getCottage(Bill::findOne($keys[0])->cottageNumber);
            $value = implode(',', $keys);
            $mails = Mail::getCottageMails($cottage);
            if(!empty($mails)){
                foreach ($mails as $mail) {
                    (new self(['bills' => $value, 'mailId' => $mail->id]))->save();
                }
                return true;
            }
        }
        return false;
    }
}