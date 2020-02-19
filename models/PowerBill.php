<?php


namespace app\models;


use Yii;
use yii\base\Model;

class PowerBill extends Model
{
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_EDIT = 'edit';


    public $limit;
    public $cost;
    public $overcost;
    public $oldData;
    public $newData;
    public $cottageId;
    public $period;
    public $payer;
    public $no_limit;
    public $countedSumm;


    public function __construct()
    {
        parent::__construct();
        // прочитаю настройки из файла
        $file = dirname($_SERVER['DOCUMENT_ROOT'] . './/') . '/settings/power_settings';
        if (!is_file($file)) {
            // создаю файл
            file_put_contents($file, "0\n0\n0");
        }
        $content = file_get_contents($file);
        $settingsArray = mb_split("\n", $content);
        $this->limit = $settingsArray[0];
        $this->cost = CashHandler::toFloat($settingsArray[1]);
        $this->overcost = CashHandler::toFloat($settingsArray[2]);
    }

    /**
     * @return array
     */
    public function scenarios():array
    {
        return [
            self::SCENARIO_CREATE => ['cottageId', 'oldData', 'newData', 'limit', 'cost', 'overcost', 'period', 'payer', 'no_limit', 'countedSumm'],
            self::SCENARIO_EDIT => ['limit', 'cost', 'overcost'],
        ];
    }

    public function attributeLabels():array
    {
        return [
            'limit' => 'Лимит льготного потребления',
            'cost' => 'Стоимость электроэнергии в пределах льготного лимита',
            'overcost' => 'Стоимость электроэнергии за пределами льготного лимита',
            'payer' => 'Плательщик',
            'oldData' => 'Старые показания счётчика',
            'newData' => 'Новые показания счётчика',
            'period' => 'Месяц оплаты',
            'no_limit' => 'Игнорировать льготный лимит',
            'countedSumm' => 'Стоимость',
        ];
    }


    /**
     * @return array
     */
    public function rules():array
    {
        return [
            // name, email, subject и body атрибуты обязательны
            [['limit', 'cost', 'overcost'], 'required'],
            [['oldData', 'newData'], 'integer'],
        ];
    }

    /**
     * @return array
     */
    public function saveSettings(): array
    {
        $file = dirname(Yii::getAlias('@webroot') . './/') . '/settings/power_settings';
        $cost = CashHandler::toDBCash($this->cost);
        $overcost = CashHandler::toDBCash($this->overcost);
        file_put_contents($file, "{$this->limit}\n{$cost}\n{$overcost}");
        Yii::$app->session->addFlash('success', 'Данные по электроэнергии обновлены.');
        return ['status' => 1];
    }

    public function save()
    {
        $oldData = $this->oldData;
        $newData = $this->newData;
        $diff = $newData - $oldData;
        // посчитаю стоимость и сравню с посчитанной скриптом
        $details = "Электроэнергия: Период: {$this->period}; Израсходовано: $diff квт.ч;";
        if($this->no_limit){
            $cost = CashHandler::toDBCash($diff * $this->overcost);
        }
        else{
            $limit = $this->limit;
            if($diff > $limit){
                $smoothInLimitCost = CashHandler::toSmooth($limit * $this->cost * 100);
                $details .= " Льготно: {$limit} квт.ч. * {$this->cost} р = $smoothInLimitCost";
                $overLimit = $diff - $limit;
                $smoothOverLimitCost = CashHandler::toSmooth($overLimit * $this->overcost * 100);
                $details .= " Сверх: {$overLimit} квт.ч. * {$this->overcost} р = $smoothOverLimitCost";
                $cost = $limit * $this->cost + $overLimit * $this->overcost;
                $smoothCost = CashHandler::toSmooth($cost * 100);
                $details .= "Итого: $smoothCost";
            }
            else{
                $cost = CashHandler::toDBCash($diff * $this->cost);
                $details .= " Льготно: {$diff} * {$this->cost} = " . CashHandler::toSmooth($cost) . "";
            }
        }
        echo $details;
        die;
    }
}