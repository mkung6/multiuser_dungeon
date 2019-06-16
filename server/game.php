<?php
require  'vendor/autoload.php';
require_once 'dungeon.php';
require_once 'player.php';
use React\Socket\ConnectionInterface;

class Game {
    /** @var SplObjectStorage  */
    protected $connections;
    protected $dungeon;
    protected $players;

    public function __construct($dungeon)
    {
        $this->connections = new SplObjectStorage();
        $this->dungeon = $dungeon;
        $this->players = array();
    }

    public function addPlayer(ConnectionInterface $connection)
    {
        $connection->write("Enter your name: ");
        $this->initEvents($connection);
        $this->setConnectionData($connection, []);
    }

    public function getUserName() {
        return $this->userName;
    }

    /**
     * @param ConnectionInterface $connection
     */
    protected function initEvents(ConnectionInterface $connection)
    {
        // On receiving the data we loop through other connections
        // from the pool and write this data to them
        $connection->on('data', function ($data) use ($connection) {
            $connectionData = $this->getConnectionData($connection);
            // It is the first data received, so we consider it as
            // a users name.
            // connection data has format:
            // Array ( [name] -> 'userName' )
            print_r($connectionData);
            if(empty($connectionData)) {
                $this->addNewMember($data, $connection);
                return;
            }
            $name = $connectionData['name'];
            // create shallow copy of player, such that we don't modify it directly
            // until operation is complete
            $player = clone $this->players[$name];
            // $this->sendAll("$name: $data", $connection);
            $this->playerCommand($player, $data, $connection);
        });
        // When connection closes detach it from the pool
        $connection->on('close', function() use ($connection){
            $data = $this->getConnectionData($connection);
            $name = $data['name'] ?? '';
            $this->connections->offsetUnset($connection);
            $this->sendAll("User $name leaves the chat\n", $connection);
        });
    }

    /**
    *@param $player the current player obj that issued the command
    *@param $data the data (string) that was sent from client
    *Evaluate what the player wants to do, and perform logic based on that data
    */
    protected function playerCommand($player, $data, ConnectionInterface $connection) {
        $data = $this->takeCommand($data);
        switch(strtolower($data[0])) {
            case 'move':
                $this->movePlayer($player, $data[1], $connection);
                break;
            case 'say':
                $this->sayInRoom($player, $data[1], $connection);
                break;
            case 'tell':
                $this->privateMessage($player, $data[1]);
                break;
            case 'yell':
                $this->sendAll($player, $data[1], $connection);
                break;
            default:
                $connection->write("I do not understand. Please enter a command:\n");
        }
    }

    /*
    Split the user's input
    Where the first word should be the command, or the target they want to speak to
    The rest of the string being the direction or message, for example
    */
    protected function takeCommand($data) {
        $temp = explode(' ', $data);
        $command = $temp[0];
        unset($temp[0]);
        $data = implode(' ', $temp);
        return array( $command, $data );
    }

    protected function privateMessage($player, $data) {
        $data = $this->takeCommand($data);
        $target = $this->players[$data[0]];
        // users should only be able to speak to each other if they are in the same room
        if($target->getPosition() == $player->getPosition()) {
            $target->getConnection()->write($data[1]);
        }
    }

    protected function dontIncludeCurrentPlayer($player) {
        $players = $this->dungeon->getPlayersInRoom($player->getPosition());
        $key = array_search($player->getName(), $players);
        unset($players[$key]);
        return $players;
    }

    protected function sayInRoom($player, $data, ConnectionInterface $connection) {
        $playersInRoom = $this->dontIncludeCurrentPlayer($player);
        foreach($this->connections as $conn) {
            foreach($playersInRoom as $otherPlayer) {
                if($conn == $this->players[$otherPlayer]->getConnection()) {
                    $conn->write($data);
                }
            }
        }
    }

