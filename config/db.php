<?php

$dbSettings = require __DIR__ . '/db_settings.php';
return [
    'class' => 'yii\db\Connection',
    'dsn' => $dbSettings['database'],
    'username' => $dbSettings['login'],
    'password' => $dbSettings['password'],
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
