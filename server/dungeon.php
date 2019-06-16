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

    public function getDungeon() {
        return $this->dungeon;
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

    private function setPosition($position) {

    }

    // for all move functions, return true if move was succesful

    // move up one row
    public function moveNorth($player) {
        $position = $player->getPosition();
        if(!isset($this->dungeon[$position[0]]->row[$position[1]--]->col[$position[2]])) {
            echo "INSIDE MOVENORTH ISSET\n";
            // remove that player from their current position
            unset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players[$player->getName()]);
            // add them to their new position
            array_push($this->dungeon[$position[0]]->row[$position[1]--]->col[$position[2]]->players, $player->getName());
            return true;
        }
        return false;
    }
    // move right one column
    public function moveEast($player) {
        $position = $player->getPosition();
        if(isset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]])) {
            unset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players[$player->getName()]);
            array_push($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players, $player->getName());
            return true;
        }
        return false;
    }

    public function moveSouth($player) {
        $position = $player->getPosition();
        if(isset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]])) {
            unset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players[$player->getName()]);
            array_push($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players, $player->getName());
            return true;
        }
        return false;
    }

    public function moveWest($player) {
        $position = $player->getPosition();
        if(isset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]])) {
            unset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players[$player->getName()]);
            array_push($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players, $player->getName());
            return true;
        }
        return false;
    }

    public function moveUp($player) {
        $position = $player->getPosition();
        if(isset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]])) {
            unset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players[$player->getName()]);
            array_push($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players, $player->getName());
            return true;
        }
        return false;
    }

    public function moveDown($player) {
        $position = $player->getPosition();
        if(isset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]])) {
            unset($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players[$player->getName()]);
            array_push($this->dungeon[$position[0]]->row[$position[1]]->col[$position[2]]->players, $player->getName());
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
