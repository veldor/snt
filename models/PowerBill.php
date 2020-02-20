<?php


namespace app\models;


use app\models\database\Bill;
use app\models\database\Cottage;
use app\models\database\Payer;
use DOMElement;
use Exception;
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
     * @param Bill $bill
     * @return string
     */
    public static function getDescription(Bill $bill): string
    {
        try {
            $info = new DOMHandler($bill->bill_destination);
            /** @var DOMElement $data */
            $data = $info->query('/pay/power');
            $attributes = DOMHandler::getElemAttributes($data[0]);
            $noLimit = $attributes['ignore-limit'];
            $diff = $attributes['new-data'] - $attributes['old-data'];
            if ($noLimit) {
                $details = "$diff * {$attributes['overcost']}";
            } else if ($diff < $attributes['limit']) {
                $details = "$diff * {$attributes['cost']}";
            } else {
                $over = $diff - $attributes['limit'];
                $details = "{$attributes['limit']}квт*{$attributes['cost']}; {$over}квт*{$attributes['overcost']}";
            }
            $description = urldecode($attributes['period']) . "; $details; пок.сч. {$attributes['new-data']}";
            return $description;
        } catch (Exception $e) {
            return $bill->bill_destination;
        }
    }

    /**
     * @param Bill $bill
     * @return bool|null
     */
    public static function isTemplated(Bill $bill): ?bool
    {
        try {
            new DOMHandler($bill->bill_destination);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param Bill $bill
     * @return string
     */
    public static function getDetailsTable(Bill $bill): ?string
    {
        try {
            $info = new DOMHandler($bill->bill_destination);
            /** @var DOMElement $data */
            $data = $info->query('/pay/power');
            $attributes = DOMHandler::getElemAttributes($data[0]);

            $noLimit = $attributes['ignore-limit'];
            $diff = $attributes['new-data'] - $attributes['old-data'];
            if ($noLimit) {
                $details = "<tr><td>Сверх нормы</td><td>$diff  кВт⋅ч * {$attributes['overcost']} ₽</td></tr>";
            } else if ($diff < $attributes['limit']) {
                $details = "<tr><td>Льготный тариф</td><td>$diff  кВт⋅ч * {$attributes['cost']} ₽</td></tr>";
            } else {
                $over = $diff - $attributes['limit'];
                $details = "<tr><td>Льготный тариф</td><td>$diff * {$attributes['cost']} ₽</td></tr><tr><td>Сверх нормы</td><td>$over * {$attributes['overcost']} ₽</td></tr>";
            }
            $text = "
            <table class='table table-striped'><tbody>
                <tr>
                    <td>Тип</td>
                    <td>Электроэнергия</td>
                </tr>
                <tr>
                    <td>Период</td>
                    <td>" . urldecode($attributes['period']) . "</td>
                </tr>
                <tr>
                    <td>Предыдущие показания</td>
                    <td>{$attributes['old-data']} кВт⋅ч</td>
                </tr>
                <tr>
                    <td>Новые показания</td>
                    <td>{$attributes['new-data']} кВт⋅ч</td>
                </tr>
                <tr>
                    <td>Потрачено за месяц</td>
                    <td>{$diff} кВт⋅ч</td>
                </tr>
                <tr>
                    <td>Общая стоимость</td>
                    <td>" . CashHandler::toSmooth($bill->amount) . "</td>
                </tr>
                $details
           </tbody> </table>
            ";
            return $text;
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        return [
            self::SCENARIO_CREATE => ['cottageId', 'oldData', 'newData', 'limit', 'cost', 'overcost', 'period', 'payer', 'no_limit', 'countedSumm'],
            self::SCENARIO_EDIT => ['limit', 'cost', 'overcost'],
        ];
    }

    public function attributeLabels(): array
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
    public function rules(): array
    {
        return [
            [['limit', 'cost', 'overcost', 'period'], 'required'],
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

    /**
     * @return array
     */
    public function save(): array
    {
        $cottage = Cottage::findOne($this->cottageId);
        if (empty($cottage)) {
            die('Не найден участок');
        }
        $payer = Payer::findOne($this->payer);
        if (empty($payer)) {
            die('Не найден плательщик');
        }
        $oldData = $this->oldData;
        $newData = $this->newData;
        $diff = $newData - $oldData;
        // посчитаю стоимость и сравню с посчитанной скриптом
        if ($this->no_limit) {
            $cost = CashHandler::toDBCash($diff * $this->overcost);
        } else {
            $limit = $this->limit;
            if ($diff > $limit) {
                $overLimit = $diff - $limit;
                $cost = CashHandler::toDBCash($limit * $this->cost + $overLimit * $this->overcost);
            } else {
                $cost = CashHandler::toDBCash($diff * $this->cost);
            }
        }
        /** @noinspection UnknownInspectionInspection */
        /** @noinspection HtmlUnknownAttribute */
        $xml = "<pay><power period='" . urlencode($this->period) . "' old-data='{$this->oldData}' new-data='{$this->newData}' ignore-limit='$this->no_limit' limit='{$this->limit}' cost='{$this->cost}' overcost='$this->overcost'/></pay>";

        $bill = new Bill(['scenario' => Bill::SCENARIO_CREATE]);
        $bill->amount = $cost;
        $bill->cottage = $this->cottageId;
        $bill->cottageNumber = $cottage->num;
        $bill->payer = $payer->fio;
        $bill->payer_address = $payer->address;
        $bill->service_name = 'power';
        $bill->create_date = time();
        $bill->bill_destination = $xml;
        $bill->save();
        return ['status' => 1];
    }
}