<?php

##############################################################################
# *																			 #
# * XG PROYECT																 #
# *  																		 #
# * @copyright Copyright (C) 2008 - 2009 By lucky from xgproyect.net      	 #
# *																			 #
# *																			 #
# *  This program is free software: you can redistribute it and/or modify    #
# *  it under the terms of the GNU General Public License as published by    #
# *  the Free Software Foundation, either version 3 of the License, or       #
# *  (at your option) any later version.									 #
# *																			 #
# *  This program is distributed in the hope that it will be useful,		 #
# *  but WITHOUT ANY WARRANTY; without even the implied warranty of			 #
# *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the			 #
# *  GNU General Public License for more details.							 #
# *																			 #
##############################################################################

define('INSIDE'  , true);
define('INSTALL' , false);

$xgp_root = './';
include($xgp_root . 'extension.inc.php');
include($xgp_root . 'common.' . $phpEx);

if ( is_numeric($_POST['fleetid']) )
{
	$fleetid  = intval($_POST['fleetid']);
	$FleetRow = doquery("SELECT * FROM {{table}} WHERE `fleet_id` = '". $fleetid ."';", 'fleets', true);
	$i = 0;

	if ($FleetRow['fleet_owner'] == $user['id'])
	{
		if ($FleetRow['fleet_mess'] == 0 || $FleetRow['fleet_mess'] == 2)  
		{
			if ($FleetRow['fleet_group'] > 0)
			{
				$Aks = doquery("SELECT teilnehmer FROM {{table}} WHERE id = '". $FleetRow['fleet_group'] ."';", 'aks', true);
				if ($Aks['teilnehmer'] == $FleetRow['fleet_owner'] AND $FleetRow['fleet_mission'] == 1)
				{
					doquery ("DELETE FROM {{table}} WHERE id ='". $FleetRow['fleet_group'] ."';", 'aks');
					doquery ("UPDATE {{table}} SET `fleet_group` = '0' WHERE `fleet_group` = '". $FleetRow['fleet_group'] ."';", 'fleets');
				}
				if ($FleetRow['fleet_mission'] == 2)
				{
					doquery ("UPDATE {{table}} SET `fleet_group` = '0' WHERE `fleet_id` = '".  $fleetid ."';", 'fleets');
				}
			}

			$CurrentFlyingTime = time() - $FleetRow['start_time'];
			/*
			if ($FleetRow['fleet_end_stay'] != 0)
			{
				if ($FleetRow['fleet_start_time'] < time())
				{
					$CurrentFlyingTime = time() - $FleetRow['start_time'];
				}
				else
				{
					$CurrentFlyingTime = time() - $FleetRow['start_time'];
				}

			}
			else
			{
				$CurrentFlyingTime = time() - $FleetRow['start_time']; 	// LO QUE ESTA COMO COMENTARIO ES EL BETA DEL FIX PARA
			}															// LOS TIEMPOS DE REGRESO DE LAS FLOTAS
																		// FUNCIONABA BIEN PERO FALLABA PARA LA MISION MATENER POSICION
			*/
			//$ReturnFlyingTime  = $CurrentFlyingTime + time();
			
         /*** start fix by jstar ***/
			//the fleet time duration between 2 planet, it is equal for go and return when maintaining time=0
			$fleetLeght	=	$FleetRow['fleet_start_time'] - $FleetRow['start_time'];
			//the return time when you press "call back ships"
			$ReturnFlyingTime  =
			//if the ships mission is maintaining position and they are already in target pianet
			( $FleetRow['fleet_end_stay'] != 0 && $CurrentFlyingTime > $fleetLeght )
			//then the return time is the $fleetLeght + the current time in maintaining position
			  ? $fleetLeght + time()
			// else normal mission
			  : $CurrentFlyingTime + time();
			/***end fix by jstar***/  

			$QryUpdateFleet  = "UPDATE {{table}} SET ";
			$QryUpdateFleet .= "`fleet_start_time` = '". (time() - 1) ."', ";
			$QryUpdateFleet .= "`fleet_end_stay` = '0', ";
			$QryUpdateFleet .= "`fleet_end_time` = '". ($ReturnFlyingTime + 1) ."', ";
			$QryUpdateFleet .= "`fleet_target_owner` = '". $user['id'] ."', ";
			$QryUpdateFleet .= "`fleet_mess` = '1' ";
			$QryUpdateFleet .= "WHERE ";
			$QryUpdateFleet .= "`fleet_id` = '" . $fleetid . "';";
			doquery( $QryUpdateFleet, 'fleets');
		}
	}
}
header("location:game.php?page=fleet");
?>