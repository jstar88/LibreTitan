<?php
 /* 
 please don't delete this
 
 
 autor ??? (unknow)
----------------------------------------------
modify by ???? v.0.2
 - fix some bugs
 - added some mystical logic in attack  ;)
 - fix found resources volume
---------------------------------------------- 
**rewrite by jstar**

v.0.3
- OOP architecture
- cleaned and optimized code
- lostships more realistic

v.0.4
- fix syntax error

v.0.5
- rewrite function foundresource

v.0.6
- fix param in function getsizeandmessagge()
- added fleet points in return messagge

v.0.7
- fix another param in function getsizeandmessagge()
- fix max darkmatter
- cleaned code with new function "updateFleetStatus"
- cleaned general

v.0.8
- fix some bugs

v.0.9
- fix param in darkmatter 
- fix lang name of darkmatter message

v1.0
- debug mode
- cleaned code with new function

v 2.0
- new architecture
- new system of debug(you can choose if the report will send only to you or to all)

v 2.1
- fix some bugs
- better debug views

v 2.2
- fix update of fleets state
- fix debug raport title

v 2.3
- fix double msg to admin
- fix fleet return
*/
//-----------------------------------------------
if (!defined('INSIDE'))die(header("location:../../"));

class MissionCaseExpedition extends FlyingFleetHandler
{
    private $debug;
    private $info=array();
    private $toAll;
    
    private $fleetRow;
    private $fleetOwner;
    private $fleetCount=array();
    private $fleetStayDuration;
    private $fleetPoints;
    private $fleetCapacity;
    private $fleetStartTime;
    private $fleetEndTime;
    private $fleetId;
    
    private $fleetResourceMetal;
    private $fleetResourceCrystal;
    private $fleetResourceDeuterium;
    private $fleetStartUniverse;
    private $fleetStartGalaxy;
    private $fleetStartSystem;
    private $fleetStartPlanet;
    private $fleetStartType;
    private $fleetEndUniverse;
    private $fleetEndGalaxy;
    private $fleetEndSystem;
    private $fleetEndPlanet;
    private $fleetEndType;
    private $fleetResourceDarkmatter;

  	public function __construct($FleetRow,$debug=false,$toAll=false){
     global $lang, $resource, $pricelist, $reslist, $game_config;
     
     $real_language_starter =getRealLanguage($user['id'],$FleetRow['fleet_owner']);
	  if(empty($real_language_starter))
	     $real_language_starter=$lang;
     
     $this->debug=$debug;
     $this->toAll=$toAll;

     if ($this->isArriveToDestination($FleetRow)){
        
        $Expowert=$this->inizialize_Expowert();        
        $this->inizialize_fleetInfo($FleetRow,$Expowert);
        
        $GetEvent    = mt_rand(1, 20);
        
           if($GetEvent<=5) //25%
              $Message=$this->foundResource($real_language_starter);
            
           elseif($GetEvent<=9) //25%
              $Message=$this->foundDarkMatter($real_language_starter); 
           
           elseif($GetEvent<=10)// 1/20 %
              $Message=$this->foundSupernova($real_language_starter);
                           
           elseif($GetEvent<=12)//10%
              $Message=$this->foundShips($real_language_starter);
          
           elseif($GetEvent<=13)//10%
              $Message=$this->alienAttack($real_language_starter);
            
           elseif($GetEvent<=15)//10%
              $Message=$this->lostShips($real_language_starter);
            
           elseif($GetEvent<=17) //10%
              $Message=$this->timeShift($real_language_starter);
          
           else    //10%
              $Message=$this->notFound($real_language_starter);
        
        if($this->debug) 
            $this->debugMessage($real_language_starter);
        SendSimpleMessage($this->fleetOwner, '', $this->fleetEndTime, 15, $real_language_starter['sys_mess_tower'], $real_language_starter['sys_expe_report'], $Message);
        if( strpos($this->info['function'],"alienAttack") === false )
            $this->updateFleetStatus();
     }
     elseif ($this->isReturnedToHome($FleetRow)){
        $Expowert=$this->inizialize_Expowert();        
        $this->inizialize_fleetInfo($FleetRow,$Expowert);         
        $this->RestoreFleetToPlanet();
        $this->sendBackHomeMessage($real_language_starter);
    }
  } 
  
