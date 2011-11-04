<?php

abstract class Formules
{
    public static function getMissileRange($combustionTech)
    {
        return max(0, $combustionTech * 2 - 1);
    }
    public static function getPhalanxRange($phalanxLvl)
    {
        return max(1, pow($phalanxLvl, 2) - 1);
    }
    
    public static function getReturnTime($stayDuration,$duration,$currentTime){
        return $stayDuration + 2 * $duration + $currentTime;    
    }
    public static function getArriveTime($duration,$currentTime){
        return $duration + $currentTime;
    }
    public static function getEndStayTime($stayDuration,$duration,$currentTime){
        return $duration + $stayDuration + $currentTime;    
    }
}

?>