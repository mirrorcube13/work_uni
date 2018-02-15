<?php

return $config = [
    'type' => 'mysql',
    'host' => 'localhost',
    'dbname' => 'task_4',
    'login' => 'mysql',
    'password' => 'mysql',
    'options' => [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
];
