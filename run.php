<?php

//Uncomment in production
error_reporting(1); //disable error and warning reporting
ini_set('memory_limit', '-1'); //endless memory available
ini_set('max_execution_time', 400); //300 seconds = 5 minutes

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

$nodes = $_GET["node"];

$dag  = new DAG();
$dag->totalTask = $nodes;
$tree = $dag->makeTree();

// get heft schedule
$heft      = new HEFT();
$heftRank  = $heft->getRankTable($tree);
$readyList = sorting($heftRank);

$eftps     = new EFTSelection();
$hschedule = $eftps->runSchedule($tree, $readyList);

// get prioratized list of jobs
$weightNormal      = new weightNormal();
$weightNormalTable = $weightNormal->getRankTable($tree);
$readyList         = sorting($weightNormalTable);

$ct        = new CrossThreshold();
$cschedule = $ct->runSchedule($tree, $readyList);

// calculate with simulated annealing
$sa       = new SimulatedAnnealing();
$schedule = $sa->getSchedule($tree,[$cschedule]);

// calculate with simulated annealing without ct input
$saw       = new SimulatedAnnealing();
$wschedule = $saw->getSchedule($tree);

echo '{
    "heft": '.$hschedule[count($hschedule) - 1]['eft'].',
    "ct": ' . $cschedule[count($cschedule) - 1]['eft'].',
    "saw": '. $wschedule[count($wschedule) - 1]['eft'].',
    "sa": '.  $schedule[count($schedule) - 1]['eft'].'
}';