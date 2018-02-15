<?php

function __autoload(string $class): void{
    $namespace = explode('\\',$class);
    array_shift($namespace);
    $classPath = __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $namespace) . '.php';

    if (file_exists($classPath)) {
        require_once $classPath;
    }
}