  private function sendBackHomeMessage($real_language_starter){
   
     $Message = sprintf($real_language_starter['sys_expe_back_home'], $real_language_starter['Metal'], pretty_number($this->fleetRresourceMmetal), $real_language_starter['Crystal'], pretty_number($this->fleetResourceCrystal),  $real_language_starter['Deuterium'], pretty_number($this->fleetResourceDeuterium), $real_language_starter['Darkmatter'], pretty_number($this->fleetResourceDarkmatter));
     SendSimpleMessage($this->fleetOwner, '', $this->fleetEndTime, 15, $real_language_starter['sys_mess_tower'], $real_language_starter['sys_expe_report'], $Message);               
  }   

  private function inizialize_fleetInfo($FleetRow,$Expowert){
     global $pricelist;
    
    $this->fleetRow       =$FleetRow;
    $this->fleetOwner     =$FleetRow['fleet_owner']; 
    $this->fleetPoints    = 0;
    $this->fleetCapacity  = 0;
    $this->fleetId        =$FleetRow["fleet_id"];
    $this->fleetEndTime   =$FleetRow["fleet_end_time"];
    $this->fleetStartTime =$FleetRow["fleet_start_time"];
    $this->fleetStartUniverse     =$FleetRow['fleet_start_universe'];
    $this->fleetStartGalaxy       =$FleetRow['fleet_start_galaxy'];
    $this->fleetStartSystem       =$FleetRow['fleet_start_system'];
    $this->fleetStartPlanet       =$FleetRow['fleet_start_planet'];
    $this->fleetStartType         =$FleetRow['fleet_start_type'];
    $this->fleetEndUniverse       =$FleetRow['fleet_end_universe'];
    $this->fleetEndGalaxy         =$FleetRow['fleet_end_galaxy'];
    $this->fleetEndSystem         =$FleetRow['fleet_end_system'];
    $this->fleetEndPlanet         =$FleetRow['fleet_end_planet'];
    $this->fleetEndType           =$FleetRow['fleet_end_type'];
    $this->fleetResourceMetal     =intval($FleetRow['fleet_resource_metal']);
    $this->fleetResourceCrystal   =intval($FleetRow['fleet_resource_crystal']);
    $this->fleetResourceDeuterium =intval($FleetRow['fleet_resource_deuterium']);
    $this->fleetResourceDarkmatter=intval($FleetRow['fleet_resource_darkmatter']);

    $farray = explode(";", $FleetRow['fleet_array']);
    foreach ($farray as $Item => $Group){
        if (empty($Group)) continue;
        $Class                         = explode (",", $Group);
        $this->fleetCount[$Class[0]]   =  $Class[1];
        $this->fleetCapacity      += $Class[1] * $pricelist[$Class[0]]['capacity'];
        $this->fleetPoints        += $Class[1] * $Expowert[$Class[0]];    
    }
    $this->fleetCapacity     -= $FleetRow['fleet_resource_metal']+ $FleetRow['fleet_resource_crystal'] + $FleetRow['fleet_resource_deuterium'] + $FleetRow['fleet_resource_darkmatter'];
    $this->fleetStayDuration  = ($FleetRow['fleet_end_stay'] - $FleetRow['fleet_start_time']) / 3600;
  }
  
  private function inizialize_Expowert(){
    global $pricelist, $reslist;
   foreach($reslist['fleet'] as $ID)
     $Expowert[$ID]    = ($pricelist[$ID]['metal'] + $pricelist[$ID]['crystal']) / 1000;                    
   $Expowert[202] = 12;
   $Expowert[203] = 47;
   $Expowert[204] = 12;
   $Expowert[205] = 110;
   $Expowert[206] = 47;
   $Expowert[207] = 160; 
   return $Expowert;
  } 
  
