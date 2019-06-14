<?php
$start_position = array();
function initialize_dungeon(){
    // HARD CODED FOR NOW
    $FILE = '../dungeon/dungeon01.json';

    // $FILE = readline('Enter a dungeon file: ');
    $data = file_get_contents($FILE);
    $dungeon = json_decode($data);
    $current_floor = 0;
    global $start_position;
    foreach($dungeon as $floor) {
        //iterate through each row
        for($i = 0; $i < count($floor->row); $i++) {
            //iterate through each column in that row
            for($j = 0; $j < count($floor->row[$i]->col); $j++) {
                if(isset($floor->row[$i]->col[$j]->start)) {
                    $start_position = array($current_floor, $i, $j);
                }
            }
        }
        $current_floor++;
    }
}

 ?>
