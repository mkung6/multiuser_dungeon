<?php

require 'vendor/autoload.php';
use React\Socket\ConnectionInterface;
require 'dungeon.php';
require 'game.php';

$file = '../dungeon/dungeon01.json';
//$FILE = readline('Enter a dungeon file: ');
$dungeon = new Dungeon($file);

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server('127.0.0.1:8080', $loop);
$game = new Game($dungeon);
$socket->on('connection', function(ConnectionInterface $connection) use ($game){
    // $connection->on('data', function($data) use ($connection){
    //     $name = $data;
    //     $connection->write("Welcome, " . $name);
    // });
    $game->addPlayer($connection);
});
echo "Listening on {$socket->getAddress()}\n";
$loop->run();

 ?>
