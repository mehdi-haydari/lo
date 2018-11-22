<?php

/*
 * MIT License.
 * By mehdi.haydari@live.com
 */
//namespace DAG;

define('ENTRANCE', "entrance");
define('EXITING', "exit");

/**
 * create Dag or series of task
 *
 */
class DAG {
    public $fat              = 0.3;
    public $ccr              = 0.1;
    public $jump             = 1;
    public $density          = 0.5;
    public $totalTask        = 100; 
    public $minimumWeight    = 5;
    public $maximumWeight    = 50;
    public $proccessorsCount = 2;
    private $tree            = array();
    private $remain          = 100;
    private $multyExit       = FALSE;
    private $currentLevel    = 0;
    private $multyEntrance   = FALSE;
    
    /**
     * making DAG
     * 
     * @return type
     */
    public function makeTree($type=NULL)
    {
        if($type == 'sample'){
            return $this->getSampleTree();
        }
        $this->remain = $this->totalTask;
        
        if (!$this->multyExit){
            $exit = $this->createTask(NULL, EXITING);
            $this->remain--;
        }
        
        while ($this->remain > 0){
            if($this->currentLevel == 0){
                if ($this->multyEntrance){
                    $this->remain -= $this->makeLevel(ENTRANCE);
                } else {
                    $this->append($this->createTask(0, ENTRANCE));
                    $this->remain--;
                }
            } else {
                $this->remain -= $this->makeLevel();
            }
            $this->currentLevel++;
        }
        
        if (!$this->multyExit){
            $exit->level = $this->currentLevel;
            $this->append($exit);
            $this->relate();
        } else {
            $this->currentLevel--;
        }
        $this->finalize();
        return $this->tree;
    }

    /**
     * for creating new node as a task
     * @param type $level
     * @param type $type
     * @return \Task
     */
    private function createTask($level=NULL, $type=NULL)
    {
        $task = new Task();
        $task->weight = $this->getWeight();
        $task->level  = $level;
        if($type == "entrance"){
            $task->isEntrance = TRUE;
        } else if($type == "exit") {
            $task->isExit = TRUE;
        }
        return $task;
    }
    
    /**
     * Making a row of task with same level
     * @param type $type
     * @return type
     */
    private function makeLevel($type=NULL)
    {
        $levelCount = $this->getLevelCount();
        if ($levelCount == $this->remain && $this->multyExit){
            $type = EXITING;
        }
        for($i = 0; $i < $levelCount; $i++){
            $task = $this->createTask($this->currentLevel,$type);
            $this->append($task);
            $this->relate();
        }
        return $levelCount;
    }
    
    /**
     * adding task to end of tree array
     * 
     * @param type $task
     */
    private function append($task)
    {
        $this->tree[count($this->tree)] = $task;
    }
    
