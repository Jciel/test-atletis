<?php

$host = getenv('DB_HOST') ?: throw new RuntimeException('DB_HOST não configurado');
$port = getenv('DB_PORT') ?: throw new RuntimeException('DB_PORT não configurado');
$dbname = getenv('DB_DATABASE') ?: throw new RuntimeException('DB_DATABASE não configurado');
$username = getenv('DB_USERNAME') ?: throw new RuntimeException('DB_USERNAME não configurado');
$password = getenv('DB_PASSWORD') ?: throw new RuntimeException('DB_PASSWORD não configurado');
$dbconnection = getenv('DB_CONNECTION') ?: throw new RuntimeException('DB_CONNECTION não configurado');

return [
    'class' => \yii\db\Connection::class,
    'dsn' => "{$dbconnection}:host={$host};dbname={$dbname}",
    'username' => $username,
    'password' => $password,
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
