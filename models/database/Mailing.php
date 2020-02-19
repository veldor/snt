<?php


namespace app\models\database;


use app\models\DOMHandler;
use Throwable;
use Yii;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * Class Mailing
 * @package app\models\database
 *
 * @property int $id [int(10) unsigned]
 * @property string $title
 * @property string $body
 * @property string $mails_info
 */

class Mailing extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName():string
    {
        return 'mailings';
    }

    /**
     * @return Mailing[]
     */
    public static function getAll()
    {
        return self::find()->all();
    }

    /**
     * @return array
     */
    public static function deleteMailing(): array
    {
        $id = trim(Yii::$app->request->post('id'));
        if(empty($id)){
            return ['message' => 'Не найден идентификатор рассылки'];
        }
        $mailing = self::findOne($id);
        if(empty($mailing)){
            return ['message' => 'Рассылка не найдена, возможно, уже удалена'];
        }
        // найду сообщения из этой рассылки
        $messages = MailingSchedule::getMailingMessages($mailing);
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
            $mailing->delete();
        } catch (StaleObjectException $e) {
        } catch (Throwable $e) {
            return ['message' => 'Ошибка удаления рассылки'];
        }
        return ['status' => 1];
    }

    public static function getMailingInfo($id)
    {
        $answer = "<table class='table table-condensed table-striped table-hover'><thead><tr><th>Номер участка</th><th>Адрес почты</th><th>Статус</th></tr></thead>";
        $mailing = self::findOne($id);
        if(!empty($mailing)){
            if(!empty($mailing->mails_info)){
                $dom = new DOMHandler($mailing->mails_info);
                $mails = $dom->query('/mails/mail');
                if(!empty($mails)){
                    /** @var \DOMElement $mail */
                    foreach ($mails as $mail) {
                        // найду сведения о почте
                        $mailInfo = Mail::findOne($mail->getAttribute('id'));
                        if(empty($mailInfo)){
                            $answer .= '<tr><td colspan="3">Сведения об электронной почты удалены</td></tr>';
                        }
                        else{
                            // проверю, не висит ли письмо в списке ожидающих отправки
                            $waiting = MailingSchedule::find()->where(['mailingId' => $mailing->id, 'mailId' => $mailInfo->id])->count();
                            $cottage = Cottage::findOne($mailInfo->cottage_num);
                            if(empty($cottage)){
                                die('ошибка с участком');
                            }
                            $answer .= "<tr><td>{$cottage->num}</td><td>{$mailInfo->email}</td><td>" . ($waiting ? '<b class="text-danger">ожидает отправки</b>' : '<b class="text-success">отправлено</b>') . '</td></tr>';
                        }
                    }
                }
            }
        }
        $answer .= '</table>';
        return $answer;
    }
}