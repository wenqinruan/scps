<?php

namespace Codeages\SCPS;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Doctrine\DBAL\DriverManager;

class ConnectionPool
{
    private $serv;
    private $logger;
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->logger = new Logger('SCPS');
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../logs/swoole.log', Logger::INFO));

        $this->serv = new \swoole_server($config['host'], $config['port']);
        $this->serv->set($config['swoole_config']);

        $this->serv->on('Start', array($this, 'onStart')); //主进程启动时
        $this->serv->on('WorkerStart', array($this, 'onWorkerStart')); //worker进程/task进程启动时
        $this->serv->on('Connect', array($this, 'onConnect')); //有新的连接进入时
        $this->serv->on('Receive', array($this, 'onReceive')); //接收到数据时
        $this->serv->on('Close', array($this, 'onClose')); //客户端连接关闭后
        $this->serv->on('Task', array($this, 'onTask')); //开始任务
        $this->serv->on('Finish', array($this, 'onFinish')); //任务完成
        $this->serv->start();
    }

    public function onStart($serv)
    {
        $this->logger->info('DB Connection Pool Server Start.');
    }

    public function onWorkerStart($serv, $worker_id)
    {
        if ($worker_id >= $this->config['swoole_config']['worker_num']) {
            $this->logger->info("Tasker start #{$worker_id}");
        } else {
            $this->logger->info("Worker start #{$worker_id}");
        }
    }

    public function onConnect($serv, $fd, $from_id)
    {
        $this->logger->info("Client {$fd} connect, from {$from_id}");
    }

    public function onReceive(\swoole_server $serv, $fd, $from_id, $data)
    {
        if ($this->config['env'] == 'dev') {
            $this->logger->info("Receive message from {$from_id}, fd {$fd}", array($data));
        } else {
            $this->logger->info("Receive message from {$from_id}, fd {$fd}");
        }
        

        $result = $serv->taskwait($data, 10, $fd%$this->config['swoole_config']['task_worker_num']);
        $serv->send($fd, json_encode($result));
    }
    public function onClose($serv, $fd, $from_id)
    {
        $this->logger->info("Client {$fd} close connection");
    }

    public function onTask($serv, $task_id, $from_id, $data)
    {
        $this->logger->info("Execute task {$task_id}");

        static $link = null;
        if ($link == null) {
            $config = new \Doctrine\DBAL\Configuration();
            $link = DriverManager::getConnection($this->config['db'], $config);
            if (!$link) {
                $link = null;

                return array('data' => '', 'error' => 'connect database failed.');
            }
        }

        $encodeData = json_decode(rtrim($data, $this->config['swoole_config']['package_eof']), true);
        $method = $encodeData['method'];
        $args = $encodeData['args'];

        $result = DBALCaller::call($link, $method, $args);

        return array('data' => $result);
    }

    public function onFinish($serv, $task_id, $data)
    {
    }
}