  private function RestoreFleetToPlanet ($Start = true)
	{
		global $resource;

		$QryUpdFleet         = "";
		foreach ($this->fleetCount as $ship => $number)
				$QryUpdFleet .= "`". $resource[$ship] ."` = `".$resource[$ship]."` + '".$number."', \n";

		$QryUpdatePlanet   = "UPDATE {{table}} SET ";
		if ($QryUpdFleet != "")
			$QryUpdatePlanet  .= $QryUpdFleet;

		$QryUpdatePlanet  .= "`metal`      = `metal`      + '". ($this->fleetResourceMetal) ."', ";
		$QryUpdatePlanet  .= "`crystal`    = `crystal`    + '". ($this->fleetResourceCrystal) ."', ";
		$QryUpdatePlanet  .= "`deuterium`  = `deuterium`  + '". ($this->fleetResourceDeuterium) ."' ";
		      
    $QryUpdatePlanet  .= "WHERE ";

		if ($Start){
		   $QryUpdatePlanet  .= "`universe` = '". ($this->fleetStartUniverse) ."' AND "; 
			$QryUpdatePlanet  .= "`galaxy` = '". ($this->fleetStartGalaxy) ."' AND ";
			$QryUpdatePlanet  .= "`system` = '". ($this->fleetStartSystem) ."' AND ";
			$QryUpdatePlanet  .= "`planet` = '". ($this->fleetStartPlanet) ."' AND ";
			$QryUpdatePlanet  .= "`planet_type` = '". ($this->fleetStartType) ."' ";
		}
		else{
		   $QryUpdatePlanet  .= "`universe` = '". ($this->fleetEndUniverse) ."' AND ";
			$QryUpdatePlanet  .= "`galaxy` = '". ($this->fleetEndGalaxy) ."' AND ";
			$QryUpdatePlanet  .= "`system` = '". ($this->fleetEndSystem) ."' AND ";
			$QryUpdatePlanet  .= "`planet` = '". ($this->fleetEndPlanet) ."' AND ";
			$QryUpdatePlanet  .= "`planet_type` = '". ($this->fleetEndType) ."' ";
		}
		$QryUpdatePlanet  .= "LIMIT 1;";
		doquery( $QryUpdatePlanet, 'planets');
		if(!empty($this->fleetResourceDarkmatter))
         doquery("UPDATE `{{table}}` SET `darkmatter` = `darkmatter` + '".($this->fleetResourceDarkmatter)."' WHERE `id` ='".($this->fleetOwner)."' LIMIT 1 ;", 'users');
		doquery ("DELETE FROM {{table}} WHERE `fleet_id` = ".intval($this->fleetId), 'fleets');
	}


private function  foundResource($real_language_starter){
  global $game_config;
    
    $StatFactor = doquery("SELECT MAX(total_points) as total FROM {{table}} WHERE `stat_type` = '1';", 'statpoints', true);
    $MaxPoints =($StatFactor['total'] < 5000000)
    ? 9000
    : 12000;

    $FindSize      = mt_rand(1, 100);
    $WitchFound    = mt_rand(1,3);
    
    $text=  "sys_expe_found_ress";
    $minRandomTxt=1;
    
    if($FindSize<=60) { //60%
        $minRandom= 1;
        $maxRandom= 10;
    }
    elseif ($FindSize<=94) { //34%
        $minRandom= 11;
        $maxRandom= 32;
    } 
    else{ //6%
       $minRandom= 33;
       $maxRandom= 50; 
    }
    if ($WitchFound == 1) { 
        $maxRandomTxt=4;
        $element= "Metal"; 
        $x=$this->getSizeAndMessage($real_language_starter,$minRandom,$maxRandom,$minRandomTxt,$maxRandomTxt,$WitchFound,$text,$element,$MaxPoints);         
        $this->fleetResourceMetal += $x['Size'];
    } 
    elseif ($WitchFound == 2) {
        $maxRandomTxt=3;
        $element= "Crystal";
        $x=$this->getSizeAndMessage($real_language_starter,$minRandom,$maxRandom,$minRandomTxt,$maxRandomTxt,$WitchFound,$text,$element,$MaxPoints);
        $this->fleetResourceCrystal += $x['Size'];
    }
    else{
        $maxRandomTxt=2;
        $element= "Deuterium";
        $x=$this->getSizeAndMessage($real_language_starter,$minRandom,$maxRandom,$minRandomTxt,$maxRandomTxt,$WitchFound,$text,$element,$MaxPoints);
        $this->fleetResourceDeuterium += $x['Size'];
    }
    $this->info['function']   = __METHOD__;
    return $x['Message'];
  }
  
