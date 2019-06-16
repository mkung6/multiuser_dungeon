<?php

class Dungeon{
    // object containing the world data
    private $dungeon;
    /* startPosition array indeces:
    [0] = floor
    [1] = row
    [2] = column
    */
    private $startPosition;

    public function __construct($file){
        $this->dungeon = json_decode(file_get_contents($file));
        $this->setStartPosition();
    }

    protected function setStartPosition() {
        $currentFloor = 0;
        foreach($this->dungeon as $floor) {
            //iterate through each row
            for($i = 0; $i < count($floor->row); $i++) {
                //iterate through each column in that row
                for($j = 0; $j < count($floor->row[$i]->col); $j++) {
                    if(isset($floor->row[$i]->col[$j]->start)) {
                        $this->startPosition = array($currentFloor, $i, $j);
                    }
                }
            }
            $currentFloor++;
        }
    }

    public function getStartPosition() {
        return $this->startPosition;
    }

    // public function getDungeon() {
    //     return $this->dungeon;
    // }

    // get the description of a specific room
    public function getDescription($position) {
        return $this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->description;
    }

    // get all players currently in a specific room
    public function getPlayersInRoom($position) {
        return $this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players;
    }

    /*
    When a new player has entered the game, add them to the starting position
    and return that position, so that the game can keep track of their current position
    */
    public function addNewPlayer($name) {
        $startPosition = $this->getStartPosition();
        array_push($this->dungeon[$startPosition[0]]->row[$startPosition[1]]->col[$startPosition[2]]->players, $name);
        return $startPosition;
    }

    // for all move functions, return true if move was succesful
    // TODO: refactor move functions, reduce repetition and see if I can make it less wordy
    // i.e. see if unset and push can be put in its own functions

    // move up one row
    public function moveNorth($player) {
        $position = $player->getPosition();
        // check if move is legal if the players field exists there
        if(isset($this->dungeon[$position[0]]->row[$position[1] - 1]->col[$position[2]]->players)) {
            // remove that player from their current position
            // names are currently stored in players field as index number as key, with name as value
            $key = array_search($player->getName(), $this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players);
            //unset() deletes by key, not value
            unset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players[$key]);
            // add them to their new position
            array_push($this->dungeon[$position[0]]->row[$position[1] - 1]->col[$position[2]]->players, $player->getName());
            return true;
        }
        return false;
    }
    // move right one column
    public function moveEast($player) {
        $position = $player->getPosition();
        if(isset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2] + 1]->players)) {
            $key = array_search($player->getName(), $this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players);
            unset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players[$key]);
            array_push($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2] + 1]->players, $player->getName());
            return true;
        }
        return false;
    }
    // move down one row
    public function moveSouth($player) {
        $position = $player->getPosition();
        if(isset($this->dungeon[$position[0]]->row[$position[1] + 1]->col[$position[2]]->players)) {
            $key = array_search($player->getName(), $this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players);
            unset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players[$key]);
            array_push($this->dungeon[$position[0]]->row[$position[1] + 1]->col[$position[2]]->players, $player->getName());
            return true;
        }
        return false;
    }
    // move left one column
    public function moveWest($player) {
        $position = $player->getPosition();
        if(isset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2] - 1]->players)) {
            $key = array_search($player->getName(), $this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players);
            unset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players[$key]);
            array_push($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2] - 1]->players, $player->getName());
            return true;
        }
        return false;
    }
    // move up one floor
    public function moveUp($player) {
        $position = $player->getPosition();
        if(isset($this->dungeon[$position[0] + 1]->row[$position[1]]->col[$position[2]]->players)) {
            $key = array_search($player->getName(), $this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players);
            unset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players[$key]);
            array_push($this->dungeon[$position[0] + 1]->row[$position[1]]->col[$position[2]]->players, $player->getName());
            return true;
        }
        return false;
    }
    // move down one floor
    public function moveDown($player) {
        $position = $player->getPosition();
        if(isset($this->dungeon[$position[0] - 1]->row[$position[1]]->col[$position[2]]->players)) {
            $key = array_search($player->getName(), $this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players);
            unset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players[$key]);
            array_push($this->dungeon[$position[0] - 1]->row[$position[1]]->col[$position[2]]->players, $player->getName());
            return true;
        }
        return false;
    }
}

// $start_position = array();
// function initialize_dungeon(){
//     // HARD CODED FOR NOW
//     $FILE = '../dungeon/dungeon01.json';
//
//     // $FILE = readline('Enter a dungeon file: ');
//     $data = file_get_contents($FILE);
//     $dungeon = json_decode($data);
    // $current_floor = 0;
    // global $start_position;
    // foreach($dungeon as $floor) {
    //     //iterate through each row
    //     for($i = 0; $i < count($floor->row); $i++) {
    //         //iterate through each column in that row
    //         for($j = 0; $j < count($floor->row[$i]->col); $j++) {
    //             if(isset($floor->row[$i]->col[$j]->start)) {
    //                 $start_position = array($current_floor, $i, $j);
    //             }
    //         }
    //     }
    //     $current_floor++;
    // }
// }

 ?>
