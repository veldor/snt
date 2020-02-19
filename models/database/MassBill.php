<?php


namespace app\models\database;


use app\models\CashHandler;
use Throwable;
use Yii;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * Class MassBill
 * @package app\models\database
 *
 * @property int $id [int(10) unsigned]
 * @property string $type [enum('membership', 'target')]
 * @property string $details
 */

class MassBill extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName():string
    {
        return 'mass_bills';
    }

    /**
     * @return MassBill[]
     */
    public static function getAll()
    {
        return self::find()->all();
    }

    public static function deleteBills()
    {
        $id = trim(Yii::$app->request->post('id'));
        if(empty($id)){
            return ['message' => 'Не найден идентификатор выставления счето'];
        }
        $billing = self::findOne($id);
        if(empty($billing)){
            return ['message' => 'Выставление счетов не найдено, возможно, уже удалено'];
        }
        // найду сообщения из этой рассылки
        $messages = MailingSchedule::getBillingMessages($billing);
        if(!empty($messages)){
            foreach ($messages as $message) {
                try {
                    $message->delete();
                } catch (StaleObjectException $e) {
                } catch (Throwable $e) {
                    return ['message' => 'Ошибка удаления сообщения из очереди'];
                }
            }
        }
        try {
            $billing->delete();
        } catch (StaleObjectException $e) {
        } catch (Throwable $e) {
            return ['message' => 'Ошибка удаления счетов'];
        }
        return ['status' => 1];
    }


}