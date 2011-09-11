<?php
class MissionCaseRecycling extends FlyingFleetHandler
{
   public function MissionCaseRecycling ($FleetRow)
	{
		global $pricelist, $lang,$user;

		if (!$this->isMissionEnded($FleetRow))
		{
			if ($this->isArriveToDestination($FleetRow))
			{
				$QrySelectGalaxy  = "SELECT metal, crystal  FROM {{table}} ";
				$QrySelectGalaxy .= "WHERE ";
				//start mod
				$QrySelectGalaxy .= "`universe` = '".$FleetRow['fleet_end_universe']."' AND ";
				//end mod
				$QrySelectGalaxy .= "`galaxy` = '".$FleetRow['fleet_end_galaxy']."' AND ";
				$QrySelectGalaxy .= "`system` = '".$FleetRow['fleet_end_system']."' AND ";
				$QrySelectGalaxy .= "`planet` = '".$FleetRow['fleet_end_planet']."' ";
				$QrySelectGalaxy .= "LIMIT 1;";
				$TargetGalaxy     = doquery( $QrySelectGalaxy, 'galaxy', true);

				$FleetRecord         = explode(";", $FleetRow['fleet_array']);
				$RecyclerCapacity    = 0;
				$OtherFleetCapacity  = 0;
				foreach ($FleetRecord as $Item => $Group)
				{
					if ($Group != '')
					{
						$Class        = explode (",", $Group);
						if ($Class[0] == 209)
							$RecyclerCapacity   += $pricelist[$Class[0]]["capacity"] * $Class[1];
						else
							$OtherFleetCapacity += $pricelist[$Class[0]]["capacity"] * $Class[1];
					}
				}

				$IncomingFleetGoods = $FleetRow["fleet_resource_metal"] + $FleetRow["fleet_resource_crystal"] + $FleetRow["fleet_resource_deuterium"];
				if ($IncomingFleetGoods > $OtherFleetCapacity)
					$RecyclerCapacity -= ($IncomingFleetGoods - $OtherFleetCapacity);

				if (($TargetGalaxy["metal"] + $TargetGalaxy["crystal"]) <= $RecyclerCapacity)
				{
					$RecycledGoods["metal"]   = $TargetGalaxy["metal"];
					$RecycledGoods["crystal"] = $TargetGalaxy["crystal"];
				}
				else
				{
					if (($TargetGalaxy["metal"]   > $RecyclerCapacity / 2) && ($TargetGalaxy["crystal"] > $RecyclerCapacity / 2))
					{
						$RecycledGoods["metal"]   = $RecyclerCapacity / 2;
						$RecycledGoods["crystal"] = $RecyclerCapacity / 2;
					}
					else
					{
						if ($TargetGalaxy["metal"] > $TargetGalaxy["crystal"])
						{
							$RecycledGoods["crystal"] = $TargetGalaxy["crystal"];
							if ($TargetGalaxy["metal"] > ($RecyclerCapacity - $RecycledGoods["crystal"]))
								$RecycledGoods["metal"] = $RecyclerCapacity - $RecycledGoods["crystal"];
							else
								$RecycledGoods["metal"] = $TargetGalaxy["metal"];
						}
						else
						{
							$RecycledGoods["metal"] = $TargetGalaxy["metal"];
							if ($TargetGalaxy["crystal"] > ($RecyclerCapacity - $RecycledGoods["metal"]))
								$RecycledGoods["crystal"] = $RecyclerCapacity - $RecycledGoods["metal"];
							else
								$RecycledGoods["crystal"] = $TargetGalaxy["crystal"];
						}
					}
				}

				$QryUpdateGalaxy  = "UPDATE {{table}} SET ";
				$QryUpdateGalaxy .= "`metal` = `metal` - '".$RecycledGoods["metal"]."', ";
				$QryUpdateGalaxy .= "`crystal` = `crystal` - '".$RecycledGoods["crystal"]."' ";
				$QryUpdateGalaxy .= "WHERE ";
				//start mod
				$QryUpdateGalaxy .= "`universe` = '".$FleetRow['fleet_end_universe']."' AND ";
				//end mod
				$QryUpdateGalaxy .= "`galaxy` = '".$FleetRow['fleet_end_galaxy']."' AND ";
				$QryUpdateGalaxy .= "`system` = '".$FleetRow['fleet_end_system']."' AND ";
				$QryUpdateGalaxy .= "`planet` = '".$FleetRow['fleet_end_planet']."' ";
				$QryUpdateGalaxy .= "LIMIT 1;";
				doquery( $QryUpdateGalaxy, 'galaxy');
            //here we get the real language
            $real_language_starter=getRealLanguage($user['id'],$FleetRow['fleet_owner']);  
		      if(empty($real_language_starter))
		          $real_language_starter=$lang;
				//end
            $Message = sprintf($real_language_starter['sys_recy_gotten'], pretty_number($RecycledGoods["metal"]), $real_language_starter['Metal'], pretty_number($RecycledGoods["crystal"]), $real_language_starter['Crystal']);
				SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 4, $real_language_starter['sys_mess_spy_control'], $real_language_starter['sys_recy_report'], $Message);

				$QryUpdateFleet  = "UPDATE {{table}} SET ";
				$QryUpdateFleet .= "`fleet_resource_metal` = `fleet_resource_metal` + '".$RecycledGoods["metal"]."', ";
				$QryUpdateFleet .= "`fleet_resource_crystal` = `fleet_resource_crystal` + '".$RecycledGoods["crystal"]."', ";
				$QryUpdateFleet .= "`fleet_mess` = '1' ";
				$QryUpdateFleet .= "WHERE ";
				$QryUpdateFleet .= "`fleet_id` = '".intval($FleetRow['fleet_id'])."' ";
				$QryUpdateFleet .= "LIMIT 1;";
				doquery( $QryUpdateFleet, 'fleets');
			}
		}
		
			elseif ($this->isReturnedToHome($FleetRow))
			{
			   //here we get the real language
            $real_language_starter=getRealLanguage($user['id'],$FleetRow['fleet_owner']);  
		      if(empty($real_language_starter))
		          $real_language_starter=$lang;
				//end
				$Message         = sprintf( $real_language_starter['sys_tran_mess_owner'],
				$TargetName, GetTargetAdressLink($FleetRow, ''),
				pretty_number($FleetRow['fleet_resource_metal']), $real_language_starter['Metal'],
				pretty_number($FleetRow['fleet_resource_crystal']), $real_language_starter['Crystal'],
				pretty_number($FleetRow['fleet_resource_deuterium']), $real_language_starter['Deuterium'] );
				SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_end_time'], 4, $real_language_starter['sys_mess_spy_control'], $real_language_starter['sys_mess_fleetback'], $Message);
				$this->RestoreFleetToPlanet ( $FleetRow, true );
				doquery("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
			}
		
	}
}
?>
