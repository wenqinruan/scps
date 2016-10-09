<?php
$client = new \swoole_client(SWOOLE_SOCK_TCP);
if (!$client->connect('127.0.0.1', 9501, -1)) {
    exit("connect failed. Error: {$client->errCode}\n");
}
$data = array(
    'method' => 'fetchAll',
    'args' => array('select * from table1'),
);
$client->send(json_encode($data));
$data = $client->recv() . "\n";
$data = json_decode($data, true);
var_dump($data);
$client->close();