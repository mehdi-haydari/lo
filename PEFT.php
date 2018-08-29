<?php

/*
 * MIT license.
 * By Mehdi.haydari@live.com
 */

/**
 * task priority based on Task scheduling for hetergeneous computing systems
 * chapter 3 related works
 * DOI 10.1007/s11227-016-1917-2
 */
class PEFT {
    private $table = [];
    
    /**
     * calculate rank and fill the table
     * 
     * @param array $tree
     * @return array
     */
    public function getRankTable($tree)
    {
        for($i = count($tree)-1 ; $i >= 0 ; $i--){
            $this->table[$i] = [];
            $this->table[$i]["rank"]   = $this->getTaskRank($i,$tree);
            $this->table[$i]["weight"] = $this->getWeight($tree[$i]);
            $this->table[$i]["task"]   = $i;
            echo $i+1 ."-----------\n";
        }
        
        return $this->table;
    }
    
    private function OCTTable($task)
    {
        return $this->table[$task]["rank"];
    }

    /**
     * calculate weight
     * 
     * @param Task object $task
     * @return int
     */
    private function getWeight($task)
    {
        $wsum = 0;
        
        foreach ($task->weight as $weight){
            $wsum += $weight;
        }
        
        return $wsum / count($task->weight);
    }
    
    /**
     * calculate task rank using AVG(weight_i) + MAX(AVG(cij) + rank(nj))
     * 
     * @param index $taskNum
     * @param array $tree
     * @return int
     */
    private function getTaskRank($taskNum,$tree)
    {
        $task      = $tree[$taskNum];
        $oct       = 0;
        
        foreach ($task->weight as $key=>$proc){
            $oct += $this->getOCT($taskNum,$tree,$key);
            echo $oct."h\n";
        }
        echo $oct / count($task->weight)."f\n";
        return $oct / count($task->weight);
    }
    
    private function getOCT($taskNum,$tree,$processor)
    {
        $maxOCT  = 0;
        $minOCT  = null;
        $task    = $tree[$taskNum];
        
        foreach ($task->successor as $suc){
            foreach ($task->weight as $key=>$proc){
                $oct = $this->OCTTable($suc["target"])
                     + $this->getWeight($tree[$suc["target"]])
                     + $this->getCommunication($tree[$suc["target"]], $processor,$key);
            echo $oct."\n";
                
                if($oct < $minOCT || $minOCT == NULL){
                    $minOCT = $oct;
                }
            }
            if($minOCT > $maxOCT){
                $maxOCT = $minOCT;
            }
        }
        
        return $maxOCT;
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
        
        return ["proccessor"=>$processor, "time"=>$minimum];
    }
    
    
    /**
     * get communication weight
     * 
     * @param type $task
     * @param type $pr
     * @return boolean
     */
    private function getCommunication($task,$pr,$key)
    {
        $flag = 0;
        foreach($task->predeccessor as $pred){
            if($pr != $key){
                $flag = $pred["weight"];
            }
        }
        return $flag;
    }
}
