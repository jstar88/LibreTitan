<?php

##############################################################################
# *																			 #
# * XG PROYECT																 #
# *  																		 #
# * @copyright Copyright (C) 2008 - 2010 By lucky from xgproyect.net      	 #
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

if(!defined('INSIDE')){ die(header("location:../../"));}

	function DeleteSelectedUser($UserID)
	{
		$TheUser = doquery ( "SELECT * FROM {{table}} WHERE `id` = '" . $UserID . "';", 'users', true );

		if ( $TheUser['ally_id'] != 0 )
		{
			$TheAlly = doquery ( "SELECT * FROM {{table}} WHERE `id` = '" . $TheUser['ally_id'] . "';", 'alliance', true );
			$TheAlly['ally_members'] -= 1;

			if ($TheAlly['ally_members'] > 0)
			{
				doquery ( "UPDATE {{table}} SET `ally_members` = '" . $TheAlly['ally_members'] . "' WHERE `id` = '" . $TheAlly['id'] . "';", 'alliance' );
			}
			else
			{
				doquery ( "DELETE FROM {{table}} WHERE `id` = '" . $TheAlly['id'] . "';", 'alliance' );
				doquery ( "DELETE FROM {{table}} WHERE `stat_type` = '2' AND `id_owner` = '" . $TheAlly['id'] . "';", 'statpoints' );
			}
		}

		doquery ( "DELETE FROM {{table}} WHERE `stat_type` = '1' AND `id_owner` = '" . $UserID . "';", 'statpoints' );

		$ThePlanets = doquery ( "SELECT * FROM {{table}} WHERE `id_owner` = '" . $UserID . "';", 'planets' );

		while ( $OnePlanet = mysql_fetch_assoc ( $ThePlanets ) )
		{  //start mod
			if ( $OnePlanet['planet_type'] == 1 )
				doquery ( "DELETE FROM {{table}} WHERE 
				`universe` = '" . $OnePlanet['universe'] . "' AND
        `galaxy` = '" . $OnePlanet['galaxy'] . "' AND 
        `system` = '" . $OnePlanet['system'] . "' AND 
        `planet` = '" . $OnePlanet['planet'] . "';", 'galaxy' );
        //end mod
			elseif ( $OnePlanet['planet_type'] == 3 )
			doquery ( "DELETE FROM {{table}} WHERE `id` = '" . $OnePlanet['id'] . "';", 'planets' );
		}

		doquery ( "DELETE FROM {{table}} WHERE `id_owner` = '" . $UserID . "';", 'planets' );
		doquery ( "DELETE FROM {{table}} WHERE `message_sender` = '" . $UserID . "';", 'messages' );
		doquery ( "DELETE FROM {{table}} WHERE `message_owner` = '" . $UserID . "';", 'messages' );
		doquery ( "DELETE FROM {{table}} WHERE `owner` = '" . $UserID . "';", 'notes' );
		doquery ( "DELETE FROM {{table}} WHERE `fleet_owner` = '" . $UserID . "';", 'fleets' );
		doquery ( "DELETE FROM {{table}} WHERE `sender` = '" . $UserID . "';", 'buddy' );
		doquery ( "DELETE FROM {{table}} WHERE `owner` = '" . $UserID . "';", 'buddy' );
		doquery ( "DELETE FROM {{table}} WHERE `id` = '" . $UserID . "';", 'users' );
		doquery ( "UPDATE `{{table}}` SET `config_value` = `config_value` - '1' WHERE `config_name` = 'users_amount' LIMIT 1 ;", "config" );
	}

	function DeleteSelectedPlanet ($ID)
	{
		global $lang;
    //start mod
		$QueryPlanet = doquery ("SELECT universe,galaxy,planet,system,planet_type FROM {{table}} WHERE id = '".$ID."'", 'planets', true );
    //end mod
		if ($QueryPlanet['planet_type'] == '3')
		{
			doquery("DELETE FROM {{table}} WHERE id = '".$ID."'", "planets");
			doquery("UPDATE {{table}} SET id_luna = 0 WHERE id_luna = '".$ID."'", "galaxy");
		}
		else
		{   //start mod
			doquery("DELETE FROM {{table}} WHERE 
			universe = '".$QueryPlanet['universe']."' AND
      galaxy = '".$QueryPlanet['galaxy']."' AND 
      system = '".$QueryPlanet['system']."' AND 
      planet = '".$QueryPlanet['planet']."'", "planets");
      //end mod
			doquery("DELETE FROM {{table}} WHERE id_planet = '".$ID."'", "galaxy");
		}
	}
?>