<?php
//finish
class MissionCaseStay extends FlyingFleetHandler
{
	private function MissionCaseStay($FleetRow)
	{
		global $lang, $resource,$user;
		

		if (!$this->isMissionEnded($FleetRow))
		{
			if ($this->isArriveToDestination($FleetRow))
			{
			   $real_language_target=getRealLanguage($user['id'],$FleetRow['fleet_target_owner']);  
            if(empty($real_language_target))
		          $real_language_target=$lang;
		      
				$TargetAdress         = sprintf ($real_language_target['sys_adress_planet'], $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
				$TargetAddedGoods     = sprintf ($real_language_target['sys_stay_mess_goods'],
				$real_language_target['Metal'], pretty_number($FleetRow['fleet_resource_metal']),
				$real_language_target['Crystal'], pretty_number($FleetRow['fleet_resource_crystal']),
				$real_language_target['Deuterium'], pretty_number($FleetRow['fleet_resource_deuterium']));
        //start mod
				$TargetMessage        = $real_language_target['sys_stay_mess_start'] ."<a href=\"game.php?page=galaxy&mode=3&universe=".$FleetRow['fleet_end_universe'] ."&galaxy=". $FleetRow['fleet_end_galaxy'] ."&system=". $FleetRow['fleet_end_system'] ."\">";
				$TargetMessage       .= $TargetAdress. "</a>". $real_language_target['sys_stay_mess_end'] ."<br />". $TargetAddedGoods;
         //end mod
				SendSimpleMessage ( $FleetRow['fleet_target_owner'], '', $FleetRow['fleet_start_time'], 5, $real_language_target['sys_mess_qg'], $real_language_target['sys_stay_mess_stay'], $TargetMessage);
				$this->RestoreFleetToPlanet ( $FleetRow, false );
				doquery("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
			}
		}
		else
		{
			if ($this->isReturnedToHome($FleetRow))
			{ 
		      $real_language_starter =getRealLanguage($user['id'],$FleetRow['fleet_owner']);
		      if(empty($real_language_starter))
		          $real_language_starter=$lang;
		      
				$TargetAdress         = sprintf ($real_language_starter['sys_adress_planet'], $FleetRow['fleet_start_galaxy'], $FleetRow['fleet_start_system'], $FleetRow['fleet_start_planet']);
				$TargetAddedGoods     = sprintf ($real_language_starter['sys_stay_mess_goods'],
				$real_language_starter['Metal'], pretty_number($FleetRow['fleet_resource_metal']),
				$real_language_starter['Crystal'], pretty_number($FleetRow['fleet_resource_crystal']),
				$real_language_starter['Deuterium'], pretty_number($FleetRow['fleet_resource_deuterium']));
         //start mod
				$TargetMessage        = $real_language_starter['sys_stay_mess_back'] ."<a href=\"game.php?page=galaxy&mode=3&universe=".$FleetRow['fleet_start_universe'] ."&galaxy=". $FleetRow['fleet_start_galaxy'] ."&system=". $FleetRow['fleet_start_system'] ."\">";
				$TargetMessage       .= $TargetAdress. "</a>". $real_language_starter['sys_stay_mess_bend'] ."<br />". $TargetAddedGoods;
         //end mod
				SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_end_time'], 5, $real_language_starter['sys_mess_qg'], $real_language_starter['sys_mess_fleetback'], $TargetMessage);
				$this->RestoreFleetToPlanet ( $FleetRow, true );
				doquery("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
			}
		}
	}
}
?>