  private function  foundDarkMatter($real_language_starter){
   
   
    $FindSize = mt_rand(1, 100);
    $minRandomTxt=1;
    $text="sys_expe_found_dm";
    $element="Darkmatter";
    
    if($FindSize<=50) {
        $maxRandomTxt=5;
        $WitchFound=1;
        $minRandom=100;
        $maxRandom=500;
    }
    elseif($FindSize<=80) {
        $maxRandomTxt=3;
        $WitchFound=2;
        $minRandom=501;
        $maxRandom=900;
    }
    else{
        $maxRandomTxt=2;
        $WitchFound=3;
        $minRandom=901;
        $maxRandom=2000;
    }
    $x=$this->getSizeAndMessage($real_language_starter,$minRandom,$maxRandom,$minRandomTxt,$maxRandomTxt,$WitchFound,$text,$element);                
    $this->fleetResourceDarkmatter += $x['Size'];
    $this->info['function']   = __METHOD__;
    return $x['Message'];
  }
  
  private function foundShips($real_language_starter){
   
           
    unset($this->fleetCount[208]);
    unset($this->fleetCount[209]);
    unset($this->fleetCount[214]);

    $FindSize = mt_rand(1, 100);
    if($FindSize > 10) {
        $Size        = mt_rand(2, 50);
        $Message    = 'sys_expe_found_ships_1_'.mt_rand(1,4);
        $MaxFound    = 300000;
    } 
    elseif($FindSize != 0) {
       $Size        = mt_rand(51, 100);
       $Message    = 'sys_expe_found_ships_2_'.mt_rand(1,2);
       $MaxFound    = 600000;
    } 
    else{
       $Size         = mt_rand(101, 200);
       $Message    = 'sys_expe_found_ships_3_'.mt_rand(1,2);
       $MaxFound    = 1200000;
    }
    $StatFactor = doquery("SELECT MAX(total_points) as total FROM {{table}} WHERE `stat_type` = '1';", 'statpoints', true);
                    
    $MaxPoints =($StatFactor['total'] < 5000000) ? 600 : 400;
                
    $Chanse = mt_rand (1, 2);
    $Chanse2 = mt_rand (1, 10);
    $RndNum = round((((($Chanse / $Chanse2) * $Size) / $MaxPoints) * $this->fleetStayDuration ), 2);

    for ($Ship = 202; $Ship < 216; $Ship++){
        if ($this->fleetCount[$Ship] != 0){
            $x = round($this->fleetCount[$Ship] * $RndNum, 0) + 1;
                if ($x > 0){
                    $this->fleetCount[$Ship] += $x; 
                    $FoundShip[$Ship] = $x;
                }
        }
    }
          
    foreach ($FoundShip as $Ship => $Count)
        $FoundShipMess   .= "<br>". $real_language_starter['tech'][$Ship].": ".$Count;    
    
    if($this->debug){
        foreach ($FoundShip as $Ship => $Count)
            if ($Count != 0)
                $this->info['Found_element_'.$real_language_starter['tech'][$Ship]]=$Count;   
        $this->info['function']                =__METHOD__;
        $this->info['Lang_key']                ="lang[".$Message."]";
        $this->info['Fleet_stay_duration']     =$this->fleetStayDuration;
        $this->info['RndNum']                  =$RndNum;
        $this->info['FindSize']                =$FindSize;
    }  
    return $real_language_starter[$Message]."".$FoundShipMess;
  }
  
