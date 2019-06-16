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
        $temp = explode(' ', $data);
        $command = $temp[0];
        unset($temp[0]);
        $data = implode(' ', $temp);
        switch(strtolower($command)) {
            case 'move':
                $this->movePlayer($player, $data, $connection);
                break;
            default:
                $connection->write("I do not understand. Please enter a command:\n");
        }
    }

    protected function movePlayer($player, $data, ConnectionInterface $connection) {
        $data = strtolower(str_replace(["\n", "\r"], "", $data));
        switch($data) {
            case "north":
                if($this->dungeon->moveNorth($player, $connection)) {
                    // get the current position
                    $position = $player.getPosition();
                    // modify it to the new position
                    $position[1]--;
                    // modify the original player object to the new position
                    $this->players[$player->getname()]->setPosition($position);
                }
                else {
                    $connection->write("Illegal move. Enter a command:\n");
                }
                break;
            case "east":
                if($this->dungeon->moveEast($player, $connection)) {
                }
                else {
                    $connection->write("Illegal move. Enter a command:\n");
                }
                break;
            case 'south':
                if($this->dungeon->moveSouth($player, $connection)) {

                }
                else {
                    $connection->write("Illegal move. Enter a command:\n");
                }
                break;
            case 'west':
                if($this->dungeon->moveWest($player, $connection)) {

                }
                else {
                    $connection->write("Illegal move. Enter a command:\n");
                }
                break;
            case 'up':
                if($this->dungeon->moveUp($player, $connection)) {

                }
                else {
                    $connection->write("Illegal move. Enter a command:\n");
                }
                break;
            case 'down':
                if($this->dungeon->moveDown($player, $connection)) {

                }
                else {
                    $connection->write("Illegal move. Enter a command:\n");
                }
                break;
            default:
                $connection->write("Which direction did you want to move?\n");
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
        $player = new Player($name, $playerPosition);
        // store this new player in the players array, where their name is the key
        // and value is their player object
        $this->players[$name] = $player;
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
    /**
     * Send data to all connections from the pool except
     * the specified one.
     *
     * @param mixed $data
     * @param ConnectionInterface $except
     */
    protected function sendAll($data, ConnectionInterface $except) {
        echo "INSIDE SENDALL";
        echo "data:\n";
        print_r($data);
        echo "end data\n";
        // $data here is what was typed in by the user
        foreach ($this->connections as $conn) {
            // send it to everyone except current connection (the user that sends it)
            if ($conn != $except) $conn->write($data);
        }
    }
}

?>
