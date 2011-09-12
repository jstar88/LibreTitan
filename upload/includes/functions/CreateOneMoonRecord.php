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

function CreateOneMoonRecord ( $Universe, $Galaxy, $System, $Planet, $Owner, $MoonID, $MoonName, $Chance )
{
	global $lang, $user;

	$PlanetName            = "";

	$QryGetMoonPlanetData  = "SELECT * FROM {{table}} ";
	$QryGetMoonPlanetData .= "WHERE ";
	//start mod
	$QryGetMoonPlanetData .= "`universe` = '". $Universe ."' AND ";
	//end mod
	$QryGetMoonPlanetData .= "`galaxy` = '". $Galaxy ."' AND ";
	$QryGetMoonPlanetData .= "`system` = '". $System ."' AND ";
	$QryGetMoonPlanetData .= "`planet` = '". $Planet ."';";
	$MoonPlanet = doquery ( $QryGetMoonPlanetData, 'planets', true);

	$QryGetMoonGalaxyData  = "SELECT * FROM {{table}} ";
	$QryGetMoonGalaxyData .= "WHERE ";
	//start mod
	$QryGetMoonPlanetData .= "`universe` = '". $Universe ."' AND ";
	//end mod
	$QryGetMoonGalaxyData .= "`galaxy` = '". $Galaxy ."' AND ";
	$QryGetMoonGalaxyData .= "`system` = '". $System ."' AND ";
	$QryGetMoonGalaxyData .= "`planet` = '". $Planet ."';";
	$MoonGalaxy = doquery ( $QryGetMoonGalaxyData, 'galaxy', true);

	if ($MoonGalaxy['id_luna'] == 0)
	{
		if ($MoonPlanet['id'] != 0)
		{
			$SizeMin                = 2000 + ( $Chance * 100 );
			$SizeMax                = 6000 + ( $Chance * 200 );

			$PlanetName             = $MoonPlanet['name'];

			$maxtemp                = $MoonPlanet['temp_max'] - rand(10, 45);
			$mintemp                = $MoonPlanet['temp_min'] - rand(10, 45);
			$size                   = rand ($SizeMin, $SizeMax);

			$QryInsertMoonInPlanet  = "INSERT INTO {{table}} SET ";
			$QryInsertMoonInPlanet .= "`name` = '". ( ($MoonName == '') ? $lang['fcm_moon'] : $MoonName ) ."', ";
			$QryInsertMoonInPlanet .= "`id_owner` = '". $Owner ."', ";
			//start mod
	    $QryInsertMoonInPlanet .= "`universe` = '". $Universe ."', ";
	    //end mod
			$QryInsertMoonInPlanet .= "`galaxy` = '". $Galaxy ."', ";
			$QryInsertMoonInPlanet .= "`system` = '". $System ."', ";
			$QryInsertMoonInPlanet .= "`planet` = '". $Planet ."', ";
			$QryInsertMoonInPlanet .= "`last_update` = '". time() ."', ";
			$QryInsertMoonInPlanet .= "`planet_type` = '3', ";
			$QryInsertMoonInPlanet .= "`image` = 'mond', ";
			$QryInsertMoonInPlanet .= "`diameter` = '". $size ."', ";
			$QryInsertMoonInPlanet .= "`field_max` = '1', ";
			$QryInsertMoonInPlanet .= "`temp_min` = '". $mintemp ."', ";
			$QryInsertMoonInPlanet .= "`temp_max` = '". $maxtemp ."', ";
			$QryInsertMoonInPlanet .= "`metal` = '0', ";
			$QryInsertMoonInPlanet .= "`metal_perhour` = '0', ";
			$QryInsertMoonInPlanet .= "`metal_max` = '".BASE_STORAGE_SIZE."', ";
			$QryInsertMoonInPlanet .= "`crystal` = '0', ";
			$QryInsertMoonInPlanet .= "`crystal_perhour` = '0', ";
			$QryInsertMoonInPlanet .= "`crystal_max` = '".BASE_STORAGE_SIZE."', ";
			$QryInsertMoonInPlanet .= "`deuterium` = '0', ";
			$QryInsertMoonInPlanet .= "`deuterium_perhour` = '0', ";
			$QryInsertMoonInPlanet .= "`deuterium_max` = '".BASE_STORAGE_SIZE."';";
			doquery( $QryInsertMoonInPlanet , 'planets');

			$QryGetMoonIdFromPlanet  = "SELECT * FROM {{table}} ";
			$QryGetMoonIdFromPlanet .= "WHERE ";
			//start mod
	    $QryGetMoonIdFromPlanet .= "`universe` = '". $Universe ."' AND ";
	    //end mod
			$QryGetMoonIdFromPlanet .= "`galaxy` = '".  $Galaxy ."' AND ";
			$QryGetMoonIdFromPlanet .= "`system` = '".  $System ."' AND ";
			$QryGetMoonIdFromPlanet .= "`planet` = '".  $Planet ."' AND ";
			$QryGetMoonIdFromPlanet .= "`planet_type` = '3';";
			$lunarow = doquery( $QryGetMoonIdFromPlanet , 'planets', true);

			$QryUpdateMoonInGalaxy  = "UPDATE {{table}} SET ";
			$QryUpdateMoonInGalaxy .= "`id_luna` = '". $lunarow['id'] ."', ";
			$QryUpdateMoonInGalaxy .= "`luna` = '0' ";
			$QryUpdateMoonInGalaxy .= "WHERE ";
			//start mod
	    $QryUpdateMoonInGalaxy .= "`universe` = '". $Universe ."' AND ";
	    //end mod
			$QryUpdateMoonInGalaxy .= "`galaxy` = '". $Galaxy ."' AND ";
			$QryUpdateMoonInGalaxy .= "`system` = '". $System ."' AND ";
			$QryUpdateMoonInGalaxy .= "`planet` = '". $Planet ."';";
			doquery( $QryUpdateMoonInGalaxy , 'galaxy');

		}
	}

	return $PlanetName;
}

?>