  private function alienAttack($real_language_starter){ 
   
   
    $Chance    = mt_rand(1, 2);
    $TypeShipInArray = array("204","205","206","207","215","213");
    $rand_keys = array_rand($TypeShipInArray, 3);

    switch($Chance) 
    {
        case 1:
            $Points    = array(-3,-5,-8);
            $Which    = 1;
            $Def    = -3;
            $Name    = $real_language_starter['sys_expe_attackname_1'];
            $Add    =  mt_rand(1,5);
            $Rand    = array(5,3,2);
            $DefenderFleetArray    = $TypeShipInArray[$rand_keys[0]].",".(mt_rand(1,5)).";".$TypeShipInArray[$rand_keys[1]].",".(mt_rand(1,5)).";".$TypeShipInArray[$rand_keys[2]].",".(mt_rand(1,5)).";";
            break;
            
        case 2:
            $Points    = array(-4,-6,-9);
            $Which    = 2;
            $Def    = 1;
            $Name    = $real_language_starter['sys_expe_attackname_2'];
            $Add    =  mt_rand(1,5);
            $Rand    = array(4,3,2);
            $DefenderFleetArray    = $TypeShipInArray[$rand_keys[0]].",".(mt_rand(1,20)).";".$TypeShipInArray[$rand_keys[1]].",".(mt_rand(1,20)).";".$TypeShipInArray[$rand_keys[2]].",".(mt_rand(1,20)).";";
            break;
    }

    $FindSize = mt_rand(0, 100);
    if(20 < $FindSize){
        $Message            = $real_language_starter['sys_expe_attack_'.$Which.'_1_'.$Rand[0]];
        $MaxAttackerPoints    = 0.3 + $Add + abs(mt_rand($Points[0], abs($Points[0])) * 0.01);
    } 
    elseif(0 < $FindSize && 20 >= $FindSize){
        $Message            = $real_language_starter['sys_expe_attack_'.$Which.'_2_'.$Rand[1]];
        $MaxAttackerPoints    = 0.3 + $Add + abs(mt_rand($Points[1], abs($Points[1])) * 0.01);
    }
    elseif(0 == $FindSize){
        $Message            = $real_language_starter['sys_expe_attack_'.$Which.'_3_'.$Rand[2]];
        $MaxAttackerPoints    = 0.3 + $Add + abs(mt_rand($Points[2], abs($Points[2])) * 0.01);
    }
    foreach($this->fleetCount as $ID => $count){
        $DefenderFleetArray    .= $ID.",".round($count * $MaxAttackerPoints / mt_rand(1,10) * $this->fleetStayDuration).";";
    }

    $AttackerTechno    = doquery('SELECT id, username, military_tech, defence_tech, shield_tech FROM {{table}} WHERE id='.($this->fleetOwner).";", 'users', true);
    $DefenderTechno    = array(
    'id' => 0, 
    'username' => $Name, 
    'military_tech' => (mt_rand(0,5)), 
    'defence_tech' => (mt_rand(0,5)), 
    'shield_tech' => (mt_rand(0,5)));

    $attackFleets[$this->fleetId]['fleet'] = $this->fleetRow;
    $attackFleets[$this->fleetId]['user'] = $AttackerTechno;
    $attackFleets[$this->fleetId]['detail'] = array();
    foreach ($this->fleetCount as $ship => $number){

        if ($ship < 100) continue;

        if (!isset($attackFleets[$this->fleetId]['detail'][$ship]))
            $attackFleets[$this->fleetId]['detail'][$ship] = 0;

        $attackFleets[$this->fleetId]['detail'][$ship] += $number;
    }
    $defense = array();

    $defRowDef = explode(';', $DefenderFleetArray);
    foreach ($defRowDef as $Element){
        $Element = explode(',', $Element);
        if ($Element[0] < 100) continue;
        if (!isset($defense[$defRow['fleet_id']]['def'][$Element[0]]))
            $defense[0][$Element[0]] = 0;
        $defense[0]['def'][$Element[0]] += $Element[1];
    }
    $defense[0]['user'] = $DefenderTechno;

    $start         = microtime(true);
    $result     = calculateAttack($attackFleets, $defense );
    $totaltime     = microtime(true) - $start;

    foreach ($attackFleets as $fleetID => $attacker){
        $fleetArray = '';
        $totalCount = 0;
        foreach ($attacker['detail'] as $element => $amount){
            if ($amount)
                $fleetArray .= $element.','.$amount.';';
            $totalCount += $amount;
        }

        if ($totalCount <= 0)
            doquery('DELETE FROM {{table}} WHERE `fleet_id`='.$fleetID.';', 'fleets');
        else
            doquery('UPDATE {{table}} SET fleet_array="'.substr($fleetArray, 0, -1).'", fleet_amount='.$totalCount.', fleet_mess = 1 WHERE fleet_id='.$fleetID.';', 'fleets');                       
    }

    $formatted_cr             = formatCR($result, $steal, $MoonChance, $GottenMoon, $totaltime, $this->fleetRow);
    $raport         = $formatted_cr['html'];
    $rid               = md5($raport);

    $QryInsertRapport  = "INSERT INTO {{table}} SET ";
    $QryInsertRapport .= "`time` = UNIX_TIMESTAMP(),  ";
    $QryInsertRapport .= "`owners` = '".($this->fleetOwner).",0', ";
    $QryInsertRapport .= "`rid` = '". $rid ."', ";
    $QryInsertRapport .= "`a_zestrzelona` = '".count($result['rounds'])."', ";
    $QryInsertRapport .= "`raport` = '".mysql_real_escape_string( $raport )."';";    
    doquery($QryInsertRapport, 'rw') or die("Erro inserting CR to database".mysql_error()."<br /><br />Trying to execute:".mysql_query());

    if($result['won'] == "a")
          $style = "green";
    elseif ($result['won'] == "w")
          $style = "orange";
    elseif ($result['won'] == "r")
          $style = "red";
                            
    $raport  = "<a href=\"#\" style=\"color:".$style.";\" OnClick='f(\"CombatReport.php?raport=". $rid ."\", \"\");' >" . $real_language_starter['sys_mess_attack_report'] ." [". ($this->fleetEndGalaxy) .":". ($this->fleetEndSystem) .":". ($this->fleetEndPlanet) ."]</a>";
    SendSimpleMessage ( $this->fleetOwner, '', $this->fleetStartTime, 3, $real_language_starter['sys_mess_tower'], $raport, '' );
    $this->info['function']  = __METHOD__;
    return  $Message;
  }
  
