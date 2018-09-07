<?php

/*
 * Task Scheduling in Distributed Computing Systems with a Genetic Algorithm
 */

class SimulatedAnnealing 
{
    public $temprature       = 1;   //initial temorature
	public $maxPopulation    = 80;
	public $cp               = 0.9;  //neighbore selection probablity
	public $mp               = 0.6;  //mutation probablity
	public $restarting       = 100;  
    public $tempureReduction = 0.06;
    private $population      = [];
    private $bestSolution    = [];
    private $mainSolution    = [];
            
    

	function __construct()
	{
		return $this;
	}

    /**
     * return scheduled list of tasks
     */
	public function getSchedule($dag,$ct=NULL)
	{
        // if the ct schedule passed
        if($ct !== NULL){
            foreach ($ct as $c){
                $this->population[] = $this->normalize($c);
            }
        }
        
		$this->initial($dag);
        $mainMakeSpan = $this->getMakespan($this->mainSolution, $dag);
        $bestMakeSpan = $this->getMakespan($this->bestSolution, $dag);
        while($this->temprature > 0){
            $this->generateNeighbore($dag);
            
            $bestNeighbore         = $this->population[0];
            $bestNeighboreMakeSpan = $this->getMakespan($bestNeighbore, $dag);
            $delta                 = $mainMakeSpan - $bestNeighboreMakeSpan;
            $prob                  = (rand(0, 10)/10) - pow(2.71828, $delta * $this->temprature);
            
            if($delta > 0 || $prob > 0){
                $this->mainSolution = $bestNeighbore;
                $mainMakeSpan       = $bestNeighboreMakeSpan;
                $delta              = $bestMakeSpan - $bestNeighboreMakeSpan;
                if($delta > 0){
                    $this->bestSolution = $bestNeighbore;
                    $bestMakeSpan       = $bestNeighboreMakeSpan;
                }
            }
            
            $this->temprature = $this->temprature - $this->tempureReduction;
        }

        return $this->makeSchedule(sortArrayByMakespan($this->population)[0],$dag);
	}

    /**
     * initial more population for start
     */
	private function initial($dag)
	{
        $this->population[] = $this->initialByMB($dag);
        $this->population[] = $this->initialByMP($dag);

        $this->fitness($dag);
        
        $this->bestSolution = $this->population[0];
        $this->mainSolution = $this->population[1];
	}
    
    /**
     * create new schedule based on current
     */
    private function generateNeighbore($dag)
    {
        $this->crossover($dag);
        $this->mutation($dag);
        $this->fitness($dag);
    }

