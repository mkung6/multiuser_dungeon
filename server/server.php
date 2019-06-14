<?php

require 'vendor/autoload.php';
use React\Socket\ConnectionInterface;

initialize_dungeon();
echo "STARTING POSITION: \n";
print_r($start_position);
$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server('127.0.0.1:8080', $loop);
$pool = new ConnectionsPool();
$socket->on('connection', function(ConnectionInterface $connection) use ($pool){
    $pool->add($connection);
});
echo "Listening on {$socket->getAddress()}\n";
$loop->run();
 ?>
