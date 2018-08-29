<?php

/*
 * MIT license.
 * By Mehdi.haydari@live.com
 */

/**
 * processor selection based on Task scheduling for hetergeneous computing systems
 * chapter 3 related works
 * DOI 10.1007/s11227-016-1917-2
 */
class EFTSelection {
    private $peat               = [0,0]; //processor earliest available time
    private $selectedprocessor = [];
    private $est                = [];
    private $eft                = [];
    /**
     * running schedule
     * 
     * @param array $tree
     * @param array $table
     */
    public function runSchedule($tree, $table)
    {
        foreach ($table as$task) {
            $eft  = $this->getEFT($task["task"],$tree);
            $meft = $this->getMinimumEFT($eft[$task["task"]]);
            if ($this->getCommunication($tree[$task["task"]],$meft["processor"]) == 0){
                $this->peat[$meft["processor"]] = $eft[$task["task"]][$meft["processor"]];
            } else {
                $this->peat[$meft["processor"]] = $eft[$task["task"]][$meft["processor"]]+$tree[$task["task"]]->weight[$meft["processor"]];
            }
            $this->selectedprocessor[$task["task"]]["processor"] = $meft["processor"];
            $this->selectedprocessor[$task["task"]]["eft"]        = $meft["time"];
            $this->selectedprocessor[$task["task"]]["est"]        = $eft[$task["task"]]["est"][$meft["processor"]];
            $this->selectedprocessor[$task["task"]]["task"]       = $task["task"];
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
            $this->eft[$task]["est"][$key] = $EST;
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
            if($minimum > $e && $key != "est"){
                $minimum   = $e;
                $processor = $key;
            }
        }
        
        return ["processor"=>$processor, "time"=>$minimum];
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
}
