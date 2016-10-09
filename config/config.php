<?php

return array(
    'host' => '127.0.0.1',
    'port' => '9501',
    'swoole_config' => array(
        'worker_num' => 8,
        'daemonize' => false,
        'max_request' => 10000,
        'dispatch_mode' => 3,
        'debug_mode' => 1,
        'task_worker_num' => 8,
    ),
    'db' => array(
        'dbname' => 'cp_test',
        'user' => 'root',
        'password' => '',
        'host' => 'localhost',
        'driver' => 'pdo_mysql',
    )
);