    /**
     * make communication link with weight
     */
    private function relate()
    {
        foreach ($this->tree as $nkey => $node) {
            if($node->level == $this->currentLevel && $node->level > 0){
                foreach ($this->tree as $tkey =>$task){
                    $difference = $this->currentLevel - $task->level;
                    if($task->level < $node->level && $difference <= $this->jump ){
                        $relChance = (rand(0,10)*(1+$difference/$this->jump) <= ($this->density*10));
                        if($relChance == TRUE){
                            $weight = ceil(rand($this->minimumWeight, $this->maximumWeight) * $this->ccr);
                            $this->addPredeccessor($tkey, $nkey, $weight);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * set relation to tasks has no relation
     * 
     * @return boolean
     */
    private function finalize()
    {
        foreach ($this->tree as $key => $task) {
            if(!$task->isEntrance && count($task->predeccessor) == 0){
                $this->getPredeccessor($key);
            }
            if(!$task->isExit && count($task->successor) == 0){
                $this->getSuccessor($key);
            }
        }
        return TRUE;
    }

    /**
     * calculate number of task in a single level
     * 
     * @return type
     */
    private function getLevelCount()
    {
        $remain= $this->remain;
        if($this->currentLevel == 0){
            return floor($remain * 0.5 * $this->fat / 10);
        } else if($remain < 3) {
            return $remain;
        } else {
            $taskCount = ceil($remain * $this->currentLevel * $this->fat / 10);
            if ($taskCount < $remain){
                return $taskCount;
            } else {
                return $remain;
            }
        }
    }
    
    /**
     * if pred and succ has no relation add a relation
     * 
     * @param type $pred
     * @param type $succ
     * @param type $weight
     * @return boolean
     */
    private function addPredeccessor($pred, $succ, $weight)
    {
        $flag = TRUE;
        foreach ($this->tree[$pred]->successor as $su){
            if($su["target"] == $succ){
                $flag = False;
                return;
            }
        }
        if($flag == TRUE){
            $this->tree[$pred]->successor[]    = ["target" => $succ, "weight" => $weight];
            $this->tree[$succ]->predeccessor[] = ["target" => $pred, "weight" => $weight];
        }
        
        return $flag;
    }
    
    /**
     * set a random task as predeccessor
     * 
     * @param type $task
     */
    private function getPredeccessor($task)
    {
        $prs   = [];
        $level = $this->tree[$task]->level;
        
        foreach ($this->tree as $key => $node) {
            if(($node->level) == $level - 1){
                $prs[] = $key;
            }
        }
        
        $goal = $prs[rand(0, count($prs)-1)];
        $weight = ceil(rand($this->minimumWeight, $this->maximumWeight) * $this->ccr);
        $this->tree[$goal]->successor[]    = ["target" => $task, "weight" => $weight];
        $this->tree[$task]->predeccessor[] = ["target" => $goal, "weight" => $weight];
    }
    
    /**
     * set a random task as a successor
     * 
     * @param type $task
     */
    private function getSuccessor($task)
    {
        $prs   = [];
        $level = $this->tree[$task]->level;
        
        foreach ($this->tree as $key => $node) {
            if(($node->level) == $level + 1){
                $prs[] = $key;
            }
        }
        
        $goal = $prs[rand(0, count($prs)-1)];
        $weight = ceil(rand($this->minimumWeight, $this->maximumWeight) * $this->ccr);
        $this->tree[$task]->successor[]    = ["target" => $goal, "weight" => $weight];
        $this->tree[$goal]->predeccessor[] = ["target" => $task, "weight" => $weight];
    }
    
    private function getWeight()
    {
        $processor = [];
        for ($i = 0;$i < $this->proccessorsCount;$i++ ){
            $processor[] = rand($this->minimumWeight, $this->maximumWeight);
        }
        return $processor;
    }
    
    /**
     * creating sample DAG based on Task scheduling for hetergeneous computing systems
     * page 5
     * 
     * @return Dag as tas list
     */
    private function getSampleTree()
    {
        $task                = [];
        $task[0]             = new Task();
        $task[0]->weight     = [171,125];
        $task[0]->isEntrance = TRUE;
        $task[0]->successor  = [
                                ["target"=>1,"weight"=>17],
                                ["target"=>2,"weight"=>31],
                                ["target"=>3,"weight"=>29],
                                ["target"=>4,"weight"=>13],
                                ["target"=>5,"weight"=>7]
                               ];
        
        $task[1]               = new Task();
        $task[1]->weight       = [133,114];
        $task[1]->level        = 1;
        $task[1]->successor    = [["target"=>7,"weight"=>3],["target"=>8,"weight"=>30]];
        $task[1]->predeccessor = [["target"=>0,"weight"=>17]];
        
        $task[2]               = new Task();
        $task[2]->weight       = [26,131];
        $task[2]->level        = 1;
        $task[2]->successor    = [["target"=>6,"weight"=>16]];
        $task[2]->predeccessor = [["target"=>0,"weight"=>31]];
        
        $task[3]               = new Task();
        $task[3]->weight       = [145,192];
        $task[3]->level        = 1;
        $task[3]->successor    = [["target"=>7,"weight"=>11],["target"=>8,"weight"=>7]];
        $task[3]->predeccessor = [["target"=>0,"weight"=>29]];
        
        $task[4]               = new Task();
        $task[4]->weight       = [120,184];
        $task[4]->level        = 1;
        $task[4]->successor    = [["target"=>8,"weight"=>57]];
        $task[4]->predeccessor = [["target"=>0,"weight"=>13]];
        
        $task[5]               = new Task();
        $task[5]->weight       = [10,152];
        $task[5]->level        = 1;
        $task[5]->successor    = [["target"=>7,"weight"=>5]];
        $task[5]->predeccessor = [["target"=>0,"weight"=>7]];
        
        $task[6]               = new Task();
        $task[6]->weight       = [114,30];
        $task[6]->level        = 2;
        $task[6]->successor    = [["target"=>9,"weight"=>9]];
        $task[6]->predeccessor = [["target"=>2,"weight"=>16]];
        
        $task[7]               = new Task();
        $task[7]->weight       = [50,126];
        $task[7]->level        = 2;
        $task[7]->successor    = [["target"=>9,"weight"=>42]];
        $task[7]->predeccessor = [
                                  ["target"=>1,"weight"=>3],
                                  ["target"=>3,"weight"=>11],
                                  ["target"=>5,"weight"=>5]
                                 ];
        
        $task[8]               = new Task();
        $task[8]->weight       = [191,65];
        $task[8]->level        = 2;
        $task[8]->successor    = [["target"=>9,"weight"=>7]];
        $task[8]->predeccessor = [
                                  ["target"=>1,"weight"=>30],
                                  ["target"=>3,"weight"=>7],
                                  ["target"=>4,"weight"=>57]
                                 ];
        
        $task[9]               = new Task();
        $task[9]->weight       = [3,2];
        $task[9]->level        = 3;
        $task[9]->isExit       = TRUE;
        $task[9]->predeccessor = [
                                  ["target"=>6,"weight"=>9],
                                  ["target"=>7,"weight"=>42],
                                  ["target"=>8,"weight"=>7]
                                 ];
        
        return $task;
    }
}