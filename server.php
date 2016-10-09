<?php

require __DIR__.'/vendor/autoload.php';

use Codeages\SCPS\ConnectionPool;

$config = require __DIR__.'/config/config.php';
$cp = new ConnectionPool($config);
