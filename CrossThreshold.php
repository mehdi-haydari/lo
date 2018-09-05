<?php

/*
 * MIT license.
 * By Mehdi.haydari@live.com
 */

/**
 * processor selection based on Task scheduling for hetergeneous computing systems
 * DOI 10.1007/s11227-016-1917-2
 */
class CrossThreshold {
    private $peat               = [0,0]; //processor earliest available time
    private $selectedprocessor  = [];
    private $est                = [];
    private $eft                = [];
    public  $threshold          = 0.3;

    /**
     * running schedule
     * 
     * @param array $tree
     * @param array $table
     */
    public function runSchedule($tree, $table)
    {
        foreach ($table as $key => $task) {
            $eft  = $this->getEFT($task["task"],$tree);
            $meft = $this->getMinimumEFT($eft[$task["task"]]);

            // check if this processor is fastest processor for this work
            // regardless to communication time or est
            if($this->compareProcessorTime($tree[$task["task"]]->weight,$meft) == TRUE){
                if ($this->getCommunication($tree[$task["task"]],$meft["processor"]) == 0){
                    $this->peat[$meft["processor"]] = $eft[$task["task"]][$meft["processor"]];
                } else {
                    $this->peat[$meft["processor"]] = $eft[$task["task"]][$meft["processor"]]+$tree[$task["task"]]->weight[$meft["processor"]];
                }
                $this->selectedprocessor[$task["task"]]["processor"]  = $meft["processor"];
                $this->selectedprocessor[$task["task"]]["eft"]        = $meft["time"];
                $this->selectedprocessor[$task["task"]]["task"]       = $task["task"];
            } else { // give chance to minimum processor
                $abstractWeight = $this->getAbstractWeight($task["task"], $tree);
                $weight_ni      = $this->getWeight_ni($table,$task["task"]);
                $crossThreshold = $this->getCrossThreshold($weight_ni, $abstractWeight);
                if ($crossThreshold <= $this->threshold){ // cross over
                    if ($this->getCommunication($tree[$task["task"]],$meft["processor"]) == 0){
                        $this->peat[$meft["processor"]] = $eft[$task["task"]][$meft["processor"]];
                    } else {
                        $this->peat[$meft["processor"]] = $eft[$task["task"]][$meft["processor"]]+$tree[$task["task"]]->weight[$meft["processor"]];
                    }
                    $this->selectedprocessor[$task["task"]]["processor"]  = $meft["processor"];
                    $this->selectedprocessor[$task["task"]]["eft"]        = $meft["time"];
                    $this->selectedprocessor[$task["task"]]["task"]       = $task["task"];
                    $this->selectedprocessor[$task["task"]]["desc"]       = "cross over:".$crossThreshold;
                } else { // no cross over
                    $mp = $this->getMinimumProcessor($tree[$task["task"]]);
                    if ($this->getCommunication($tree[$task["task"]],$mp["processor"]) == 0){
                        $this->peat[$mp["processor"]] = $eft[$task["task"]][$mp["processor"]];
                    } else {
                        $this->peat[$mp["processor"]] = $eft[$task["task"]][$mp["processor"]]+$tree[$task["task"]]->weight[$meft["processor"]];
                    }
                    $this->selectedprocessor[$task["task"]]["processor"] = $mp["processor"];
                    $this->selectedprocessor[$task["task"]]["eft"]        = $this->peat[$mp["processor"]];
                    $this->selectedprocessor[$task["task"]]["task"]       = $task["task"];
                    $this->selectedprocessor[$task["task"]]["desc"]       = "no cross over:".$crossThreshold;
                }
            }
        }
        return $this->selectedprocessor;
    }
    
    /**
     * calculating Earliest Finish Time
     * 
     * @param object $task
     * @param array $tree
     * @return array
     */
    private function getEFT($task,$tree)
    {
        $est = $this->getEST($task,$tree);
        foreach ($est as $key=>$EST){
            $this->eft[$task][$key] = $tree[$task]->weight[$key]+$EST;
        }
        return $this->eft;
    }
    
    private function getEST($task,$tree)
    {
        foreach ($this->peat as $key=>$processor){
            $lastEFT = $this->getPredeccessorEFT($task, $tree, $key);
            if($lastEFT > $processor){
                $this->est[$task][$key] = $lastEFT;
            } else {
                $this->est[$task][$key] = $processor;
            }
            
        }
        
        return $this->est[$task];
    }

