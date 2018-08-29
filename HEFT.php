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
class HEFT {
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
        }
        
        return $this->table;
    }

    /**
     * calculate weight_ni using (ni,pj - ni,pk)/(ni,pj / ni,pk)
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
        $maxRank   = 0;
        $task      = $tree[$taskNum];
        $weight    = $this->getWeight($task);
        $comWeight = 0;
                
        if(count($task->successor) == 0){
            return $weight;
        }
        
        foreach ($task->successor as $node) {
            if($this->table[$node["target"]]["rank"]+$node["weight"] > $maxRank+$comWeight){
                $maxRank   = $this->table[$node["target"]]["rank"];
                $comWeight = $node["weight"];
            }
        }
        
        return $weight+$comWeight+$maxRank;
    }
}
