<?php


namespace app\models\utils;


use Yii;
use yii\base\Model;
use yii\db\Exception;

class DbRestore extends Model
{
    public $file;

    public function rules(): array
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false],
            [['file'], 'required'],
        ];
    }

    public function restore()
    {
        if($this->validate()){
            $file = $this->file->tempName;
            $fileName = dirname($_SERVER['DOCUMENT_ROOT'] . './/') . '/backup/db.restore.sql';
            file_put_contents($fileName, file_get_contents($file));
            $dbSettings = Misc::getDbSettings();
            $cmd  = 'c: & cd "' . $dbSettings->mysqlPath . '" & mysql --user=' . $dbSettings->login . ' --password=' . $dbSettings->pass . ' < ' . $fileName;
            Yii::$app->session->addFlash('success', 'База данных восстановлена!');
            exec($cmd);
        }
    }
}