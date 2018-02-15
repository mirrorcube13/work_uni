<?php

return $config = [
    'type' => 'mysql',
    'host' => 'localhost',
    'dbname' => 'task_4',
    'login' => 'root',
    'password' => '123456',
    'options' => [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
];
