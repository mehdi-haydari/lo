<?php

/**
 * Task Scheduling in Distributed Computing Systems with a Genetic Algorithm
 *  DOI: 10.1109/HPC.1997.592164
 */
class Genetic
{
	public $maxPopulation  = 50;
	public $cp             = 0.9;   //crossover probablity
	public $mp             = 0.3;   //mutation probablity
	public $restarting     = 100;
	public $maxGen         = 28;    //maximum number of generation
	private $population    = [];
            

	function __construct()
	{
		return $this;
	}

	public function getSchedule($dag)
	{
		$this->initial($dag);
        for($i=0;$i<$this->maxGen;$i++){
            $this->crossover($dag);
            $this->mutation($dag);
            $this->fitness($dag);
        }

        return $this->makeSchedule(sortArrayByMakespan($this->population)[0],$dag);
	}

	private function initial($dag)
	{
        $this->population[] = $this->initialByMB($dag);
        $this->population[] = $this->initialByMP($dag);

        $this->fitness($dag);
	}

	private function fitness($dag)
	{
        foreach($this->population as $key=>$schedule){
            $makespan = $this->getMakespan($schedule,$dag);
            if($makespan != false) {
                $this->population[$key]["makespan"] = $makespan;
            } else {
                unset($this->population[$key]);
            }
        }

        sortArrayByMakespan($this->population);
        $cTask = count($this->population);
        for($i = $cTask; $i > $this->maxPopulation-1;$i--){
            unset($this->population[$i]);
        }
	}

	private function crossover($dag)
	{
        $this->reorderArray();
        $itr     = (int)count($this->population)/2;
        $inCount = count($this->population);
        for($i = 0;$i<$itr;$i++){
            if(rand(0,10) <= $this->cp*10 ) {
                $crossNode = rand(0, count($dag) - 1);
                $candidate = $this->getCrossCandidates($crossNode, $dag);

                $crossGen = [];
                $crossGen[0] = $this->population[$i];
                $crossGen[1] = $this->population[$inCount - ($i + 1)];

                $newGens = $this->mixGens($crossGen, $candidate);
                $this->population[] = $newGens[0];
                $this->population[] = $newGens[1];
            }
        }
	}

	private function mutation($dag)
	{
        $count = count($this->population);
        for($i=0;$i < $count;$i++) {
            if(isset($this->population[$i])) {
                if (rand(0, 10) < ($this->mp * 10)) {
                    $x = $this->getMutated($this->population[$i], $dag);
                    $this->population[] = $x;
                }
            }
        }
	}

    private function getMutated($sched,$dag)
    {
        $task      = null;
        $schedule  = $this->makeSchedule($sched,$dag);
        $eft       = $schedule[count($schedule)-1]["eft"];
        $processor = $schedule[count($schedule)-1]["processor"];
        $idle      = 0;

        if(isset($sched["makespan"])){
            unset($sched["makespan"]);
        }

        foreach($schedule as $proc){
            if($eft < $proc["eft"]){
                $processor = $proc["processor"];
            }
        }

        $prev = null;

        foreach($schedule as $proc){
            if($processor == $proc["processor"]){
                if($prev == null){
                    $prev = $proc["eft"];
                } else if($idle < $proc["eft"]-$prev) {
                    $idle = $proc["eft"]-$prev;
                    $task = $proc["task"];
                    $prev = $proc["eft"];
                }
            }
        }
        unset($prev);

        $minPred = null;
        $idle    = $dag[$task]->predeccessor[0]["weight"];
        foreach($dag[$task]->predeccessor as $parent){
            if($parent["weight"] < $idle){
                $idle    = $parent["weight"];
                $minPred = $parent["target"];
            }
        }

        $place = array_search($task,$sched[$processor]);
        $cTask = count($sched[$processor])-1;
        $step = 1;
        for($i=$place;$i+$step <= $cTask;$i++){
            if(!isset($sched[$processor][$i+$step])){
                return;
            }
            $sched[$processor][$i] = $sched[$processor][$i+$step];
        }
        for($i = 0;$i<$step;$i++){
            unset($sched[$processor][count($sched[$processor])-1]);
        }

        $preds = [];
        foreach($dag[$task]->predeccessor as $parent){
            $preds[] = $parent["target"];
        }

        $targetProcessor = null;
        $maxPlace = 0;
        foreach($sched as $id=>$proc){
            if(in_array($minPred,$proc)){
                $maxPlace = array_search($minPred,$proc);
                $targetProcessor = $id;
                foreach($preds as $pred){
                    if(array_search($pred,$proc) > $maxPlace){
                        $maxPlace = array_search($pred,$proc);
                    }
                }
                break;
            }
        }

        $cTask = count($sched[$targetProcessor]);
        $step  = 1;
        for($i=$cTask;$i > $maxPlace;$i--){
            if(!isset($sched[$targetProcessor][$i-$step])){
                return;
            }
            if($i-($step) < $maxPlace){
                break;
            } else {
                $sched[$targetProcessor][$i] = $sched[$targetProcessor][$i - $step];
            }
        }
        $sched[$targetProcessor][$maxPlace] = $task;

        $newSchedule = [];
        for($i=0;$i<count($sched);$i++){
            $newSchedule[$i] = [];
            $step = 0;
            for($j=0;$j<count($sched[$i]);$j++){
                if(!isset($sched[$i][$j+$step])){
                    if(max(array_keys($sched[$i])) < $j+$step || $j < 0){
                        $j = count($sched[$i]);
                    } else {
                        $step++;
                        $j--;
                    }
                } else {
                    $newSchedule[$i][$j] = $sched[$i][$j+$step];
                }
            }
        }

        return $newSchedule;
    }

