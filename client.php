<?php
$client = new \swoole_client(SWOOLE_TCP);
if (!$client->connect('127.0.0.1', 9501, -1)) {
    exit("connect failed. Error: {$client->errCode}\n");
}
$data = array(
    'method' => 'executeQuery',
    'args' => array('SELECT * FROM USER', array(0 => 'course'), array(), null),
);
$client->send(json_encode($data).'\r\n\r\n');
$data = $client->recv() . "\n";
$data = json_decode($data, true);
var_dump($data);
$client->close();
