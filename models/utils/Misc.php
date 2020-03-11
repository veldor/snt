<?php


namespace app\models\utils;


use app\models\database\Cottage;
use app\models\database\Mail;
use app\models\database\Payer;
use app\models\database\Phone;
use app\models\selections\DbSettings;

class Misc
{

    /**
     * @return DbSettings
     */
    public static function getDbSettings(): DbSettings
    {
        $file = dirname($_SERVER['DOCUMENT_ROOT'] . './/') . '/settings/db_settings';
        if (!is_file($file)) {
            // создаю файл
            file_put_contents($file, "test\ntest\ntest");
        }
        $content = file_get_contents($file);
        $settingsArray = mb_split("\n", $content);
        $settings = new DbSettings();
        $settings->dbName = $settingsArray[0];
        $settings->login = $settingsArray[1];
        $settings->pass = $settingsArray[2];
        $settings->mysqlPath = $settingsArray[3];
        $settings->database = $settingsArray[4];
        return $settings;
    }

    /**
     * @return string
     */
    public static function backupDatabase(): string
    {
        $backupName = self::getDbBackupPath();
        $dbSettings = Misc::getDbSettings();
        $cmd  = 'c: & cd "' . $dbSettings->mysqlPath . '" & mysqldump --user=' . $dbSettings->login . ' --password=' . $dbSettings->pass . ' --host=localhost  --add-drop-table --databases ' . $dbSettings->database . ' > "' . $backupName . '"';
        exec($cmd);
        return $backupName;
    }

    /**
     * @return string
     */
    public static function getDbBackupPath(): string
    {
        return dirname($_SERVER['DOCUMENT_ROOT'] . './/') . '/backup/db.backup.sql';
    }

    public static function getRegisterPath()
    {
        $fileName = dirname($_SERVER['DOCUMENT_ROOT'] . './/') . '/backup/register.xml';
        $xml = '<?xml version="1.0" encoding="utf-8"?><участки>';
        // сформирую файл
        $cottages = Cottage::getCottages();
        foreach ($cottages as $cottage) {
            $xml .= '<участок>';
            $xml .= "<номер>$cottage->num</номер>";
            $xml .= "<членство>$cottage->membership</членство>";
            $xml .= "<права>$cottage->rigths</права>";
            $xml .= "<площадь>$cottage->square</площадь>";
            // найду плательщиков
            $xml .= '<контакты>';
            $contacts = Payer::getCottagePayers($cottage);
            if(!empty($contacts)){
                foreach ($contacts as $contact) {
                    $xml .= "ФИО: $contact->fio \n";
                    $xml .= "Адрес: $contact->address \n";
                    $xml .= "Доля собственности: $contact->part \n";
                    $xml .= " \n";
                }
            }
            $xml .= '</контакты>';
            // найду телефоны
            $xml .= '<телефоны>';
            $phones = Phone::getCottagePhones($cottage);
            if(!empty($phones)){
                foreach ($phones as $phone) {
                    $xml .= "ФИО: $phone->fio \n";
                    $xml .= "Адрес: $phone->phone \n";
                    $xml .= " \n";
                }
            }
            $xml .= '</телефоны>';
            $xml .= '<электонная_почта>';
            // найду плательщиков
            $mails = Mail::getCottageMails($cottage);
            if(!empty($mails)){
                foreach ($mails as $mail) {
                    $xml .= "ФИО: $mail->fio \n";
                    $xml .= "Адрес: $mail->email \n";
                    $xml .= " \n";
                }
            }
            $xml .= '</электонная_почта>';
            $xml .= '</участок>';
        }
        $xml .= '</участки>';
        file_put_contents($fileName, $xml);
        return $fileName;
    }
}