    private function mixGens($gens, $candidate)
    {
        $copy = $this->getCopy($gens, $candidate);
        foreach($copy as $gi=>$gen){
            if(isset($gen["makespan"])){
                unset($gen["makespan"]);
            }
            for($i=0;$i<count($gen);$i++){
                $rowCandid = $this->searchCandidPlace($candidate,$gens[$gi][$i]);
                $copy[($gi == 1) ? 0 : 1][$i] = $this->addToPRC(($copy[($gi == 1) ? 0 : 1][$i]),$rowCandid);
            }
        }

        $newSchedule = [];
        for($i=0;$i<count($copy);$i++){
            $newSchedule[$i] = [];
            $step = 0;
            for($j=0;$j<count($copy[$i]);$j++){
                if(!isset($copy[$i][$j+$step])){
                    if(max(array_keys($copy[$i])) < $j+$step || $j < 0){
                        $j = count($copy[$i]);
                    } else {
                        $step++;
                        $j--;
                    }
                } else {
                    $newSchedule[$i][$j] = $copy[$i][$j+$step];
                }
            }
        }

        return $newSchedule;
    }

    private function addToPRC($prc, $gens)
    {
        $flash = 0;
        $flag = FALSE;
        if(count($gens)==0){
            $flag = TRUE;
        }
        for($i=0;$flag==FALSE;$i++){
            if(!isset($prc[$i])){
                $X = $gens[$flash];
                $prc[$i] = $X;
                $flash++;
                if(count($gens)<=$flash){
                    $flag = TRUE;
                }
            }
        }

        return $prc;
    }

    private function searchCandidPlace($candidate,$string)
    {
        $places = [];
        foreach($candidate as $candid){
            if(in_array($candid,$string)){
                $places[] = $candid;
            }
        }

        return $places;
    }

    private function getCopy($gens, $candidate)
    {
        $copy = $gens;
        foreach($copy as $gi=>$gen){
            if(isset($gen["makespan"])){
                unset($gen["makespan"]);
            }
            foreach($gen as $pi=>$prc){
                foreach($prc as $ti=>$task){
                    if(in_array($task,$candidate)){
                        unset($copy[$gi][$pi][$ti]);
                    }
                }
            }
        }
        return $copy;
    }

    private function getCrossCandidates($pred,$dag)
    {
        $candidate    = [];
        $candidate[0] = $pred;
        foreach($dag[$pred]->successor as $suc){
            $candidate[] = $suc["target"];
        }

        return $candidate;
    }

    private function getMakespan($schedule, $dag)
    {
        $makespan           = 0;
        $selectedProccessor = $this->makeSchedule($schedule, $dag);

        if($selectedProccessor == false){
            return false;
        }

        foreach($selectedProccessor as $task){
            if($task["eft"] > $makespan){
                $makespan = $task["eft"];
            }
        }
        return $makespan;
    }