    /*
    If command was 'move', we see which direction the player wants to move in
    then perform the correct logic based on that direction
    if that location does not exist, move is illegal
    otherwise, move the player there, update their position in the dungeon as well
    as that player's position field, and display the room description
    TODO: try to refactor code here, looks repetitive
    */
    protected function movePlayer($player, $data, ConnectionInterface $connection) {
        $data = strtolower(str_replace(["\n", "\r"], "", $data));
        switch($data) {
            case "north":
                if($this->dungeon->moveNorth($player, $connection)) {
                    // get the current position
                    $position = $player->getPosition();
                    // modify it to the new position
                    $position[1] -= 1;
                    // modify the original player object to the new position
                    $this->players[$player->getName()]->setPosition($position);
                    // then describe the room that the player is now in
                    // pass player by reference, after having modified it
                    $this->describeRoom($this->players[$player->getName()], $connection);
                }
                else {
                    $connection->write("Illegal move. Enter a command:\n");
                }
                break;
            case "east":
                if($this->dungeon->moveEast($player, $connection)) {
                    $position = $player->getPosition();
                    $position[2] += 1;
                    $this->players[$player->getName()]->setPosition($position);
                    $this->describeRoom($this->players[$player->getName()], $connection);
                }
                else {
                    $connection->write("Illegal move. Enter a command:\n");
                }
                break;
            case 'south':
                if($this->dungeon->moveSouth($player, $connection)) {
                    $position = $player->getPosition();
                    $position[1] += 1;
                    $this->players[$player->getName()]->setPosition($position);
                    $this->describeRoom($this->players[$player->getName()], $connection);
                }
                else {
                    $connection->write("Illegal move. Enter a command:\n");
                }
                break;
            case 'west':
                if($this->dungeon->moveWest($player, $connection)) {
                    $position = $player->getPosition();
                    $position[2] -= 1;
                    $this->players[$player->getName()]->setPosition($position);
                    $this->describeRoom($this->players[$player->getName()], $connection);
                }
                else {
                    $connection->write("Illegal move. Enter a command:\n");
                }
                break;
            case 'up':
                if($this->dungeon->moveUp($player, $connection)) {
                    $position = $player->getPosition();
                    $position[0] += 1;
                    $this->players[$player->getName()]->setPosition($position);
                    $this->describeRoom($this->players[$player->getName()], $connection);
                }
                else {
                    $connection->write("Illegal move. Enter a command:\n");
                }
                break;
            case 'down':
                if($this->dungeon->moveDown($player, $connection)) {
                    $position = $player->getPosition();
                    $position[0] -= 1;
                    $this->players[$player->getName()]->setPosition($position);
                    $this->describeRoom($this->players[$player->getName()], $connection);
                }
                else {
                    $connection->write("Illegal move. Enter a command:\n");
                }
                break;
            default:
                $connection->write("Which direction did you want to move?\n");
        }
    }

    protected function describeRoom($player, $connection) {
        $connection->write($this->dungeon->getDescription($player->getPosition()));
        $playersInRoom = $this->dontIncludeCurrentPlayer($player);
        // display all players in room, except the current player
        // (they already know they're in there)
        if(empty($playersInRoom)) {
            $connection->write("There is nobody else here\n");
        }
        else {
            $connection->write("\nCurrent players in room: \n");
            foreach($playersInRoom as $otherPlayer) {
                $connection->write($otherPlayer . " ");
            }
            $connection->write("\n");
        }
    }

    protected function checkIsUniqueName($name)
    {
        foreach ($this->connections as $obj) {
            $data = $this->connections->offsetGet($obj);
            $takenName = $data['name'] ?? '';
            if($takenName == $name) return false;
        }
        return true;
    }

    protected function addNewMember($name, ConnectionInterface $connection)
    {
        $name = str_replace(["\n", "\r"], "", $name);
        if(!$this->checkIsUniqueName($name)) {
            $connection->write("Name $name is already taken!\n");
            $connection->write("Enter your name: ");
            return;
        }
        // store connection in a pool of connections so we can access it on event trigger
        $this->setConnectionData($connection, ['name' => $name]);
        $playerPosition = $this->dungeon->addNewPlayer($name);
        $player = new Player($name, $playerPosition, $connection);
        // store this new player obj in the players array, where their name is the key
        // and value is their player object
        $this->players[$name] = $player;
        // then describe their starting room
        $this->describeRoom($player, $connection);
        $connection->write("Commands: move, say, tell, yell.\nFor example 'move north'\n\nEnter a command:\n");
        // $this->sendAll("User $name joins the chat\n", $connection);

    }

    protected function setConnectionData(ConnectionInterface $connection, $data)
    {
        $this->connections->offsetSet($connection, $data);
    }

    protected function getConnectionData(ConnectionInterface $connection)
    {
        return $this->connections->offsetGet($connection);
    }

    protected function sendAll($player, $data, ConnectionInterface $except) {
        // $data here is what was typed in by the user
        foreach ($this->connections as $conn) {
            // send it to everyone except current connection (the user that sends it)
            if ($conn != $except) $conn->write($player->getName() . " yells " . strtoupper($data));
        }
    }
}

?>
