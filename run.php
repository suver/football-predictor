<?php

include './index.php';
include './toss.php';

$calc->debug = true;

$toss = new Toss($data);

$games = $toss->start();

foreach ($games as $game) {
    $goals = match($game[0], $game[1]);

    echo "\tИТОГ '{$goals[0]}' VS '{$goals[1]}' \n\n";
}