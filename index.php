<?php

//Uncomment in production
//error_reporting(0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300); //300 seconds = 5 minutes


include 'DAG.php';
include 'Task.php';
include 'HEFT.php';
include 'PEFT.php';
include 'Common.php';
include 'Genetic.php';
include 'WeightNormal.php';
include 'EFTSelection.php';
include 'CrossThreshold.php';
include 'SimulatedAnnealing.php';

$dag = new DAG();
$tree = $dag->makeTree();

//echo '<div style="display:block;"><img src="dag.png"></div>';

$weightNormal      = new weightNormal();
$weightNormalTable = $weightNormal->getRankTable($tree);
$readyList         = sorting($weightNormalTable);

$ct = new CrossThreshold();
$schedule = $ct->runSchedule($tree, $readyList);
createTable($schedule,"Cross Threshold",true);

$heft = new HEFT();
$heftRank = $heft->getRankTable($tree);
$readyList = sorting($heftRank);

$eftps = new EFTSelection();
$eschedule = $eftps->runSchedule($tree, $readyList);
createTable($eschedule,"HEFT Algorithm");

$sa = new SimulatedAnnealing();
$schedule = $sa->getSchedule($tree,[$schedule,$eschedule]);
createTable($schedule,"simulated annealing");
/*
$ga        = new Genetic();
$schedule  = $ga->getSchedule($tree);
createTable($schedule,"Genetic");
/*
$peft = new PEFT();
$peftRank = $peft->getRankTable($tree);
$readyList = sorting($peftRank);

$ct = new CrossThreshold();
$schedule = $ct->runSchedule($tree, $readyList);
createTable($schedule,"Cross Threshold",true);
*/

echo "<pre>";
print_r($tree);