  private function lostShips($real_language_starter){
   
   
    $LostAmount  =max(1,((20+$this->getProb())/100 )/(max(log($this->fleetPoints),1)));   
		$allDestroyed=true;
		
    foreach ($this->fleetCount as $Ship => $Number){
		    if(floor($Number * $LostAmount)!=0){
		        $allDestroyed=false;
				    $this->fleetCount[$Ship]  = floor($Number * $LostAmount); 
			  }
			  else
			    unset($this->fleetCount[$Ship]);
		}
		$Message= ($allDestroyed)
    ? 'sys_expe_blackholl_1'
		: 'sys_expe_lost_fleet_'.mt_rand(1,4);
		 
		if($this->debug){
        foreach ($this->fleetCount as $Ship => $Number)
            $this->info['Rest_element_'.$real_language_starter['tech'][$Ship]]=$Number;  
        $this->info['function']      = __METHOD__;
        $this->info['Lang_key']      = "lang[".$Message."]";
        $this->info['Fleet_points']  = $this->fleetPoints;
        $this->info['Lost_amount']   = $LostAmount;
        $this->info['all_destroyed'] = $allDestroyed;
    }   
    return  $real_language_starter[$Message];
  }
   
  private function timeShift($real_language_starter){
   
            
    $MoreTime     = mt_rand(1, 100);
    if($MoreTime < 75) {
        $this->fleetEndTime = round($this->fleetEndStay + ($this->fleetEndTime - $this->fleetEndStay) + ((($this->fleetEndStay - $this->fleetStartTime) / 3600) * mt_rand(1, 5)));
        $Message = 'sys_expe_time_slow_'.mt_rand(1,6);
    } 
    else {
        $this->fleetEndTime = round($this->fleetEndStay + ($this->fleetEndTime - $this->fleetEndStay) / 2);
        $Message = 'sys_expe_time_fast_'.mt_rand(1,3);
    }            
    
    if($this->debug){
        $this->info['function']       = __METHOD__;
        $this->info['Lang_key']       = "lang[".$Message."]";
        $this->info['More_time']      = $MoreTime;
        $this->info['fleet_end_time'] = date ("d-m-Y H:i:s",$this->fleetEndTime);    
    }    
    return  $real_language_starter[$Message];
  }
  
