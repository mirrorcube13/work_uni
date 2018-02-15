<?php

declare(strict_types = 1);

use PTS\Core\DB;
use PTS\Core\Route;

require_once 'autoload.php';
$config = require_once 'db_config.php';

DB::setConfig($config);
Route::run()->processRequest()->processResponse();
