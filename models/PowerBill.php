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
            self::SCENARIO_CREATE => ['cottageId', 'oldData', 'newData', 'limit', 'cost', 'overcost', 'period', 'payer', 'no_limit'],
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
}