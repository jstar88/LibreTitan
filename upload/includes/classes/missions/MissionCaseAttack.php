<?php
class MissionCaseAttack extends FlyingFleetHandler
{
   public function MissionCaseAttack ($FleetRow)
	{
		global $lang, $pricelist, $resource, $CombatCaps, $game_config,$user;

		if ($this->isArriveToDestination($FleetRow))
		{ 
			$targetPlanet = $this->getTargetPlanetInfoInCache($FleetRow);
			$targetUser   = $this->getTargetUser($FleetRow);
			$TargetUserID = $targetUser['id'];
			$targetUniverse=$FleetRow['fleet_end_universe'];
         $targetGalaxy  =$FleetRow['fleet_end_galaxy'];
         $targetSystem  =$FleetRow['fleet_end_system'];
         $targetPlanet  =$FleetRow['fleet_end_planet'];
         $targetType  =$FleetRow['fleet_end_type'];
			
         
			if ($FleetRow['fleet_group'] > 0)
			{
				doquery("DELETE FROM {{table}} WHERE id =".intval($FleetRow['fleet_group']),'aks');
				doquery("UPDATE {{table}} SET fleet_mess=1 WHERE fleet_group=".$FleetRow['fleet_group'],'fleets');
			}
			else
			{
				doquery("UPDATE {{table}} SET fleet_mess=1 WHERE fleet_id=".intval($FleetRow['fleet_id']),'fleets');
			}		

			PlanetResourceUpdate ( $targetUser, $targetPlanet, time() );
       
         $attackFleets = array();
         
			if ($FleetRow['fleet_group'] != 0)
			{
				$fleets = doquery('SELECT * FROM {{table}} WHERE fleet_group='.$FleetRow['fleet_group'],'fleets');
				while ($fleet = mysql_fetch_assoc($fleets))
				  $this->inizializeFleets($attackFleets,$fleet);					
			}
			else
				$this->inizializeFleets($attackFleets,$FleetRow);
				    
			//stazionamento
         $defStay = doquery('SELECT * FROM {{table}} WHERE 
			`fleet_end_universe` = '.   $targetUniverse .'
         AND `fleet_end_galaxy` = '. $targetGalaxy .'
         AND `fleet_end_system` = '. $targetSystem .' 
         AND `fleet_end_planet` = '. $targetPlanet .' 
         AND `fleet_end_type` = '.   $targetType .' 
         AND fleet_start_time<'.time().' 
         AND fleet_end_stay>='.time(),'fleets');
       
         $defense = array();
         
			while ($fleet = mysql_fetch_assoc($defStay))
				  $this->inizializeFleets($defense,$fleet);	

			$defense[0]['def'] = array();
			$defense[0]['user'] = $targetUser;
			
			$this->inizializeDefense($defense,$targetPlanet);
         
			/*
         *
         */
			$start 		= microtime(true);
			$result 	= calculateAttack($attackFleets, $defense);
			$totaltime 	= microtime(true) - $start;
         /**
          *
          */                   
         $steal = array('metal' => 0, 'crystal' => 0, 'deuterium' => 0);
			if ($result['won'] == "a")
				$steal = self::calculateAKSSteal($attackFleets, $targetPlanet);	

         $this->updateFleet($attackFleets);
			$this->updateFleet($defense,$steal,$targetUniverse,$targetSystem,$targetPlanet,$targetType);
         
         $metal   =$result['debree']['att'][0]+$result['debree']['def'][0];
         $crystal =$result['debree']['att'][1]+$result['debree']['def'][1];
         $totalDebree = $metal+$crystal;
         $this->storeDebreeToGalaxy($metal,$crystal,$FleetRow['fleet_end_universe'],$FleetRow['fleet_end_galaxy'],$FleetRow['fleet_end_system'],$FleetRow['fleet_end_planet']);		 		
         
         $MoonChance=$this->getMoonProb($totalDebree);
			
			if($targetPlanet['have_moon']){
            $TargetPlanetName=$this->tryMoon($totalDebree,$universe,$galaxy,$planet,$targetUserID,$FleetRow['fleet_start_time']);
            $mooon= !empty($name);
         } 
         else
         {
            $moon=false;
         } 
			foreach ($attackFleets as $fleetID => $attacker)
			{
				$users2[$attacker['user']['id']] = $attacker['user']['id'];
			}

			foreach ($defense as $fleetID => $defender)
			{
				$users2[$defender['user']['id']] = $defender['user']['id'];
			}
			
			foreach ($attackFleets as $fleetID => $attacker)
			{
				if($result['won'] == "a")
			   {
				  $style = "green";
			   }  
			   elseif ($result['won'] == "w")
			   {
				  $style = "orange";
		     	}
			   elseif ($result['won'] == "r")
			   {
				  $style = "red";
			   }
            $this-> sendRaport($result,$attacker['fleet'],$attacker['user']['id'],$style,$user2,$moon,$MoonChance,$totaltime,$steal,$TargetPlanetName);
			}

			foreach ($defense as $fleetID => $defender)
			{
			   if($result['won'] == "a")
		    	{
			   	$style = "red";
			   }
		    	elseif ($result['won'] == "w")
			   {
				  $style = "orange";
			   }
		    	elseif ($result['won'] == "r")
			   {
				  $style = "green";
		    	}
			   $this-> sendRaport($result,$defender['fleet'],$defender['user']['id'],$style,$user2,$moon,$MoonChance,$totaltime,$steal,$TargetPlanetName);
			}	
       
		}
		elseif ($this->isReturnedToHome($FleetRow))
		{
	     $real_language=getRealLanguage($user['id'],$FleetRow['fleet_owner']);
        if(empty($real_language))
          $real_language=$lang;
		
		   	$Message         = sprintf( $real_language['sys_fleet_won'],
						$TargetName, GetTargetAdressLink($FleetRow, ''),
						pretty_number($FleetRow['fleet_resource_metal']), $real_language['Metal'],
						pretty_number($FleetRow['fleet_resource_crystal']), $real_language['Crystal'],
						pretty_number($FleetRow['fleet_resource_deuterium']), $real_language['Deuterium'] );
			SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_end_time'], 3, $real_language['sys_mess_tower'], $real_language['sys_mess_fleetback'], $Message);
			$this->RestoreFleetToPlanet($FleetRow);
			doquery ('DELETE FROM {{table}} WHERE `fleet_id`='.intval($FleetRow['fleet_id']),'fleets');
		}
	}

   private function sendRaport($result,$FleetRow,$ID,$style,$totID,$moon,$MoonChance,$totaltime,$steal,$TargetPlanetName){
         global $lang,$user,$mem;
            $real_language=getRealLanguage($user['id'],$ID);
            if(empty($real_language))
               $real_language=$lang;
            if(isset($mem)&& isset($mem[$real_language]))
            {
               $rid=$mem[$real_language];
            }
            else
            {
               if($moon){
                  $GottenMoon       = sprintf ($real_language['sys_moonbuilt'], $TargetPlanetName, $FleetRow['fleet_end_universe'],$FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
				      $GottenMoon .= "<br />";
               }
               else
				      $GottenMoon="";
               $formatted_cr 	= formatCR($result,$steal,$MoonChance,$GottenMoon,$totaltime,$real_language); //global $lang
			      $raport 		= $formatted_cr['html'];
               $rid=md5($raport);
               $QryInsertRapport  = 'INSERT INTO {{table}} SET ';
		       	$QryInsertRapport .= '`time` = UNIX_TIMESTAMP(), ';
               $QryInsertRapport .= '`owners` = "'.implode(',', $totID).'", ';
			      $QryInsertRapport .= '`rid` = "'. $rid .'", ';
			      $QryInsertRapport .= '`a_zestrzelona` = "'.$formatted_cr['destroyed'].'", ';
			      $QryInsertRapport .= '`raport` = "'.  $raport  .'"';
			      doquery($QryInsertRapport,'rw') or die("Error inserting CR to database".mysql_error()."<br /><br />Trying to execute:".mysql_query()); 
         
               $mem[$real_language]=$rid;   
            }
			
         
         $raport  = "<a href=\"#\" style=\"color:".$style.";\" OnClick='f(\"CombatReport.php?raport=". $rid ."\", \"\");' >" . $real_language['sys_mess_attack_report'] ." [". $FleetRow['fleet_end_universe'] .":". $FleetRow['fleet_end_galaxy'] .":". $FleetRow['fleet_end_system'] .":". $FleetRow['fleet_end_planet'] ."]</a>";
			SendSimpleMessage ( $ID, '', $FleetRow['fleet_start_time'], 3, $real_language['sys_mess_tower'], $raport, '' );
         
   }
   private function inizializeFleets(&$fleets,$fleet)
   {
      $fleets[$fleet['fleet_id']]['fleet'] = $fleet;
		$fleets[$fleet['fleet_id']]['user']  = $this->getStartUser($fleet); 
		$fleets[$fleet['fleet_id']]['detail'] = array();
		$waves = explode(';', $fleet['fleet_array']);
		foreach ($waves as $wave)
		{
			$wave = explode(',', $wave);
			if (!isset($fleets[$fleet['fleet_id']]['detail'][$wave[0]]))
			   $fleets[$fleet['fleet_id']]['detail'][$wave[0]] = 0;
			$fleets[$fleet['fleet_id']]['detail'][$wave[0]] += $wave[1];
		}
   }
   private function inizializeDefense(&$defense,$targetPlanet){
    global $resource;
      for ($i = 202; $i <= 216; $i++)	
				if (!empty($targetPlanet[$resource[$i]]))
					$defense[0]['def'][$i] = $targetPlanet[$resource[$i]];
	
   	for ($i = 401; $i <= 409; $i++)
			if (!empty($targetPlanet[$resource[$i]]))
				$defense[0]['def'][$i] = $targetPlanet[$resource[$i]];
   }
   
   private function updateFleet($Fleets,$steal=false,$targetUniverse=false,$targetSystem=false,$targetPlanet=false,$targetType=false){
      foreach ($Fleets as $fleetID => $player)
			{
            if ($fleetID != 0)
            {
              
				  $fleetArray = '';
				  $totalCount = 0;
				  foreach ($player['detail'] as $element => $amount)
				  {
					 if (!empty($amount))
					 {
				     $fleetArray .= $element.','.$amount.';';
                 $totalCount += $amount;
                }
				  }

			   	if ($totalCount <= 0)
				  {
					 doquery ('DELETE FROM {{table}} WHERE `fleet_id`='.intval($fleetID),'fleets');
				  }
				  else
				  {
				  	doquery ('UPDATE {{table}} SET fleet_array="'.substr($fleetArray, 0, -1).'", fleet_amount='.$totalCount.', fleet_mess=1 WHERE fleet_id='.intval($fleetID),'fleets');
				  }
			   }
			   else //defense
				{
					$defenseArray = '';

					foreach ($defender['def'] as $element => $amount)
					{
						$defenseArray .= '`'.$resource[$element].'`='.$amount.', ';
					}

					$QryUpdateTarget  = "UPDATE {{table}} SET ";
					$QryUpdateTarget .= $defenseArray;
					$QryUpdateTarget .= "`metal` = `metal` - '". $steal['metal'] ."', ";
					$QryUpdateTarget .= "`crystal` = `crystal` - '". $steal['crystal'] ."', ";
					$QryUpdateTarget .= "`deuterium` = `deuterium` - '". $steal['deuterium'] ."' ";
					$QryUpdateTarget .= "WHERE ";
					$QryUpdateTarget .= "`universe` = '". $targetUniverse ."' AND ";
					$QryUpdateTarget .= "`galaxy` = '". $targetGalaxy ."' AND ";					
					$QryUpdateTarget .= "`system` = '". $targetSystem ."' AND ";
					$QryUpdateTarget .= "`planet` = '". $targetPlanet ."' AND ";
					$QryUpdateTarget .= "`planet_type` = '". $targetType ."' ";
					$QryUpdateTarget .= "LIMIT 1;";
					doquery( $QryUpdateTarget , 'planets');
				}
		}
   }
}
?>
