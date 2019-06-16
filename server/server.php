<?php

require 'vendor/autoload.php';
use React\Socket\ConnectionInterface;
require 'dungeon.php';
require 'game.php';

$file = readline('Enter a dungeon file: ');
// initialize dungeon first, so we can choose specific dungeon files
$dungeon = new Dungeon($file);

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server('127.0.0.1:8080', $loop);
$game = new Game($dungeon);
$socket->on('connection', function(ConnectionInterface $connection) use ($game){
    $game->addPlayer($connection);
});
echo "Listening on {$socket->getAddress()}\n";
$loop->run();

 ?>
