<?php
class Player {
    private $name;
    /*
    Position array indeces correspond to:
    [0] = floor
    [1] = row
    [2] = column
    */
    private $position;

    public function __construct($name, $position) {
        $this->name = $name;
        $this->position = $position;
    }

    public function setPosition($position) {
        $this->position = $position;
    }

    public function getPosition() {
        return $this->position;
    }

    public function getName() {
        return $this->name;
    }
}
?>
