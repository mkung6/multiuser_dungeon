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
    private $connection;

    public function __construct($name, $position, $connection) {
        $this->name = $name;
        $this->position = $position;
        $this->connection = $connection;
    }

    public function setPosition($position) {
        $this->position = $position;
    }

    public function getName() {
        return $this->name;
    }

    public function getPosition() {
        return $this->position;
    }

    public function getConnection() {
        return $this->connection;
    }

}
?>
