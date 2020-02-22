<?php


namespace app\models\utils;


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
}