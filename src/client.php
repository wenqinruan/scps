<?php

$cli = new swoole_http_client('127.0.0.1', 9500);

$cli->setHeaders(['User-Agent' => "swoole"]);
$cli->post('/dump.php', array("test" => '9999999'), function (swoole_http_client $cli)
{
    echo "#{$cli->sock}\tPOST response Length: " . strlen($cli->body) . "\n";
    echo $cli->body . "\n";
});