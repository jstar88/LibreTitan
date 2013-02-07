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
define('IN_ADMIN', true);

$xgp_root = './../';
include($xgp_root . 'extension.inc.php');
include($xgp_root . 'common.' . $phpEx);
include('AdminFunctions/Autorization.' . $phpEx);

if ($EditUsers != 1) die();

$parse	= $lang;


if ($_POST && $_POST['add_moon'])
{
	$PlanetID  	= $_POST['add_moon'];
	$MoonName  	= $_POST['name'];
	$Diameter	= $_POST['diameter'];
	$TempMin	= $_POST['temp_min'];
	$TempMax	= $_POST['temp_max'];
	$FieldMax	= $_POST['field_max'];
	
	$search			=	doquery("SELECT * FROM {{table}} WHERE `id` LIKE '%{$PlanetID}%'", "planets");
	$MoonPlanet		= 	doquery("SELECT * FROM {{table}} WHERE `id` = '".$PlanetID."'", 'planets', true);
	$MoonGalaxy		= 	doquery("SELECT * FROM {{table}} WHERE `id_planet` = '".$PlanetID."'", 'galaxy', true);


if (mysql_num_rows($search) != 0)
{
	if ($MoonGalaxy['id_luna'] == 0 && $MoonPlanet['planet_type'] == 1 && $MoonPlanet['destruyed'] == 0)
	{
		$Universe    = $MoonPlanet['universe'];
    $Galaxy    = $MoonPlanet['galaxy'];
		$System    = $MoonPlanet['system'];
		$Planet    = $MoonPlanet['planet'];
		$Owner     = $MoonPlanet['id_owner'];
		$MoonID    = time();


		if ($_POST['diameter_check'] == 'on')
		{
			$SizeMin	= 4500;
			$SizeMax    = 9999;
			$size       = rand ($SizeMin, $SizeMax);
		}
		elseif ($_POST['diameter_check'] != 'on' && is_numeric($Diameter))
		{
			$size	=	$Diameter;
		}
		else
		{
			message ($lang['mo_only_numbers'], "MoonOptionsPage.php", 2);
		}
				
				
		if ($_POST['temp_check']	==	'on')
		{
			$maxtemp	= $MoonPlanet['temp_max'] - rand(10, 45);
			$mintemp	= $MoonPlanet['temp_min'] - rand(10, 45);
		}
		elseif ($_POST['temp_check']	!=	'on' && is_numeric($TempMax) && is_numeric($TempMin) )
		{
			$maxtemp	=	$TempMax;
			$mintemp	=	$TempMin;
		}
		else
		{
			message ($lang['mo_only_numbers'], "MoonOptionsPage.php", 2);
		}
			$QueryFind	=	doquery("SELECT `id_level` FROM {{table}} WHERE `id` = '".$PlanetID."'", "planets", true);
			
			$QryInsertMoonInPlanet  = "INSERT INTO {{table}} SET ";
			$QryInsertMoonInPlanet .= "`name` = '".$MoonName."', ";
			$QryInsertMoonInPlanet .= "`id_owner` = '". $Owner ."', ";
			$QryInsertMoonInPlanet .= "`id_level` = '". $QueryFind['id_level'] ."', ";
			$QryInsertMoonInPlanet .= "`universe` = '". $Universe ."', ";
			$QryInsertMoonInPlanet .= "`galaxy` = '". $Galaxy ."', ";
			$QryInsertMoonInPlanet .= "`system` = '". $System ."', ";
			$QryInsertMoonInPlanet .= "`planet` = '". $Planet ."', ";
			$QryInsertMoonInPlanet .= "`last_update` = '". time() ."', ";
			$QryInsertMoonInPlanet .= "`planet_type` = '3', ";
			$QryInsertMoonInPlanet .= "`image` = 'mond', ";
			$QryInsertMoonInPlanet .= "`diameter` = '". $size ."', ";
			$QryInsertMoonInPlanet .= "`field_max` = '".$FieldMax."', ";
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
			
			$QryGetMoonIdFromLunas  = "SELECT * FROM {{table}} WHERE ";
			$QryGetMoonIdFromLunas .= "`universe` = '".  $Universe ."' AND ";
			$QryGetMoonIdFromLunas .= "`galaxy` = '".  $Galaxy ."' AND ";
			$QryGetMoonIdFromLunas .= "`system` = '".  $System ."' AND ";
			$QryGetMoonIdFromLunas .= "`planet` = '". $Planet ."' AND ";
			$QryGetMoonIdFromLunas .= "`planet_type` = '3';";
			$PlanetRow = doquery( $QryGetMoonIdFromLunas , 'planets', true);

			$QryUpdateMoonInGalaxy  = "UPDATE {{table}} SET ";
			$QryUpdateMoonInGalaxy .= "`id_luna` = '". $PlanetRow['id'] ."', ";
			$QryUpdateMoonInGalaxy .= "`luna` = '0' ";
			$QryUpdateMoonInGalaxy .= "WHERE ";
			$QryUpdateMoonInGalaxy .= "`universe` = '". $Universe ."' AND ";
			$QryUpdateMoonInGalaxy .= "`galaxy` = '". $Galaxy ."' AND ";
			$QryUpdateMoonInGalaxy .= "`system` = '". $System ."' AND ";
			$QryUpdateMoonInGalaxy .= "`planet` = '". $Planet ."';";
			doquery( $QryUpdateMoonInGalaxy , 'galaxy');
			
			message ($lang['mo_moon_added'],"MoonOptionsPage.php",2);
		}
		else
		{
			message ($lang['mo_moon_unavaible'],"MoonOptionsPage.php",2);
		}
	}
	else
	{
		message ($lang['mo_planet_doesnt_exist'],"MoonOptionsPage.php",2);
	}
}
elseif($_POST && $_POST['del_moon'])
{
	$MoonID	= $_POST['del_moon'];

	$search	=	doquery("SELECT * FROM {{table}} WHERE `id` LIKE '%{$MoonID}%'", "planets");
	if (mysql_num_rows($search) != 0)
	{
		$MoonSelected  			= doquery("SELECT * FROM {{table}} WHERE `id` = '". $MoonID ."'", 'planets', true);
		
		if ($MoonSelected['planet_type'] == 3)
		{
		  $Universe    = $MoonSelected['universe'];
			$Galaxy    = $MoonSelected['galaxy'];
			$System    = $MoonSelected['system'];
			$Planet    = $MoonSelected['planet'];
		
			doquery("DELETE FROM {{table}} WHERE `universe` ='".$Universe."' AND `galaxy` ='".$Galaxy."' AND `system` ='".$System."' AND `planet` ='".$Planet."' AND `planet_type` = '3'",'planets');

			$QryUpdateGalaxy  = "UPDATE {{table}} SET ";
			$QryUpdateGalaxy .= "`id_luna` = '0' ";
			$QryUpdateGalaxy .= "WHERE ";
			$QryUpdateGalaxy .= "`universe` = '". $Universe ."' AND ";
			$QryUpdateGalaxy .= "`galaxy` = '". $Galaxy ."' AND ";
			$QryUpdateGalaxy .= "`system` = '". $System ."' AND ";
			$QryUpdateGalaxy .= "`planet` = '". $Planet ."' ";
			$QryUpdateGalaxy .= "LIMIT 1;";
			doquery( $QryUpdateGalaxy , 'galaxy');

			message ($lang['mo_moon_deleted'], "MoonOptionsPage.php", 2);
		}
		else
		{
			message ($lang['mo_moon_only'], "MoonOptionsPage.php", 2);
		}
	}
	else
	{
		message ($lang['mo_moon_doesnt_exist'], "MoonOptionsPage.php", 2);
	}
}
elseif($_POST && $_POST['search_moon'])
{
	$UserID		=	$_POST['search_moon'];
	$search_m	=	doquery("SELECT * FROM {{table}} WHERE `id_owner` LIKE '%{$UserID}%' AND `planet_type` = '3'", "planets");

	while ($c = mysql_fetch_array($search_m))
	{
		$parse['moonlist']	.=	"<tr><td colspan=\"2\" class=\"big\">".$c['name']." [".$c['universe'].":".$c['galaxy'].":".$c['system'].":".$c['planet']."] ID: ".$c['id']."</td></tr>";
	}
}

$search_u	=	doquery("SELECT * FROM {{table}}", "users");
while ($b = mysql_fetch_array($search_u))
{
	$parse['list']	.=	"<option value=\"".$b['id']."\">".$b['username']."</option>";
}


display (parsetemplate(gettemplate("adm/MoonOptionsBody"), $parse), false, '', true, false);

?>