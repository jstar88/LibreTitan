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

	function SetSelectedPlanet ( &$CurrentUser )
	{

		$SelectPlanet  = intval($_GET['cp']);
		$RestorePlanet = intval($_GET['re']);

		// ADDED && $SelectPlanet != 0 THIS PREVENTS RUN A QUERY WHEN IT'S NOT NEEDED.
      if (isset($SelectPlanet) && is_numeric($SelectPlanet) && isset($RestorePlanet) && $RestorePlanet == 0 && $SelectPlanet != 0 )
		{
			$IsPlanetMine   = doquery("SELECT `id` FROM {{table}} WHERE `id` = '". $SelectPlanet ."' AND `id_owner` = '". intval($CurrentUser['id']) ."';", 'planets', true);

			if ($IsPlanetMine)
			{
				$CurrentUser['current_planet'] = $SelectPlanet;
				doquery("UPDATE {{table}} SET `current_planet` = '". $SelectPlanet ."' WHERE `id` = '".intval($CurrentUser['id'])."';", 'users');
			}
		}
	}

?>