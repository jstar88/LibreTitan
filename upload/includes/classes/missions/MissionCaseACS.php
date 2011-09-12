<?php
class MissionCaseACS extends FlyingFleetHandler
{ 
   public function MissionCaseACS($FleetRow)
	{

		if ($FleetRow['fleet_mess'] == 0 && $FleetRow['fleet_start_time'] > time())
		{
			$QryUpdateFleet  = "UPDATE {{table}} SET `fleet_mess` = '1' WHERE `fleet_id` = '". intval($FleetRow['fleet_id']) ."' LIMIT 1 ;";
			doquery( $QryUpdateFleet, 'fleets');
		}
		elseif ($this->isReturnedToHome($FleetRow))
		{
			$this->RestoreFleetToPlanet($FleetRow);
			doquery ('DELETE FROM {{table}} WHERE `fleet_id`='.intval($FleetRow['fleet_id']),'fleets');
		}
	}
}
?>
