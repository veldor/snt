<?php
// прочитаю настройки из файла
$file = dirname($_SERVER['DOCUMENT_ROOT'] . './/') . '/settings/db_settings';
if (!is_file($file)) {
    // создаю файл
    file_put_contents($file, "test\ntest\ntest");
}
$content = file_get_contents($file);
$settingsArray = mb_split("\n", $content);

return ['database' => $settingsArray[0], 'login' => $settingsArray[1], 'password' => $settingsArray[2]];