    private function makeSchedule($schedule, $dag)
    {
        $done               = [];
        $peat               = [0, 0];
        $selectedProccessor = [];

        if(isset($schedule["makespan"])){
            unset($schedule["makespan"]);
        }

        $newSchedule = [];
        for($i=0;$i<count($schedule);$i++){
            $newSchedule[$i] = [];
            $step = 0;
            for($j=0;$j<count($schedule[$i]);$j++){
                if(!isset($schedule[$i][$j+$step])){
                    if(max(array_keys($schedule[$i])) < $j+$step || $j < 0){
                        $j = count($schedule[$i]);
                    } else {
                        $step++;
                        $j--;
                    }
                } else {
                    $newSchedule[$i][$j] = $schedule[$i][$j+$step];
                }
            }
        }

        $schedule = $newSchedule;

        $loopLimiter = 0;
        for ($i = 0; $i < count($dag);){
            if($loopLimiter > count($dag)*count($schedule)){
                return false;
            }
            foreach ($schedule as $num => $processor) {
                foreach ($processor as $task) {
                    $allowed = TRUE;
                    $EST = $peat[$num];
                    if (in_array($task, $done)) {
                        $allowed = FALSE;
                    } else {
                        foreach ($dag[$task]->predeccessor as $pre) {
                            if (!in_array($pre["target"], $done)) {
                                $allowed = FALSE;
                            } else {
                                if (!$this->isItLocalTo($pre["target"], $dag, $num, $selectedProccessor)) {
                                    if ($EST < $selectedProccessor[$pre["target"]]["eft"] + $pre["weight"]) {
                                        $EST = $pre["weight"] + $selectedProccessor[$pre["target"]]["eft"];
                                    }
                                } else {
                                    if ($EST < $selectedProccessor[$pre["target"]]["eft"]) {
                                        $EST = $selectedProccessor[$pre["target"]]["eft"];
                                    }
                                }
                            }
                        }
                    }
                    if ($allowed != FALSE) {
                        $selectedProccessor[$task]["eft"] = $EST + $dag[$task]->weight[$num];
                        $selectedProccessor[$task]["task"] = $task;
                        $selectedProccessor[$task]["processor"] = $num;

                        $peat[$num] = $EST + $dag[$task]->weight[$num];
                        $done[] = $task;
                        $i++;
                    }
                }
            }
            $loopLimiter++;
        }

        return $selectedProccessor;
    }

	//initial by minimum cpu busy time
	private function initialByMB($dag)
	{
		$schedule = [];
		$busy     = [0,0];

		foreach($dag as $index=>$node){
			$weight = $node->weight[0] + $busy[0];
			$pr     = 0;
			foreach($node->weight as $key => $pw){
				if($pw+$busy[$key] < $weight){
					$weight = $pw + $busy[$key];
					$pr     = $key;
				}
			}
			$schedule[$pr][] = $index;
			$busy[$pr]       = $weight;
		}

		return $schedule;
	}

	//initial by minimum processor
	private function initialByMP($dag)
	{
        $schedule = [];

        foreach($dag as $index=>$node){
            $weight = $node->weight[0];
            $pr     = 0;
            foreach($node->weight as $key => $pw){
                if($pw < $weight){
                    $weight = $pw;
                    $pr     = $key;
                }
            }
            $schedule[$pr][] = $index;
        }

        return $schedule;
	}

    private function isItLocalTo($pr , $tree, $key, $selectedProccessor)
    {
        $flag  = FALSE;
        if($key == $selectedProccessor[$pr]["processor"]){
            $flag = TRUE;
        } elseif ($this->isSuccessorRunOn($pr,$tree,$key,$selectedProccessor)) {
            $flag = TRUE;
        }

        return $flag;
    }

    private function isSuccessorRunOn($task, $tree, $proccessor, $selectedProccessor)
    {
        $flag = FALSE;
        $succes = $tree[$task]->successor;
        foreach ($succes as $suc){
            if( isset($selectedProccessor[$suc["target"]]) && $proccessor == $selectedProccessor[$suc["target"]]["processor"]){
                $flag = TRUE;
            }
        }

        return $flag;
    }

    private function reorderArray()
    {
	    $i = 0;
	    foreach ($this->population as $index => $cell){
            if($index > $i){
                $this->population[$i] = $cell;
                unset($this->population[$index]);
            }
            $i++;
        }
    }
}