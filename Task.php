<?php

/*
 * MIT license.
 * By Mehdi.haydari@live.com
 */
//namespace Task;

/**
 * creat a node as a task
 *
 */
class Task {
    public $predeccessor = array();
    public $successor    = array();
    public $weight       = [];
    public $deadline     = FALSE; //false means no deadline
    public $isEntrance   = FALSE;
    public $isExit       = FALSE;
    public $level        = 0;
    public $visited      = FALSE;
    
    function __construct() {
        return $this;
    }
    
    public function visit(){
        $this->visited = TRUE;
        return $this;
    }
}