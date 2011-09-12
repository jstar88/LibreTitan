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

if(!defined('INSIDE')){ die(header("location:../../"));}

	function HandleTechnologieBuild ( &$CurrentPlanet, &$CurrentUser )
	{
		global $resource;

		if ($CurrentUser['b_tech_planet'] != 0)
		{
			if ($CurrentUser['b_tech_planet'] != $CurrentPlanet['id'])
				$WorkingPlanet = doquery("SELECT * FROM {{table}} WHERE `id` = '". intval($CurrentUser['b_tech_planet']) ."';", 'planets', true);

			if ($WorkingPlanet)
				$ThePlanet = $WorkingPlanet;
			else
				$ThePlanet = $CurrentPlanet;

			if ($ThePlanet['b_tech']    <= time() && $ThePlanet['b_tech_id'] != 0)
			{
				$CurrentUser[$resource[$ThePlanet['b_tech_id']]]++;

				$QryUpdatePlanet  = "UPDATE {{table}} SET ";
				$QryUpdatePlanet .= "`b_tech` = '0', ";
				$QryUpdatePlanet .= "`b_tech_id` = '0' ";
				$QryUpdatePlanet .= "WHERE ";
				$QryUpdatePlanet .= "`id` = '". intval($ThePlanet['id']) ."';";
				doquery( $QryUpdatePlanet, 'planets');

				$QryUpdateUser    = "UPDATE {{table}} SET ";
				$QryUpdateUser   .= "`".$resource[$ThePlanet['b_tech_id']]."` = '". $CurrentUser[$resource[$ThePlanet['b_tech_id']]] ."', ";
				$QryUpdateUser   .= "`b_tech_planet` = '0' ";
				$QryUpdateUser   .= "WHERE ";
				$QryUpdateUser   .= "`id` = '". intval($CurrentUser['id']) ."';";
				doquery( $QryUpdateUser, 'users');

				$ThePlanet["b_tech_id"] = 0;

				if (isset($WorkingPlanet))
					$WorkingPlanet = $ThePlanet;
				else
					$CurrentPlanet = $ThePlanet;

				$Result['WorkOn'] = "";
				$Result['OnWork'] = false;
			}
			elseif ($ThePlanet["b_tech_id"] == 0)
			{
				doquery("UPDATE {{table}} SET `b_tech_planet` = '0'  WHERE `id` = '". intval($CurrentUser['id']) ."';", 'users');
				$Result['WorkOn'] = "";
				$Result['OnWork'] = false;
			}
			else
			{
				$Result['WorkOn'] = $ThePlanet;
				$Result['OnWork'] = true;
			}
		}
		else
		{
			$Result['WorkOn'] = "";
			$Result['OnWork'] = false;
		}

		return $Result;
	}
?>