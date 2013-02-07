<?php
//finish
class MissionCaseTransport extends FlyingFleetHandler
{
   public function MissionCaseTransport ( $FleetRow )
	{
		global $lang,$user;


		if (!$this->isMissionEnded($FleetRow))
		{
			if ($this->isArriveToDestination($FleetRow))
			{
			   $real_language_target=getRealLanguage($user['id'],$FleetRow['fleet_target_owner']);  
		      $real_language_starter =getRealLanguage($user['id'],$FleetRow['fleet_owner']);
            if(empty($real_language_target))
		          $real_language_target=$lang;
		      if(empty($real_language_starter))
		          $real_language_starter=$lang;
		      
            $StartPlanet      = $this->getStartPlanetInfoInCache($FleetRow);
		      $StartName        = $StartPlanet['name'];
		      $StartOwner       = $StartPlanet['id_owner'];

		      $TargetPlanet     = $this->getTargetPlanetInfoInCache($FleetRow);
		      $TargetName       = $TargetPlanet['name'];
		      $TargetOwner      = $TargetPlanet['id_owner'];
		          
				$this->StoreGoodsToPlanet ($FleetRow, false);
				$Message         = sprintf( $real_language_starter['sys_tran_mess_owner'],
							$TargetName, GetTargetAdressLink($FleetRow, ''),
							$FleetRow['fleet_resource_metal'], $real_language_starter['Metal'],
							$FleetRow['fleet_resource_crystal'], $real_language_starter['Crystal'],
							$FleetRow['fleet_resource_deuterium'], $real_language_starter['Deuterium'] );

				SendSimpleMessage ( $StartOwner, '', $FleetRow['fleet_start_time'], 5, $real_language_starter['sys_mess_tower'], $real_language_starter['sys_mess_transport'], $Message);
				if ($TargetOwner != $StartOwner)
				{
					$Message         = sprintf( $real_language_target['sys_tran_mess_user'],
									$StartName, GetStartAdressLink($FleetRow, ''),
									$TargetName, GetTargetAdressLink($FleetRow, ''),
									$FleetRow['fleet_resource_metal'], $real_language_target['Metal'],
									$FleetRow['fleet_resource_crystal'], $real_language_target['Crystal'],
									$FleetRow['fleet_resource_deuterium'], $real_language_target['Deuterium'] );
					SendSimpleMessage ( $TargetOwner, '', $FleetRow['fleet_start_time'], 5, $real_language_target['sys_mess_tower'], $real_language_target['sys_mess_transport'], $Message);
				}

				$QryUpdateFleet  = "UPDATE {{table}} SET ";
				$QryUpdateFleet .= "`fleet_resource_metal` = '0' , ";
				$QryUpdateFleet .= "`fleet_resource_crystal` = '0' , ";
				$QryUpdateFleet .= "`fleet_resource_deuterium` = '0' , ";
				$QryUpdateFleet .= "`fleet_mess` = '1' ";
				$QryUpdateFleet .= "WHERE `fleet_id` = '". intval($FleetRow['fleet_id']) ."' ";
				$QryUpdateFleet .= "LIMIT 1 ;";
				doquery( $QryUpdateFleet, 'fleets');
			}
		}
		elseif ($this->isReturnedToHome($FleetRow))
			{  
			   $StartPlanet      = $this->getStartPlanetInfoInCache($FleetRow);
		      $StartName        = $StartPlanet['name'];
		      $StartOwner       = $StartPlanet['id_owner'];   
			         
		      $real_language_starter =getRealLanguage($user['id'],$FleetRow['fleet_owner']);
		      if(empty($real_language_starter))
		          $real_language_starter=$lang;
				$Message             = sprintf ($real_language_starter['sys_tran_mess_back'], $StartName, GetStartAdressLink($FleetRow, ''));
				SendSimpleMessage ( $StartOwner, '', $FleetRow['fleet_end_time'], 5, $real_language_starter['sys_mess_tower'], $real_language_starter['sys_mess_fleetback'], $Message);
				$this->RestoreFleetToPlanet ( $FleetRow, true );
				doquery("DELETE FROM {{table}} WHERE fleet_id=" . $FleetRow["fleet_id"], 'fleets');
			}
		
	}   
}
?>
