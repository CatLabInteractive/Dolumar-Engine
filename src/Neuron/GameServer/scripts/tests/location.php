<?php

$player = new Dolumar_Players_Player (1);

echo print_r ($player->calculateNewStartLocation (array (0, 0), Dolumar_Races_Race::getRace ('DarkElves')));

?>
