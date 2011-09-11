<?php
class MissionCaseAttack extends FlyingFleetHandler{
   public function MissionCaseColonisation($FleetRow)
	{
		global $lang, $resource,$user;
		
		if (!$this->isMissionEnded($FleetRow))
		{
		   $real_language_target=getRealLanguage($user['id'],$FleetRow['fleet_target_owner']);  
		$real_language_starter =getRealLanguage($user['id'],$FleetRow['fleet_owner']);
      if(empty($real_language_target))
		 $real_language_target=$lang;
		if(empty($real_language_starter))
		 $real_language_starter=$lang; 
		//start mod
			$iGalaxyPlace = mysql_result(doquery ("SELECT count(*) FROM {{table}} WHERE 
      `universe` = '". $FleetRow['fleet_end_universe']."' AND
      `galaxy` = '". $FleetRow['fleet_end_galaxy']."' AND 
      `system` = '". $FleetRow['fleet_end_system']."' AND 
      `planet` = '". $FleetRow['fleet_end_planet']."';", 'galaxy'), 0);
			$TargetAdress = sprintf ($real_language_starter['sys_adress_planet'],$FleetRow['fleet_end_universe'], $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
		   $TargetAdressTarget = sprintf ($real_language_target['sys_adress_planet'],$FleetRow['fleet_end_universe'], $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
		
      //end mod
    	if ($iGalaxyPlace == 0)
			{
			   $iPlanetCount = mysql_result(doquery ("SELECT count(*) FROM {{table}} WHERE `id_owner` = '". $FleetRow['fleet_owner'] ."' AND `planet_type` = '1' AND `destruyed` = '0'", 'planets'), 0);

				if ($iPlanetCount >= MAX_PLAYER_PLANETS)
				{
					$TheMessage = $real_language_starter['sys_colo_arrival'] . $TargetAdress . $real_language_starter['sys_colo_maxcolo'] . MAX_PLAYER_PLANETS . $real_language_starter['sys_colo_planet'];
					SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 0, $real_language_starter['sys_colo_mess_from'], $real_language_starter['sys_colo_mess_report'], $TheMessage);
					doquery("UPDATE {{table}} SET `fleet_mess` = '1' WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
				}
				else
				{
					$NewOwnerPlanet = CreateOnePlanetRecord($FleetRow['fleet_end_universe'],$FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet'], $FleetRow['fleet_owner'], $lang['sys_colo_defaultname'], false);
					if ( $NewOwnerPlanet == true )
					{
						$TheMessage = $real_language_target['sys_colo_arrival'] . $TargetAdressTarget . $real_language_target['sys_colo_allisok'];
						SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 0, $real_language_target['sys_colo_mess_from'], $real_language_target['sys_colo_mess_report'], $TheMessage);
						if ($FleetRow['fleet_amount'] == 1)
						{
							$this->StoreGoodsToPlanet ($FleetRow);
							doquery("DELETE FROM {{table}} WHERE fleet_id=" . $FleetRow["fleet_id"], 'fleets');
						}
						else
						{
							$this->StoreGoodsToPlanet ($FleetRow);
							$CurrentFleet = explode(";", $FleetRow['fleet_array']);
							$NewFleet     = "";
							foreach ($CurrentFleet as $Item => $Group)
							{
								if ($Group != '')
								{
									$Class = explode (",", $Group);
									if ($Class[0] == 208)
									{
										if ($Class[1] > 1)
										{
											$NewFleet  .= $Class[0].",".($Class[1] - 1).";";
										}
									}
									else
									{
										if ($Class[1] <> 0)
										{
											$NewFleet  .= $Class[0].",".$Class[1].";";
										}
									}
								}
							}
							$QryUpdateFleet  = "UPDATE {{table}} SET ";
							$QryUpdateFleet .= "`fleet_array` = '". $NewFleet ."', ";
							$QryUpdateFleet .= "`fleet_amount` = `fleet_amount` - 1, ";
							$QryUpdateFleet .= "`fleet_resource_metal` = '0' , ";
							$QryUpdateFleet .= "`fleet_resource_crystal` = '0' , ";
							$QryUpdateFleet .= "`fleet_resource_deuterium` = '0' , ";
							$QryUpdateFleet .= "`fleet_mess` = '1' ";
							$QryUpdateFleet .= "WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';";
							doquery( $QryUpdateFleet, 'fleets');
						}
					}
					else
					{
						$TheMessage = $real_language_starter['sys_colo_arrival'] . $TargetAdress . $real_language_starter['sys_colo_badpos'];
						SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 0, $real_language_starter['sys_colo_mess_from'], $real_language_starter['sys_colo_mess_report'], $TheMessage);
						doquery("UPDATE {{table}} SET `fleet_mess` = '1' WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
					}
				}
			}
			else
			{
				$TheMessage = $real_language_starter['sys_colo_arrival'] . $TargetAdress . $real_language_starter['sys_colo_notfree'];
				SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_end_time'], 0, $real_language_starter['sys_colo_mess_from'], $real_language_starter['sys_colo_mess_report'], $TheMessage);
				doquery("UPDATE {{table}} SET `fleet_mess` = '1' WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
			}
		}
		elseif ($this->isReturnedToHome($FleetRow))
		{
			$this->RestoreFleetToPlanet ( $FleetRow, true );
			doquery("DELETE FROM {{table}} WHERE fleet_id=" . $FleetRow["fleet_id"], 'fleets');
		}
	}
}
?>
