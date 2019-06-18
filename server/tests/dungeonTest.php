<?php

use PHPUnit\Framework\TestCase;
include 'dungeon.php';
include 'player.php';

class testDungeon extends TestCase {
    //mock the dungeon floor plan

    // setStartPosition will fail if there's no "start" field in the JSON file

    /**
    * @dataProvider providerTestStartPosition
    */
    public function testStartPosition($file, $expectedResult) {
        $dungeon = new Dungeon($file);
        $result = $dungeon->getStartPosition();
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestStartPosition() {
        return array(
            array("./tests/testDungeons/testDungeon01.json", array(0,0,0)),
            array("./tests/testDungeons/testDungeon02.json", array(1,0,0)),
            array("./tests/testDungeons/testDungeon03.json", array(1,1,1))
        );
    }

    public function testPlayersInRoom() {
        // create dungeon and player
        $file = "./tests/testDungeons/testDungeon01.json";
        $dungeon = new Dungeon($file);
        $nameOne = "testPlayer01";
        $position = array(0,0,0);
        $playerOne = new Player($nameOne, $position, '');
        $dungeon->addNewPlayer($nameOne);
        $playersInRoomResult = $dungeon->getPlayersInRoom($position);
        // that player should be in the room
        $this->assertEquals($playersInRoomResult, array($nameOne));
        // add a second player to the dungeon
        $nameTwo = "testPlayer02";
        $playerTwo = new Player($nameTwo, $position, '');
        $dungeon->addNewPlayer($nameTwo);
        $playersInRoomResult = $dungeon->getPlayersInRoom($position);
        // player1 and player2 should be in the room now
        $this->assertEquals($playersInRoomResult, array($nameOne, $nameTwo));
        // remove the first player
        $dungeon->removePlayerFromPosition($playerOne, $position);
        $playersInRoomResult = $dungeon->getPlayersInRoom($position);
        // only second player should be in room now
        $this->assertEquals($playersInRoomResult, array(1 => $nameTwo));
        // remove the second player from room
        $dungeon->removePlayerFromPosition($playerTwo, $position);
        $playersInRoomResult = $dungeon->getPlayersInRoom($position);
        // room should be empty now
        $this->assertEquals($playersInRoomResult, array());
    }

    /**
    * @dataProvider providerTestRoomDescription
    */
    public function testRoomDescription($file, $position, $expectedResult) {
        $dungeon = new Dungeon($file);
        $result = $dungeon->getDescription($position);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestRoomDescription() {
        return array(
            array("./tests/testDungeons/testDungeon01.json", array(0,0,0), "this is the starting room"),
        );
    }

    public function testMoveNorth() {
        // create a dungeon where we can't move to any other rooms
        $file = "./tests/testDungeons/testDungeon01.json";
        $dungeon = new Dungeon($file);
        // add a player to that dungeon
        $name = "testPlayer";
        $player = new Player($name, $dungeon->getStartPosition(), '');
        $dungeon->addNewPlayer($player);
        // player should not be able to move
        $this->assertFalse($dungeon->moveNorth($player));
        // create a new dungeon where movement is possible
        $file = "./tests/testDungeons/testDungeon03.json";
        $dungeon = new Dungeon($file);
        // add player to that dungeon
        $player = new Player($name, $dungeon->getStartPosition(), '');
        $dungeon->addNewPlayer($name);
        // verfiy that they can make the move
        $this->assertTrue($dungeon->moveNorth($player));
        // check that the starting room is now empty
        $result = $dungeon->getPlayersInRoom($dungeon->getStartPosition());
        $this->assertEquals($result, array());
        // verfiy that the new room they are in now holds the player
        $newPosition = $dungeon->getStartPosition();
        $newPosition[1] = $newPosition[1] - 1;
        $result = $dungeon->getPlayersInRoom($newPosition);
        $this->assertEquals($result, array($name));
    }

    public function testMoveEast() {
        $file = "./tests/testDungeons/testDungeon01.json";
        $dungeon = new Dungeon($file);
        $name = "testPlayer";
        $player = new Player($name, $dungeon->getStartPosition(), '');
        $dungeon->addNewPlayer($player);
        $this->assertFalse($dungeon->moveEast($player));
        $file = "./tests/testDungeons/testDungeon03.json";
        $dungeon = new Dungeon($file);
        $player = new Player($name, $dungeon->getStartPosition(), '');
        $dungeon->addNewPlayer($name);
        $this->assertTrue($dungeon->moveEast($player));
        $result = $dungeon->getPlayersInRoom($dungeon->getStartPosition());
        $this->assertEquals($result, array());
        $newPosition = $dungeon->getStartPosition();
        $newPosition[2] = $newPosition[2] + 1;
        $result = $dungeon->getPlayersInRoom($newPosition);
        $this->assertEquals($result, array($name));
    }

    public function testMoveSouth() {
        $file = "./tests/testDungeons/testDungeon01.json";
        $dungeon = new Dungeon($file);
        $name = "testPlayer";
        $player = new Player($name, $dungeon->getStartPosition(), '');
        $dungeon->addNewPlayer($player);
        $this->assertFalse($dungeon->moveSouth($player));
        $file = "./tests/testDungeons/testDungeon03.json";
        $dungeon = new Dungeon($file);
        $player = new Player($name, $dungeon->getStartPosition(), '');
        $dungeon->addNewPlayer($name);
        $this->assertTrue($dungeon->moveSouth($player));
        $result = $dungeon->getPlayersInRoom($dungeon->getStartPosition());
        $this->assertEquals($result, array());
        $newPosition = $dungeon->getStartPosition();
        $newPosition[1] = $newPosition[1] + 1;
        $result = $dungeon->getPlayersInRoom($newPosition);
        $this->assertEquals($result, array($name));
    }

    public function testMoveWest() {
        $file = "./tests/testDungeons/testDungeon01.json";
        $dungeon = new Dungeon($file);
        $name = "testPlayer";
        $player = new Player($name, $dungeon->getStartPosition(), '');
        $dungeon->addNewPlayer($player);
        $this->assertFalse($dungeon->moveWest($player));
        $file = "./tests/testDungeons/testDungeon03.json";
        $dungeon = new Dungeon($file);
        $player = new Player($name, $dungeon->getStartPosition(), '');
        $dungeon->addNewPlayer($name);
        $this->assertTrue($dungeon->moveWest($player));
        $result = $dungeon->getPlayersInRoom($dungeon->getStartPosition());
        $this->assertEquals($result, array());
        $newPosition = $dungeon->getStartPosition();
        $newPosition[2] = $newPosition[2] - 1;
        $result = $dungeon->getPlayersInRoom($newPosition);
        $this->assertEquals($result, array($name));
    }

    public function testMoveUp() {
        $file = "./tests/testDungeons/testDungeon01.json";
        $dungeon = new Dungeon($file);
        $name = "testPlayer";
        $player = new Player($name, $dungeon->getStartPosition(), '');
        $dungeon->addNewPlayer($player);
        $this->assertFalse($dungeon->moveUp($player));
        $file = "./tests/testDungeons/testDungeon03.json";
        $dungeon = new Dungeon($file);
        $player = new Player($name, $dungeon->getStartPosition(), '');
        $dungeon->addNewPlayer($name);
        $this->assertTrue($dungeon->moveUp($player));
        $result = $dungeon->getPlayersInRoom($dungeon->getStartPosition());
        $this->assertEquals($result, array());
        $newPosition = $dungeon->getStartPosition();
        $newPosition[0] = $newPosition[0] + 1;
        $result = $dungeon->getPlayersInRoom($newPosition);
        $this->assertEquals($result, array($name));
    }

    public function testMoveDown() {
        $file = "./tests/testDungeons/testDungeon01.json";
        $dungeon = new Dungeon($file);
        $name = "testPlayer";
        $player = new Player($name, $dungeon->getStartPosition(), '');
        $dungeon->addNewPlayer($player);
        $this->assertFalse($dungeon->moveDown($player));
        $file = "./tests/testDungeons/testDungeon03.json";
        $dungeon = new Dungeon($file);
        $player = new Player($name, $dungeon->getStartPosition(), '');
        $dungeon->addNewPlayer($name);
        $this->assertTrue($dungeon->moveDown($player));
        $result = $dungeon->getPlayersInRoom($dungeon->getStartPosition());
        $this->assertEquals($result, array());
        $newPosition = $dungeon->getStartPosition();
        $newPosition[0] = $newPosition[0] - 1;
        $result = $dungeon->getPlayersInRoom($newPosition);
        $this->assertEquals($result, array($name));
    }
}

?>