  private function notFound($real_language_starter){ 
   
    
    $Message='sys_expe_nothing_'.mt_rand(1,8);                  
    
    if($this->debug){
        $this->info['function']  =__METHOD__;
        $this->info['Lang_key']  = "lang[".$Message."]";
    }
    return  $real_language_starter[$Message];
  } 
  /**
   *Function for calculate destruction probability; return: % to destroy (min=0, max=90)
   * */
  private function getProb() {  //0 to 90
    $random = mt_rand(1,450);
    $lastprob=0;
    for($i=10;$i+$lastprob<450;$i=$i+10) {
        if($random<=$i+$lastprob) 
          return $i-10;
        $lastprob+=$i;
    }
    return 1;
  }
  /**
   *Function for calculate found resources and return message
   * */
  private function getSizeAndMessage($real_language_starter,$minRandom,$maxRandom,$minRandomTxt,$maxRandomTxt,$WitchFound,$text,$element,$MaxPoints=false){
    global  $game_config;
   
    if($element!="Darkmatter"){
      $Factor     = (mt_rand($minRandom,$maxRandom) / $WitchFound) * $game_config['resource_multiplier'] * $this->fleetStayDuration;
      $x['Size']  = min($Factor * max(min($this->fleetPoints / ($WitchFound*1000), $MaxPoints), 200), $this->fleetCapacity);
    }
   else{
      $Factor     = mt_rand($minRandom,$maxRandom)* $this->fleetStayDuration;
      $x['Size']  = min($Factor * max($this->fleetPoints / 1000, 200)/1000, $this->fleetCapacity);
   }
   $real_language_starterName=$text."_".$WitchFound."_".mt_rand($minRandomTxt,$maxRandomTxt);
   $x['Message']    = sprintf($real_language_starter[$real_language_starterName], pretty_number($x['Size']), $real_language_starter[$element]);  
   
   if($this->debug){
     $this->info['Found_element']          =$element;
     if ($element != "Darkmatter")
        $this->info['Found_element']       .= "=".$WitchFound;
     $this->info['Found_quantity']          =$x['Size'];
     $this->info['Lang_key']                ="lang[".$real_language_starterName."]";
     $this->info['Fleet_capacity']          =$this->fleetCapacity;
     $this->info['Fleet_stay_duration']     =$this->fleetStayDuration;
     $this->info['Fleet_points']            =$this->fleetPoints; 
     $this->info['Resource_multiplier']     =$game_config['resource_multiplier'];     
  }
  return $x;         
}
/**
 *function for send report message to admin
 * */
private function debugMessage($real_language_starter){
  
   $txt ='<div align="center"><table border="1">';
   $txt.='<tr><td colspan="2" class="c"><div align="center"><font color=\"red\">------DEBUG-----</font></div></td></tr> ';
   
    foreach($this->info as $key => $value){
         $txt.='<tr>';
         $txt.= '<th><font color="LIME">'.$key.'</font></th>';
         $txt.= '<th>'.$value.'</th>';
         $txt.='</tr>';
    }
    $txt.='</table></div>';
    SendSimpleMessage(1, '', $this->fleetEndStay, 15, $real_language_starter['sys_mess_tower'], $real_language_starter['sys_expe_report'], $txt);    
    if( $this->toAll==true && $this->fleetOwner != 1  )
        SendSimpleMessage($this->fleetOwner, '', $this->fleetEndStay, 15, $real_language_starter['sys_mess_tower'], $real_language_starter['sys_expe_report'], $txt);  
}
  
  
  /**
   *Function for update fleet status
   * */
  private function updateFleetStatus($return=1){  
    $fleetArray="";
    foreach ($this->fleetCount as $ship => $number)
        $fleetArray .= $ship.','.$number.';';
    
    if(count($this->fleetCount)==0){
        doquery ("DELETE FROM {{table}} WHERE `fleet_id` = ". ($this->fleetId), 'fleets');
        return;
    } 
    $QryUpdateFleet  = "UPDATE {{table}} SET ";
    $QryUpdateFleet .= "`fleet_resource_metal`      = `fleet_resource_metal` +      '".($this->fleetResourceMetal)."', ";
    $QryUpdateFleet .= "`fleet_resource_crystal`    = `fleet_resource_crystal` +    '".($this->fleetResourceCrystal)."', ";
    $QryUpdateFleet .= "`fleet_resource_deuterium`  = `fleet_resource_deuterium` +  '".($this->fleetResourceDeuterium)."', ";
    $QryUpdateFleet .= "`fleet_resource_darkmatter` = `fleet_resource_darkmatter` + '".($this->fleetResourceDarkmatter)."', ";
    $QryUpdateFleet .= "`fleet_array` = '".$fleetArray."', ";
    $QryUpdateFleet .= "`fleet_end_time` = '".($this->fleetEndTime)."', ";
    $QryUpdateFleet .= "`fleet_mess` = '".$return."' ";
    $QryUpdateFleet .= "WHERE ";
    $QryUpdateFleet .= "`fleet_id` = '".($this->fleetId)."';";
    doquery ( $QryUpdateFleet,'fleets' );   
  }
  private function foundSupernova($real_language_starter){
   global $game_config,$real_language_starter;
   $message=$this->timeShift();
   $rand=mt_rand(1,20);
   if($rand <= 3 ){  //supernova
      if(isset($this->fleetCount[216]))
         $this->fleetCount[216] = $this->fleetCount[216]+1;
      else
         $this->fleetCount[216] =1;
      $Message='sys_expe_foundSupernova_1';
   }
	elseif($rand>=17){   //missili
		$tempvar1 			= abs($this->fleetEndSystem - $this->fleetStartSystem);
		$flugzeit = round(((30 + (60 * $tempvar1)) * 2500) / $game_config['fleet_speed']);
		$anz=mt_rand(20,100);
		doquery("INSERT INTO {{table}} SET
		`fleet_owner` = '".($this->fleetOwner)."',
		`fleet_mission` = 10,
		`fleet_amount` = ".$anz.",
		`fleet_array` = '503,".$anz."',
		`fleet_start_time` = '".(time() + $flugzeit)."',
		`fleet_start_universe` = '".($this->fleetEndUniverse)."',
		`fleet_start_galaxy` = '".($this->fleetEndGalaxy)."',
		`fleet_start_system` = '".($this->fleetEndSystem)."',
		`fleet_start_planet` ='".($this->fleetEndPlanet) ."',
		`fleet_start_type` = 1,
		`fleet_end_time` = '".(time() + $flugzeit+1)."',
		`fleet_end_stay` = 0,
		`fleet_end_universe` = '".($this->fleetStartUniverse)."',
		`fleet_end_galaxy` = '".($this->fleetStartGalaxy)."',
		`fleet_end_system` = '".($this->fleetStartSystem)."',
		`fleet_end_planet` = '".($this->fleetStartPlanet)."',
		`fleet_end_type` = 1,
		`fleet_target_obj` = 'all',
		`fleet_resource_metal` = 0,
		`fleet_resource_crystal` = 0,
		`fleet_resource_deuterium` = 0,
		`fleet_target_owner` = '".($this->fleetOwner)."',
		`fleet_group` = 0,
		`fleet_mess` = 0,
		`start_time` = ".time().";", 'fleets');
		$Message='sys_expe_foundSupernova_3';
	}
   else
		$Message=  'sys_expe_foundSupernova_2';
  
  if($this->debug){
        $this->info['function']  =__METHOD__;
        $this->info['Lang_key']  = "lang[".$Message."]";
   }
   return $real_language_starter[$Message];
   }
}  
?>
