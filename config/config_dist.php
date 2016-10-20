<?php

return array(
    'host' => '127.0.0.1',
    'port' => '9501',
    'env' => 'dev', //prod || dev
    'swoole_config' => array(
        'open_eof_check' => true, //打开EOF检测
        'package_eof' => '\r\n\r\n',
        'open_eof_split' => true,
        'worker_num' => 8,
        'daemonize' => false,
        'max_request' => 10000,
        'dispatch_mode' => 3,
        'debug_mode' => 1,
        'task_worker_num' => 8,
    ),
    'db' => array(
        'dbname' => 'your_db',
        'user' => 'root',
        'password' => '',
        'host' => 'localhost',
        'driver' => 'pdo_mysql',
    )
);
