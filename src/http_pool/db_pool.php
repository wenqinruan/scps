<?php

require __DIR__.'/../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;

$serv = new swoole_http_server('127.0.0.1', 9500);
$serv->set(array(
    'worker_num' => 10,
    'task_worker_num' => 2, //database connection pool
    'db_uri' => 'mysql:host=127.0.0.1;dbname=cp_test',
    'db_user' => 'root',
    'db_passwd' => '',
));
function my_onRequest_sync($req, $resp)
{
    global $serv;
    $result = $serv->taskwait('show tables');
    if ($result !== false) {
        $resp->end(var_export($result['data'], true));

        return;
    } else {
        $resp->status(500);
        $resp->end("Server Error, Timeout\n");
    }
}
function my_onTask($serv, $task_id, $from_id, $sql)
{
    static $link = null;
    if ($link == null) {
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array(
            'dbname' => 'cp_test',
            'user' => 'root',
            'password' => '',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        );
        $link = DriverManager::getConnection($connectionParams, $config);
        if (!$link) {
            $link = null;

            return array('data' => '', 'error' => 'connect database failed.');
        }
    }
    $result = $link->fetchAll("SELECT * FROM table1");
    if (!$result) {
        return array('data' => '', 'error' => 'query error');
    }

    return array('data' => $result);
}
function my_onFinish($serv, $data)
{
    echo 'AsyncTask Finish:Connect.PID='.posix_getpid().PHP_EOL;
}
$serv->on('Request', 'my_onRequest_sync');
$serv->on('Task', 'my_onTask');
$serv->on('Finish', 'my_onFinish');
$serv->start();
