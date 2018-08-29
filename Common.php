<?php

/*
 * MIT license.
 * By Mehdi.haydari@live.com
 *
 * contain commonly used fuctions
 */

function sorting($array)
{
    usort($array, function($a, $b) {
        return $b['rank'] <=> $a['rank'];
    });

    return $array;
}

function makespansort($a,$b)
{
    if ($a["makespan"] == $b["makespan"]) {
        return 0;
    }
    return ($a["makespan"] < $b["makespan"]) ? -1 : 1;
}

function makespanSorting($array)
{
    usort($array, "makespansort");

    return $array;
}

function sortArrayByMakespan($arr)
{
    if(count($arr) < 2){
        return $arr;
    } else {
        $l1 = [];
        $l2 = [];
        $i  = 0;

        foreach($arr as $l){
            if($i < count($arr)/2){
                $l1[] = $l;
            } else {
                $l2[] = $l;
            }
            $i++;
        }
        
    }
    return merge(sortArrayByMakespan($l1),  sortArrayByMakespan($l2));
}

function merge($l1,$l2)
{
    $x  = [];
    $p1 = 0;
    $p2 = 0;

    for($i=0;$i < count($l1)+count($l2);$i++){
        if(!isset($l1[$p1])){
            $x[] = $l2[$p2];
            $p2++;
        }else if(!isset($l2[$p2])){
            $x[] = $l1[$p1];
            $p1++;
        }else if($l1[$p1]["makespan"] < $l2[$p2]["makespan"]){
            $x[] = $l1[$p1];
            $p1++;
        }else if($l1[$p1]["makespan"] >= $l2[$p2]["makespan"]){
            $x[] = $l2[$p2];
            $p2++;
        }
    }

    return $x;
}

function createTable($schedule,$title,$haveDesc = false)
{
    echo '<div style="width:25%;display:inline-block">';
    echo '<h1>'.$title.'</h1>';
    echo '<table>'
        . '<tr style="border-bottom: 1px solid black;">'
        . '<th style="border: 1px solid black;">Task</td>'
        . '<th style="border: 1px solid black;">Processor</th>'
        . '<th style="border: 1px solid black;">EFT</td>';
    if($haveDesc == true) {
        echo '<th style="border: 1px solid black;">Description</th>';
    }
    echo '</tr>';

    foreach ($schedule as $key => $value) {
        if($key < 95){
            
        } else {
        echo '<tr style="border: 1px solid black;">';
        echo '<td style="border: 1px solid black;">'.($value["task"]+1).'</td>';
        echo '<td style="border: 1px solid black;">'.($value["processor"]+1).'</td>';
        echo '<td style="border: 1px solid black;">'.$value["eft"].'</td>';
        if($haveDesc == true) {
            if (isset($value["desc"])) {
                echo '<td style="border: 1px solid black;">' . $value["desc"] . '</td>';
            }
        }}
        echo '</tr>';
    }
    echo '</table>';

    echo '</div>';
}