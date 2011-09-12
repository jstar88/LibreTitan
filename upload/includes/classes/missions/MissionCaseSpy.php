<?php
class MissionCaseSpy extends FlyingFleetHandler
{
   public function MissionCaseSpy($FleetRow)
	{
		global $lang, $resource,$user;

		if ($this->isArriveToDestination($FleetRow))
		{
        
			$CurrentUser      = $this->getStartUser($FleetRow);
         $CurrentUserID    = $FleetRow['fleet_owner'];
         $CurrentSpyLvl    = $CurrentUser['spy_tech'] + ($CurrentUser['rpg_espion'] * ESPION);
         $CurrentPlanet    = $this->getStartPlanetInfoInCache($FleetRow);
         
         $TargetUser       = $this->getTargetUser($FleetRow);
         $TargetUserID     = $TargetPlanet['id_owner'];
         $TargetSpyLvl     = $TargetUser['spy_tech'] + ($TargetUser['rpg_espion'] * ESPION);     		
         $TargetPlanet     = $this->getTargetPlanetInfoInCache($FleetRow);			
			
			$fleet               = explode(";", $FleetRow['fleet_array']);

         $real_language_target=getRealLanguage($user['id'],$FleetRow['fleet_target_owner']);  
		   $real_language_starter =getRealLanguage($user['id'],$FleetRow['fleet_owner']);
         if(empty($real_language_target))
		      $real_language_target=$lang;
		   if(empty($real_language_starter))
		      $real_language_starter=$lang;

			PlanetResourceUpdate ( $TargetUser, $TargetPlanet, time() );

			foreach ($fleet as $a => $b)   //permettere di ricevere lo spionaggio anche se invio altre navi
			{
				if ($b != '')
				{
					$a = explode(",", $b);
					
					if ($a[0] == "210")
					{
						
						$LS    = $a[1];  //numero sonde spia
						$QryTargetGalaxy  = "SELECT crystal FROM {{table}} WHERE ";
						$QryTargetGalaxy .= "`universe` = '". $FleetRow['fleet_end_universe'] ."' AND ";
						$QryTargetGalaxy .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
						$QryTargetGalaxy .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
						$QryTargetGalaxy .= "`planet` = '". $FleetRow['fleet_end_planet'] ."';";
						$TargetGalaxy     = doquery( $QryTargetGalaxy, 'galaxy', true);
						$CristalDebris    = $TargetGalaxy['crystal'];
						$SpyToolDebris    = $LS * 300;

						$MaterialsInfo    = $this->SpyTarget ( $TargetPlanet, 0, $real_language_starter['sys_spy_maretials'],$real_language_starter );
						$Materials        = $MaterialsInfo['String'];

						$PlanetFleetInfo  = $this->SpyTarget ( $TargetPlanet, 1, $real_language_starter['sys_spy_fleet'],$real_language_starter );
						$PlanetFleet      = $Materials;
						$PlanetFleet     .= $PlanetFleetInfo['String'];
						
						$PlanetDefenInfo  = $this->SpyTarget ( $TargetPlanet, 2, $real_language_starter['sys_spy_defenses'],$real_language_starter );
						$PlanetDefense    = $PlanetFleet;
						$PlanetDefense   .= $PlanetDefenInfo['String'];

						$PlanetBuildInfo  = $this->SpyTarget ( $TargetPlanet, 3, $real_language_starter['tech'][0],$real_language_starter );
						$PlanetBuildings  = $PlanetDefense;
						$PlanetBuildings .= $PlanetBuildInfo['String'];

						$TargetTechnInfo  = $this->SpyTarget ( $TargetUser, 4, $real_language_starter['tech'][100],$real_language_starter );
						$TargetTechnos    = $PlanetBuildings;
						$TargetTechnos   .= $TargetTechnInfo['String'];

						$TargetForce      = ($PlanetFleetInfo['Count'] * $LS) / 4;

						if ($TargetForce > 100)
							$TargetForce = 100;

						$TargetChances = rand(0, $TargetForce);
						$SpyerChances  = rand(0, 100);

						if ($TargetChances >= $SpyerChances)
							$DestProba = "<font color=\"red\">".$real_language_starter['sys_mess_spy_destroyed']."</font>";
						elseif ($TargetChances < $SpyerChances)
							$DestProba = sprintf( $real_language_starter['sys_mess_spy_lostproba'], $TargetChances);

						$AttackLink = "<center>";
							//start mod
						$AttackLink .= "<a href=\"game.php?page=fleet&universe=". $FleetRow['fleet_end_universe'] ."&galaxy=".$FleetRow['fleet_end_galaxy'] ."&system=". $FleetRow['fleet_end_system'] ."";
						   //end mod
           	     $AttackLink .= "&planet=".$FleetRow['fleet_end_planet']."&planettype=".$FleetRow['fleet_end_type']."";
						$AttackLink .= "&target_mission=1";
						$AttackLink .= " \">". $real_language_starter['type_mission'][1] ."";
						$AttackLink .= "</a></center>";
						$MessageEnd  = "<center>".$DestProba."</center>";

						$pT = ($TargetSpyLvl - $CurrentSpyLvl);
						$pW = ($CurrentSpyLvl - $TargetSpyLvl);
						if ($TargetSpyLvl > $CurrentSpyLvl)
							$ST = ($LS - pow($pT, 2));
						if ($CurrentSpyLvl > $TargetSpyLvl)
							$ST = ($LS + pow($pW, 2));
						if ($TargetSpyLvl == $CurrentSpyLvl)
							$ST = $CurrentSpyLvl;
						if ($ST <= "1")
							$SpyMessage = $Materials."<br />".$AttackLink.$MessageEnd;
						if ($ST == "2")
							$SpyMessage = $PlanetFleet."<br />".$AttackLink.$MessageEnd;
						if ($ST == "4" or $ST == "3")
							$SpyMessage = $PlanetDefense."<br />".$AttackLink.$MessageEnd;
						if ($ST == "5" or $ST == "6")
							$SpyMessage = $PlanetBuildings."<br />".$AttackLink.$MessageEnd;
						if ($ST >= "7")
							$SpyMessage = $TargetTechnos."<br />".$AttackLink.$MessageEnd;

						SendSimpleMessage ( $CurrentUserID, '', $FleetRow['fleet_start_time'], 0, $real_language_starter['sys_mess_qg'], $real_language_starter['sys_mess_spy_report'], $SpyMessage);

						$TargetMessage  = $real_language_target['sys_mess_spy_ennemyfleet'] ." ". $CurrentPlanet['name'];

						if($FleetRow['fleet_start_type'] == 3)
							$TargetMessage .= $real_language_target['sys_mess_spy_report_moon'] . " ";
               //start mod
						$TargetMessage .= "<a href=\"game.php?page=galaxy&mode=3&universe=".$CurrentPlanet["universe"] ."&galaxy=". $CurrentPlanet["galaxy"] ."&system=". $CurrentPlanet["system"] ."\">";
						$TargetMessage .= "[".$CurrentPlanet["universe"] .":". $CurrentPlanet["galaxy"] .":". $CurrentPlanet["system"] .":". $CurrentPlanet["planet"] ."]</a> ";
						$TargetMessage .= $real_language_target['sys_mess_spy_seen_at'] ." ". $TargetPlanet['name'];
						$TargetMessage .= " [".$TargetPlanet["universe"] .":". $TargetPlanet["galaxy"] .":". $TargetPlanet["system"] .":". $TargetPlanet["planet"] ."].";
               //end mod
						SendSimpleMessage ( $TargetUserID, '', $FleetRow['fleet_start_time'], 0, $real_language_target['sys_mess_spy_control'], $real_language_target['sys_mess_spy_activity'], $TargetMessage);
					   if ($TargetChances >= $SpyerChances)
					   {
						   $QryUpdateGalaxy  = "UPDATE {{table}} SET ";
						   $QryUpdateGalaxy .= "`crystal` = `crystal` + '". (0 + $SpyToolDebris) ."' ";
						   $QryUpdateGalaxy .= "WHERE `id_planet` = '". $TargetPlanet['id'] ."';";
						   doquery( $QryUpdateGalaxy, 'galaxy');
					    	doquery("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
					   }
					 
               }
				}
			}
		 doquery("UPDATE {{table}} SET `fleet_mess` = '1' WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');		
		}   
		elseif ($this->isReturnedToHome($FleetRow))
		{
			$this->RestoreFleetToPlanet ( $FleetRow, true );
			doquery("DELETE FROM {{table}} WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
		}				
	}
	
	private function SpyTarget ($TargetPlanet, $Mode, $TitleString, $lang)
	{
		global $resource;

		$LookAtLoop = true;
		if ($Mode == 0)
		{
			$String  = "<table width=\"440\"><tr><td class=\"c\" colspan=\"5\">";
			$String .= $TitleString ." ". $TargetPlanet['name'];
			//start mod
			$String .= " <a href=\"game.php?page=galaxy&mode=3&universe=".$TargetPlanet["universe"]."&galaxy=". $TargetPlanet["galaxy"] ."&system=". $TargetPlanet["system"]. "\">";
      $String .= "[". $TargetPlanet["universe"] .":".$TargetPlanet["galaxy"] .":". $TargetPlanet["system"] .":". $TargetPlanet["planet"] ."]</a>";
			//end mod
      $String .= $lang['sys_the'] . date("d-m-Y H:i:s", time()) ."</td>"; 
      $String .= "</tr><tr>";
			$String .= "<td width=220>". $lang['Metal']     ."</td><td width=220 align=right>". pretty_number($TargetPlanet['metal'])      ."</td><td>&nbsp;</td>";
			$String .= "<td width=220>". $lang['Crystal']   ."</td></td><td width=220 align=right>". pretty_number($TargetPlanet['crystal'])    ."</td>";
			$String .= "</tr><tr>";
			$String .= "<td width=220>". $lang['Deuterium'] ."</td><td width=220 align=right>". pretty_number($TargetPlanet['deuterium'])  ."</td><td>&nbsp;</td>";
			$String .= "<td width=220>". $lang['Energy']    ."</td><td width=220 align=right>". pretty_number($TargetPlanet['energy_max']) ."</td>";
			$String .= "</tr>";
			$LookAtLoop = false;
		}
		elseif ($Mode == 1)
		{
			$ResFrom[0] = 200;
			$ResTo[0]   = 299;
			$Loops      = 1;
		}
		elseif ($Mode == 2)
		{
			$ResFrom[0] = 400;
			$ResTo[0]   = 499;
			$ResFrom[1] = 500;
			$ResTo[1]   = 599;
			$Loops      = 2;
		}
		elseif ($Mode == 3)
		{
			$ResFrom[0] = 1;
			$ResTo[0]   = 99;
			$Loops      = 1;
		}
		elseif ($Mode == 4)
		{
			$ResFrom[0] = 100;
			$ResTo[0]   = 199;
			$Loops      = 1;
		}

		if ($LookAtLoop == true)
		{
			$String  = "<table width=\"440\" cellspacing=\"1\"><tr><td class=\"c\" colspan=\"". ((2 * SPY_REPORT_ROW) + (SPY_REPORT_ROW - 1))."\">". $TitleString ."</td></tr>";
			$Count       = 0;
			$CurrentLook = 0;
			while ($CurrentLook < $Loops)
			{
				$row     = 0;
				for ($Item = $ResFrom[$CurrentLook]; $Item <= $ResTo[$CurrentLook]; $Item++)
				{
					if ( $TargetPlanet[$resource[$Item]] > 0)
					{
						if ($row == 0)
							$String  .= "<tr>";

						$String  .= "<td align=left>".$lang['tech'][$Item]."</td><td align=right>".$TargetPlanet[$resource[$Item]]."</td>";
						if ($row < SPY_REPORT_ROW - 1)
							$String  .= "<td>&nbsp;</td>";

						$Count   += $TargetPlanet[$resource[$Item]];
						$row++;
						if ($row == SPY_REPORT_ROW)
						{
							$String  .= "</tr>";
							$row      = 0;
						}
					}
				}

				while ($row != 0)
				{
					$String  .= "<td>&nbsp;</td><td>&nbsp;</td>";
					$row++;
					if ($row == SPY_REPORT_ROW)
					{
						$String  .= "</tr>";
						$row      = 0;
					}
				}
				$CurrentLook++;
			}
		}
		$String .= "</table>";

		$return['String'] = $String;
		$return['Count']  = $Count;

		return $return;
	}
}
   //fixed
?>