    /**
     * get processor with low processing time for given task
     * 
     * @param object $task
     * @return array
     */
    private function getMinimumProcessor($task)
    {
        $processor = 0;
        $minimum   = $task->weight[0];
        
        foreach ($task->weight as $key => $e){
            if($minimum > $e){
                $minimum   = $e;
                $processor = $key;
            }
        }
        
        return ["processor"=>$processor, "time"=>$minimum];
    }
    
    /**
     * finding a processor with minimum EFT
     * 
     * @param array $eft
     * @return $array
     */
    private function getMinimumEFT($eft)
    {
        $processor = 0;
        $minimum   = $eft[0];
        
        foreach ($eft as $key => $e){
            if($minimum > $e){
                $minimum   = $e;
                $processor = $key;
            }
        }
        
        return ["processor"=>$processor, "time"=>$minimum];
    }
    
    /**
     * return true if selected processor is minimum 
     * 
     * @param type $task
     * @param array $selected
     * @return boolean
     */
    private function compareProcessorTime($task,$selected)
    {
        $processor = $selected["processor"];
        $minimum   = $task[$selected["processor"]];
        
        foreach ($task as $key => $e){
            if($minimum > $e && $processor != $key){
                return FALSE;
            }
        }
        
        return TRUE;
    }
    
    /**
     * get communication weight
     * 
     * @param type $task
     * @param type $pr
     * @return boolean
     */
    private function getCommunication($task,$pr)
    {
        $flag = 0;
        foreach($task->predeccessor as $pred){
            if($pr == $this->selectedprocessor[$pred["target"]]){
                $flag = $pred["weight"];
            }
        }
        return $flag;
    }
    
    private function getAbstractWeight($task,$tree)
    {
        $eft       = $this->getEFT($task, $tree)[$task];
        $maxWeight = 0;
        $minWeight = $eft[0];
        
        $highProc = NULL; //processor had max wight
        $lowProc  = NULL; //processor had min weight
        
        foreach ($eft as $key => $value) {
            if($value <= $minWeight){
                $minWeight = $value;
                $lowProc   = $key;
            }
            if($value >= $maxWeight){
                $maxWeight = $value;
                $highProc  = $key;
            }
        }
        
        if (($maxWeight - $minWeight) / ($maxWeight / $minWeight)==0){
            return 1;
        }
        return ($maxWeight - $minWeight) / ($maxWeight / $minWeight);
    }
    
    private function getCrossThreshold($weight, $abstract)
    {
        return $weight/$abstract;
    }
    
    /**
     * geting predeccessors eft
     * 
     * @param type $task
     * @param type $processor
     * @return max eft as int
     */
    private function getPredeccessorEFT($task,$tree,$processor)
    {
        $eft = 0;
        $com = 0;
        foreach ($tree[$task]->predeccessor as $pred){
            if (!isset($this->eft[$pred["target"]][$this->selectedprocessor[$pred["target"]]["processor"]])){
                echo '<br>target ';
                print_r($task);
                echo '<br>';
                print_r($pred["target"]);
                echo '<br>';
                print_r($this->selectedprocessor[$pred["target"]]);
                echo '<br>';
                print_r($this->eft);
                echo '<br>';
                print_r($this->eft[$pred["target"]]);
                echo '<br>';
                print_r($this->selectedprocessor);
                echo '<br>';
                exit;
            }
            $e = $this->eft[$pred["target"]][$this->selectedprocessor[$pred["target"]]["processor"]];
            if($this->isItLocalTo($pred["target"], $task, $tree, $processor)==FALSE){
                $e += $pred["weight"];
            }
            if($eft < $e){
                $eft = $e;
            }
        }
        
        return $eft+$com;
    }
    
    private function isItLocalTo($pr ,$task,$tree,$key)
    {
        $flag  = FALSE;
        if($key == $this->selectedprocessor[$pr]["processor"]){
            $flag = TRUE;
        } elseif ($this->isSuccessorRunOn($pr,$tree,$key)) {
            $flag = TRUE;
        }
        
        return $flag;
    }
    
    private function isSuccessorRunOn($task, $tree, $processor)
    {
        $flag = FALSE;
        $succes = $tree[$task]->successor;
        foreach ($succes as $suc){
            if( isset($this->selectedprocessor[$suc["target"]]) && $processor == $this->selectedprocessor[$suc["target"]]["processor"]){
                $flag = TRUE;
            }
        }
        
        return $flag;
    }
    
    private function getWeight_ni($table,$key){
        foreach ($table as $row){
            if($row["task"] == $key){
                return $row["weight"];
            }
        }
    }
}