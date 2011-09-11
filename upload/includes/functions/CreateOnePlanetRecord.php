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

	function PlanetSizeRandomiser ($Position, $HomeWorld = false)
	{
		global $game_config, $user;

		if (!$HomeWorld)
		{
			$ClassicBase      = 163;
			$SettingSize      = $game_config['initial_fields'];
			$PlanetRatio      = floor ( ($ClassicBase / $SettingSize) * 10000 ) / 100;
			$RandomMin        = array (  140,  150,  155, 200, 195, 180, 215, 220, 225, 175, 180, 185, 160, 140, 150);
			$RandomMax        = array (  290,  295,  295, 440, 440, 430, 380, 380, 390, 325, 320, 330, 360, 500, 350);
			$CalculMin        = floor ( $RandomMin[$Position - 1] + ( $RandomMin[$Position - 1] * $PlanetRatio ) / 100 );
			$CalculMax        = floor ( $RandomMax[$Position - 1] + ( $RandomMax[$Position - 1] * $PlanetRatio ) / 100 );
			$RandomSize       = mt_rand($CalculMin, $CalculMax);
			$MaxAddon         = mt_rand(0, 110);
			$MinAddon         = mt_rand(0, 100);
			$Addon            = ($MaxAddon - $MinAddon);
			$PlanetFields     = ($RandomSize + $Addon);
		}
		else
		{
			$PlanetFields     = $game_config['initial_fields'];
		}
		$PlanetSize           = ($PlanetFields ^ (14 / 1.5)) * 75;

		$return['diameter']   = $PlanetSize;
		$return['field_max']  = $PlanetFields;
		return $return;
	}

	function CreateOnePlanetRecord($Universe, $Galaxy, $System, $Position, $PlanetOwnerID, $PlanetName = '', $HomeWorld = false)
	{
		global $lang;

		$QrySelectPlanet  = "SELECT	`id` ";
		$QrySelectPlanet .= "FROM {{table}} ";
		$QrySelectPlanet .= "WHERE ";
		//start mod
		$QrySelectPlanet .= "`universe` = '". $Universe ."' AND ";
		//end mod
		$QrySelectPlanet .= "`galaxy` = '". $Galaxy ."' AND ";
		$QrySelectPlanet .= "`system` = '". $System ."' AND ";
		$QrySelectPlanet .= "`planet` = '". $Position ."';";
		$PlanetExist = doquery( $QrySelectPlanet, 'planets', true);

		if (!$PlanetExist)
		{
			$planet                      = PlanetSizeRandomiser ($Position, $HomeWorld);
			$planet['diameter']          = ($planet['field_max'] ^ (14 / 1.5)) * 75 ;
			$planet['metal']             = BUILD_METAL;
			$planet['crystal']           = BUILD_CRISTAL;
			$planet['deuterium']         = BUILD_DEUTERIUM;
			$planet['metal_perhour']     = $game_config['metal_basic_income'];
			$planet['crystal_perhour']   = $game_config['crystal_basic_income'];
			$planet['deuterium_perhour'] = $game_config['deuterium_basic_income'];
			$planet['metal_max']         = BASE_STORAGE_SIZE;
			$planet['crystal_max']       = BASE_STORAGE_SIZE;
			$planet['deuterium_max']     = BASE_STORAGE_SIZE;

			// Posistion  1 -  3: 80% entre  40 et  70 Cases (  55+ / -15 )
			// Posistion  4 -  6: 80% entre 120 et 310 Cases ( 215+ / -95 )
			// Posistion  7 -  9: 80% entre 105 et 195 Cases ( 150+ / -45 )
			// Posistion 10 - 12: 80% entre  75 et 125 Cases ( 100+ / -25 )
			// Posistion 13 - 15: 80% entre  60 et 190 Cases ( 125+ / -65 )
      //start mod
      $planet['universe'] = $Universe;
      //end mod
			$planet['galaxy'] = $Galaxy;
			$planet['system'] = $System;
			$planet['planet'] = $Position;

			if ($Position == 1 || $Position == 2 || $Position == 3) {
				$PlanetType         = array('trocken');
				$PlanetClass        = array('planet');
				$PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10');
				$planet['temp_min'] = rand(0, 100);
				$planet['temp_max'] = $planet['temp_min'] + 40;
			} elseif ($Position == 4 || $Position == 5 || $Position == 6) {
				$PlanetType         = array('dschjungel');
				$PlanetClass        = array('planet');
				$PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10');
				$planet['temp_min'] = rand(-25, 75);
				$planet['temp_max'] = $planet['temp_min'] + 40;
			} elseif ($Position == 7 || $Position == 8 || $Position == 9) {
				$PlanetType         = array('normaltemp');
				$PlanetClass        = array('planet');
				$PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07');
				$planet['temp_min'] = rand(-50, 50);
				$planet['temp_max'] = $planet['temp_min'] + 40;
			} elseif ($Position == 10 || $Position == 11 || $Position == 12) {
				$PlanetType         = array('wasser');
				$PlanetClass        = array('planet');
				$PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09');
				$planet['temp_min'] = rand(-75, 25);
				$planet['temp_max'] = $planet['temp_min'] + 40;
			} elseif ($Position == 13 || $Position == 14 || $Position == 15) {
				$PlanetType         = array('eis');
				$PlanetClass        = array('planet');
				$PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10');
				$planet['temp_min'] = rand(-100, 10);
				$planet['temp_max'] = $planet['temp_min'] + 40;
			} else {
				$PlanetType         = array('dschjungel', 'gas', 'normaltemp', 'trocken', 'wasser', 'wuesten', 'eis');
				$PlanetClass        = array('planet');
				$PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '00',);
				$planet['temp_min'] = rand(-120, 10);
				$planet['temp_max'] = $planet['temp_min'] + 40;
			}

			$planet['image']       = $PlanetType[ rand( 0, count( $PlanetType ) -1 ) ];
			$planet['image']      .= $PlanetClass[ rand( 0, count( $PlanetClass ) - 1 ) ];
			$planet['image']      .= $PlanetDesign[ rand( 0, count( $PlanetDesign ) - 1 ) ];
			$planet['planet_type'] = 1;
			$planet['id_owner']    = $PlanetOwnerID;
			$planet['last_update'] = time();
			$planet['name']        = ($PlanetName == '') ? $lang['sys_colo_defaultname'] : $PlanetName;

			$QryInsertPlanet  = "INSERT INTO {{table}} SET ";

			if($HomeWorld == false)
				$QryInsertPlanet .= "`name` = '".$lang['fcp_colony']."', ";

			$QryInsertPlanet .= "`id_owner` = '".          $planet['id_owner']          ."', ";
			$QryInsertPlanet .= "`id_level` = '".          $user['authlevel']           ."', ";
			//start mod
			$QryInsertPlanet .= "`universe` = '".            $planet['universe']            ."', ";
			//end mod
			$QryInsertPlanet .= "`galaxy` = '".            $planet['galaxy']            ."', ";
			$QryInsertPlanet .= "`system` = '".            $planet['system']            ."', ";
			$QryInsertPlanet .= "`planet` = '".            $planet['planet']            ."', ";
			$QryInsertPlanet .= "`last_update` = '".       $planet['last_update']       ."', ";
			$QryInsertPlanet .= "`planet_type` = '".       $planet['planet_type']       ."', ";
			$QryInsertPlanet .= "`image` = '".             $planet['image']             ."', ";
			$QryInsertPlanet .= "`diameter` = '".          $planet['diameter']          ."', ";
			$QryInsertPlanet .= "`field_max` = '".         $planet['field_max']         ."', ";
			$QryInsertPlanet .= "`temp_min` = '".          $planet['temp_min']          ."', ";
			$QryInsertPlanet .= "`temp_max` = '".          $planet['temp_max']          ."', ";
			$QryInsertPlanet .= "`metal` = '".             $planet['metal']             ."', ";
			$QryInsertPlanet .= "`metal_perhour` = '".     $planet['metal_perhour']     ."', ";
			$QryInsertPlanet .= "`metal_max` = '".         $planet['metal_max']         ."', ";
			$QryInsertPlanet .= "`crystal` = '".           $planet['crystal']           ."', ";
			$QryInsertPlanet .= "`crystal_perhour` = '".   $planet['crystal_perhour']   ."', ";
			$QryInsertPlanet .= "`crystal_max` = '".       $planet['crystal_max']       ."', ";
			$QryInsertPlanet .= "`deuterium` = '".         $planet['deuterium']         ."', ";
			$QryInsertPlanet .= "`deuterium_perhour` = '". $planet['deuterium_perhour'] ."', ";
			$QryInsertPlanet .= "`deuterium_max` = '".     $planet['deuterium_max']     ."';";
			doquery( $QryInsertPlanet, 'planets');

			$QrySelectPlanet  = "SELECT `id` ";
			$QrySelectPlanet .= "FROM {{table}} ";
			$QrySelectPlanet .= "WHERE ";
			//start mod
				$QrySelectPlanet .= "`universe` = '".   $planet['universe']   ."' AND ";
				//end mod
			$QrySelectPlanet .= "`galaxy` = '".   $planet['galaxy']   ."' AND ";
			$QrySelectPlanet .= "`system` = '".   $planet['system']   ."' AND ";
			$QrySelectPlanet .= "`planet` = '".   $planet['planet']   ."' AND ";
			$QrySelectPlanet .= "`id_owner` = '". $planet['id_owner'] ."';";
			$GetPlanetID      = doquery( $QrySelectPlanet , 'planets', true);

			$QrySelectGalaxy  = "SELECT * ";
			$QrySelectGalaxy .= "FROM {{table}} ";
			$QrySelectGalaxy .= "WHERE ";
			//start mod
			$QrySelectGalaxy .= "`universe` = '". $planet['universe'] ."' AND ";
			//end mod
			$QrySelectGalaxy .= "`galaxy` = '". $planet['galaxy'] ."' AND ";
			$QrySelectGalaxy .= "`system` = '". $planet['system'] ."' AND ";
			$QrySelectGalaxy .= "`planet` = '". $planet['planet'] ."';";
			$GetGalaxyID      = doquery( $QrySelectGalaxy, 'galaxy', true);

			if ($GetGalaxyID)
			{
				$QryUpdateGalaxy  = "UPDATE {{table}} SET ";
				$QryUpdateGalaxy .= "`id_planet` = '". $GetPlanetID['id'] ."' ";
				$QryUpdateGalaxy .= "WHERE ";
				//start mod
			$QryUpdateGalaxy .= "`universe` = '". $planet['universe'] ."' AND ";
			//end mod
				$QryUpdateGalaxy .= "`galaxy` = '". $planet['galaxy'] ."' AND ";
				$QryUpdateGalaxy .= "`system` = '". $planet['system'] ."' AND ";
				$QryUpdateGalaxy .= "`planet` = '". $planet['planet'] ."';";
				doquery( $QryUpdateGalaxy, 'galaxy');
			}
			else
			{
				$QryInsertGalaxy  = "INSERT INTO {{table}} SET ";
				//start mod
				$QryInsertGalaxy .= "`universe` = '". $planet['universe'] ."', ";
			//end mod
				$QryInsertGalaxy .= "`galaxy` = '". $planet['galaxy'] ."', ";
				$QryInsertGalaxy .= "`system` = '". $planet['system'] ."', ";
				$QryInsertGalaxy .= "`planet` = '". $planet['planet'] ."', ";
				$QryInsertGalaxy .= "`id_planet` = '". $GetPlanetID['id'] ."';";
				doquery( $QryInsertGalaxy, 'galaxy');
			}

			$RetValue = true;
		}
		else
		{
			$RetValue = false;
		}

		return $RetValue;
	}
?>