    /**
     * this function check is schedule runnable and sort population with makespan
     */
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
        $cTask = [];
        for($i = 0; $i < $this->maxPopulation && count($this->population) > $i;$i++){
            $cTask[$i] = $this->population[$i];
        }
        $this->population = $cTask;
	}

    /**
     * change processor for processing
     */
	private function crossover($dag)
	{
        $this->reorderArray();
        $itr     = (int)count($this->population)/2;
        $inCount = count($this->population);
        for($i = 0;$i<$itr;$i++){
            if(rand(0,10) <= $this->cp*10 ) {
                $crossNode   = rand(0, count($dag) - 1); // choose node between list of nodes in pop[$i]
                $candidate   = $this->getCrossCandidates($crossNode, $dag); //get successors of pop[$i][$crossNode]

                $crossGen    = [];
                $crossGen[0] = $this->population[$i];
                $crossGen[1] = $this->population[$inCount - ($i + 1)];

                $newGens            = $this->mixGens($crossGen, $candidate);
                $this->population[] = $newGens[0];
                $this->population[] = $newGens[1];
            }
        }
    }
    
    /**
     * find task with most idle time and change the processor with mp probablity
     */
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

    /**
     * find task with most idle time and change the processor
     */
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

        //find the processor that finish last
        foreach($schedule as $proc){
            if($eft < $proc["eft"]){
                $processor = $proc["processor"];
            }
        }

        
        //find task with max idle time on cpu
        $prev = null;

        foreach($schedule as $proc){
            if($processor == $proc["processor"]){
                if($prev == null){
                    $prev = $proc["eft"];
                } else if($idle < $proc["eft"]-$prev) {
                    $idle = $proc["eft"]-$prev;
                    $task = $proc["task"];
                }
                $prev = $proc["eft"];
            }
        }
        unset($prev);

        //find witch parent have min io time
        $minPred = $dag[$task]->predeccessor[0]["target"];
        $idle    = $dag[$task]->predeccessor[0]["weight"];
        foreach($dag[$task]->predeccessor as $parent){
            if($parent["weight"] < $idle){
                $idle    = $parent["weight"];
                $minPred = $parent["target"];
            }
        }

        //remove task from processor list
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

        //find best place for task
        $targetProcessor = "x";
        $maxPlace = 0;
        foreach($sched as $id=>$proc){
            foreach ($proc as $key=>$job){
                if($minPred == $job){
                    $maxPlace = $key;
                    $targetProcessor = $id;
                    foreach($preds as $pred){
                        foreach ($proc as $index=>$todo){
                            if($pred == $todo && $index > $maxPlace){
                                $maxPlace = $index;
                            }
                        }
                    }
                }
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

    // blend two gen and return new schedule
    private function mixGens($gens, $candidate)
    {
        $copy = $this->getCopy($gens, $candidate);
        foreach($copy as $gi=>$gen){
            if(isset($gen["makespan"])){
                unset($gen["makespan"]);
            }
            for($i=0;$i<count($gen);$i++){ // find task place in x and place it in y
                $rowCandid = $this->searchCandidPlace($candidate,$gens[$gi][$i]);
                $copy[($gi == 1) ? 0 : 1][$i] = $this->addToPRC(($copy[($gi == 1) ? 0 : 1][$i]),$rowCandid);
            }
        }

        // reorder new schedule
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

    /**
     * add task to processor
     */
    private function addToPRC($prc, $gens)
    {
        $flash = 0;
        $flag  = FALSE;
        if(count($gens)==0){ // if it is initial node so there is no cross over
            $flag = TRUE;
        }
        for($i=0;$flag==FALSE;$i++){
            if(!isset($prc[$i])){
                $X = $gens[$flash];
                $prc[$i] = $X;
                $flash++;
                if(count($gens)<=$flash){ // if cross over candidate placed in list
                    $flag = TRUE;
                }
            }
        }

        return $prc;
    }

    /**
     * return cross over candidates place
     */
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

    /**
     * get an altered copy of gens
     * candidates removed from schedules
     */
    private function getCopy($gens, $candidate)
    {
        $copy = $gens;
        foreach($copy as $gi=>$gen){ // foreach schedule
            if(isset($gen["makespan"])){
                unset($gen["makespan"]);
            }
            foreach($gen as $pi=>$prc){// foreach processor
                foreach($prc as $ti=>$task){// foreach task
                    if(in_array($task,$candidate)){
                        unset($copy[$gi][$pi][$ti]); // remove candid task from list
                    }
                }
            }
        }
        return $copy;
    }

    /**
     * get some node for cross over
     */
    private function getCrossCandidates($pred,$dag)
    {
        $candidate    = [];
        $candidate[0] = $pred;
        foreach($dag[$pred]->successor as $suc){
            $candidate[] = $suc["target"];
        }

        return $candidate;
    }

    /**
     * get makespan of an schedule
     * if schedule cant be run then makespan will be false
     */
    private function getMakespan($schedule, $dag)
    {
        $makespan           = 0;
        $selectedProccessor = $this->makeSchedule($schedule, $dag);

        if($selectedProccessor == false){
            return false;
        }

        // get last finished process
        foreach($selectedProccessor as $task){
            if($task["eft"] > $makespan){
                $makespan = $task["eft"];
            }
        }
        return $makespan;
    }

    /**
     * test the passed schedule
     */
    private function makeSchedule($schedule, $dag)
    {
        $done               = [];
        $peat               = [0, 0];
        $selectedProccessor = [];

        if(isset($schedule["makespan"])){
            unset($schedule["makespan"]);
        }

        // reorder the schedule (for array key gaps remove)
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

        // run schedule and get makespan
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
                        foreach ($dag[$task]->predeccessor as $pre) { // calculate EST
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
                    if ($allowed != FALSE) { // calculate EFT
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

    /**
     * remove gap between array keys 
     */
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
    
    /**
     * change schedule format
     */
    private function normalize($schedule)
    {
        $normal = [0=>[],1=>[]];
        foreach ($schedule as $task){
            $normal[$task["processor"]][] = $task["task"];
        }
        
        return $normal;
    }
}
