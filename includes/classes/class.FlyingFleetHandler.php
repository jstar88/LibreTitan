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

if (!defined('INSIDE'))die(header("location:../../"));

class FlyingFleetHandler
{
	protected static function calculateAKSSteal($attackFleets, $FleetRow, $defenderPlanet, $ForSim = false)
    {
        //Steal-Math by Slaver for 2Moons(http://www.titanspace.org) based on http://www.owiki.de/Beute
        global $pricelist, $db;
        
        $SortFleets = array();
        foreach ($attackFleets as $FleetID => $Attacker)
        {
            foreach($Attacker['detail'] as $Element => $amount)    
            {
                if ($Element != 210)
                  $SortFleets[$FleetID]        += $pricelist[$Element]['capacity'] * $amount;
            }
            
            $SortFleets[$FleetID]            -= $Attacker['fleet']['fleet_resource_metal'] - $Attacker['fleet']['fleet_resource_crystal'] - $Attacker['fleet']['fleet_resource_deuterium'];
        }
        
        $Sumcapacity              = array_sum($SortFleets);
        //FIX JTSAMPER
		$booty['deuterium']       = min($Sumcapacity / 3,  ($defenderPlanet['deuterium'] / 2));
		$Sumcapacity             -= $booty['deuterium'];



		$booty['crystal']         = min(($Sumcapacity / 2),  ($defenderPlanet['crystal'] / 2));
		$Sumcapacity             -= $booty['crystal'];

		$booty['metal']           = min(($Sumcapacity ),  ($defenderPlanet['metal'] / 2));
		$Sumcapacity             -= $booty['metal'];


		$oldMetalBooty            = $booty['crystal'] ;
		$booty['crystal']         += min(($Sumcapacity /2 ),  max((($defenderPlanet['crystal']) / 2) - $booty['crystal'], 0));

		$Sumcapacity             += $oldMetalBooty - $booty['crystal'] ;

		$booty['metal']          += min(($Sumcapacity ),  max(($defenderPlanet['metal'] / 2) - $booty['metal'], 0));


		$booty['metal']             = max($booty['metal'] ,0);
		$booty['crystal']           = max($booty['crystal'] ,0);
		$booty['deuterium']         = max($booty['deuterium'] ,0);
		//END FIX

        $steal                 = array_map('floor', $booty);
        if($ForSim)
            return $steal;
            
        $AllCapacity    = array_sum($SortFleets);
        $QryUpdateFleet    = "";

        foreach($SortFleets as $FleetID => $Capacity)
        {
            $QryUpdateFleet = 'UPDATE {{table}} SET ';
		    	$QryUpdateFleet .= '`fleet_resource_metal` = `fleet_resource_metal` + '.floattostring($steal['metal'] * ($Capacity / $AllCapacity)).', ';
			   $QryUpdateFleet .= '`fleet_resource_crystal` = `fleet_resource_crystal` +'.floattostring($steal['crystal'] * ($Capacity / $AllCapacity)).', ';
			   $QryUpdateFleet .= '`fleet_resource_deuterium` = `fleet_resource_deuterium` +'.floattostring($steal['deuterium'] * ($Capacity / $AllCapacity)).' ';
			   $QryUpdateFleet .= 'WHERE fleet_id = '.$FleetID.' ';
			   $QryUpdateFleet .= 'LIMIT 1;';    
            doquery($QryUpdateFleet, 'fleets');
    
        }
        
        return $steal;
    } 

	

	protected function walka ($CurrentSet, $TargetSet, $CurrentTechno, $TargetTechno)
	{
		global $pricelist, $CombatCaps, $game_config, $user;

		$runda       = array();
		$atakujacy_n = array();
		$wrog_n      = array();

		if (!is_null($CurrentSet))
		{
			$atakujacy_zlom_poczatek['metal']   = 0;
			$atakujacy_zlom_poczatek['crystal'] = 0;
			foreach($CurrentSet as $a => $b)
			{
				$atakujacy_zlom_poczatek['metal']   = $atakujacy_zlom_poczatek['metal']   + $CurrentSet[$a]['count'] * $pricelist[$a]['metal'];
				$atakujacy_zlom_poczatek['crystal'] = $atakujacy_zlom_poczatek['crystal'] + $CurrentSet[$a]['count'] * $pricelist[$a]['crystal'];
			}
		}

		$wrog_zlom_poczatek['metal']   	= 0;
		$wrog_zlom_poczatek['crystal'] 	= 0;
		$wrog_poczatek 					= $TargetSet;

		if (!is_null($TargetSet))
		{
			foreach($TargetSet as $a => $b)
			{
				if ($a < 300)
				{
					$wrog_zlom_poczatek['metal']   = $wrog_zlom_poczatek['metal']   + $TargetSet[$a]['count'] * $pricelist[$a]['metal'];
					$wrog_zlom_poczatek['crystal'] = $wrog_zlom_poczatek['crystal'] + $TargetSet[$a]['count'] * $pricelist[$a]['crystal'];
				}
				else
				{
					$wrog_zlom_poczatek_obrona['metal']   = $wrog_zlom_poczatek_obrona['metal']   + $TargetSet[$a]['count'] * $pricelist[$a]['metal'];
					$wrog_zlom_poczatek_obrona['crystal'] = $wrog_zlom_poczatek_obrona['crystal'] + $TargetSet[$a]['count'] * $pricelist[$a]['crystal'];
				}
			}
		}

		for ($i = 1; $i <= 7; $i++)
		{
			$atakujacy_atak   = 0;
			$wrog_atak        = 0;
			$atakujacy_obrona = 0;
			$wrog_obrona      = 0;
			$atakujacy_ilosc  = 0;
			$wrog_ilosc       = 0;
			$wrog_tarcza      = 0;
			$atakujacy_tarcza = 0;

			if (!is_null($CurrentSet))
			{
				foreach($CurrentSet as $a => $b)
				{
					$CurrentSet[$a]["obrona"] 	= $CurrentSet[$a]['count'] * ($pricelist[$a]['metal'] + $pricelist[$a]['crystal']) / 10 * (1 + (0.1 * ($CurrentTechno["defence_tech"]) + (AMIRAL * $user['rpg_amiral'])));
					$rand 						= rand(80, 120) / 100;
					$CurrentSet[$a]["tarcza"] 	= $CurrentSet[$a]['count'] * $CombatCaps[$a]['shield'] * (1 + (0.1 * $CurrentTechno["shield_tech"]) + (AMIRAL * $user['rpg_amiral'])) * $rand;
					$atak_statku 				= $CombatCaps[$a]['attack'];
					$technologie 				= (1 + (0.1 * $CurrentTechno["military_tech"]+(AMIRAL * $user['rpg_amiral'])));
					$rand 						= rand(80, 120) / 100;
					$ilosc 						= $CurrentSet[$a]['count'];
					$CurrentSet[$a]["atak"] 	= $ilosc * $atak_statku * $technologie * $rand;
					$atakujacy_atak			 	= $atakujacy_atak + $CurrentSet[$a]["atak"];
					$atakujacy_obrona 			= $atakujacy_obrona + $CurrentSet[$a]["obrona"];
					$atakujacy_ilosc 			= $atakujacy_ilosc + $CurrentSet[$a]['count'];
				}
			}
			else
			{
				$atakujacy_ilosc = 0;
				break;
			}

			if (!is_null($TargetSet))
			{
				foreach($TargetSet as $a => $b)
				{
					$TargetSet[$a]["obrona"] 	= $TargetSet[$a]['count'] * ($pricelist[$a]['metal'] + $pricelist[$a]['crystal']) / 10 * (1 + (0.1 * ($TargetTechno["defence_tech"]) + (AMIRAL* $user['rpg_amiral'])));
					$rand 						= rand(80, 120) / 100;
					$TargetSet[$a]["tarcza"] 	= $TargetSet[$a]['count'] * $CombatCaps[$a]['shield'] * (1 + (0.1 * $TargetTechno["shield_tech"])+ (AMIRAL * $user['rpg_amiral'])) * $rand;
					$atak_statku 				= $CombatCaps[$a]['attack'];
					$technologie 				= (1 + (0.1 * $TargetTechno["military_tech"]) + (AMIRAL * $user['rpg_amiral']));
					$rand 						= rand(80, 120) / 100;
					$ilosc 						= $TargetSet[$a]['count'];
					$TargetSet[$a]["atak"] 		= $ilosc * $atak_statku * $technologie * $rand;
					$wrog_atak 					= $wrog_atak + $TargetSet[$a]["atak"];
					$wrog_obrona 				= $wrog_obrona + $TargetSet[$a]["obrona"];
					$wrog_ilosc 				= $wrog_ilosc + $TargetSet[$a]['count'];
				}
			}
			else
			{
				$wrog_ilosc 						= 0;
				$runda[$i]["atakujacy"] 			= $CurrentSet;
				$runda[$i]["wrog"] 					= $TargetSet;
				$runda[$i]["atakujacy"]["atak"] 	= $atakujacy_atak;
				$runda[$i]["wrog"]["atak"] 			= $wrog_atak;
				$runda[$i]["atakujacy"]['count'] 	= $atakujacy_ilosc;
				$runda[$i]["wrog"]['count'] 		= $wrog_ilosc;
				break;
			}

			$runda[$i]["atakujacy"] 			= $CurrentSet;
			$runda[$i]["wrog"] 					= $TargetSet;
			$runda[$i]["atakujacy"]["atak"] 	= $atakujacy_atak;
			$runda[$i]["wrog"]["atak"] 			= $wrog_atak;
			$runda[$i]["atakujacy"]['count']	= $atakujacy_ilosc;
			$runda[$i]["wrog"]['count'] 		= $wrog_ilosc;

			if (($atakujacy_ilosc == 0) or ($wrog_ilosc == 0))
				break;

			foreach($CurrentSet as $a => $b)
			{
				if ($atakujacy_ilosc > 0)
				{
					$wrog_moc = $CurrentSet[$a]['count'] * $wrog_atak / $atakujacy_ilosc;
					if ($CurrentSet[$a]["tarcza"] < $wrog_moc)
					{
						$max_zdjac = floor($CurrentSet[$a]['count'] * $wrog_ilosc / $atakujacy_ilosc);
						$wrog_moc = $wrog_moc - $CurrentSet[$a]["tarcza"];
						$atakujacy_tarcza = $atakujacy_tarcza + $CurrentSet[$a]["tarcza"];
						$ile_zdjac = floor(($wrog_moc / (($pricelist[$a]['metal'] + $pricelist[$a]['crystal']) / 10)));

						if ($ile_zdjac > $max_zdjac)
							$ile_zdjac = $max_zdjac;
						$atakujacy_n[$a]['count'] = ceil($CurrentSet[$a]['count'] - $ile_zdjac);

						if ($atakujacy_n[$a]['count'] <= 0)
							$atakujacy_n[$a]['count'] = 0;
					}
					else
					{
						$atakujacy_n[$a]['count'] = $CurrentSet[$a]['count'];
						$atakujacy_tarcza = $atakujacy_tarcza + $wrog_moc;
					}
				}
				else
				{
					$atakujacy_n[$a]['count'] = $CurrentSet[$a]['count'];
					$atakujacy_tarcza = $atakujacy_tarcza + $wrog_moc;
				}
			}

			foreach($TargetSet as $a => $b)
			{
				if ($wrog_ilosc > 0)
				{
					$atakujacy_moc = $TargetSet[$a]['count'] * $atakujacy_atak / $wrog_ilosc;
					if ($TargetSet[$a]["tarcza"] < $atakujacy_moc)
					{
						$max_zdjac = floor($TargetSet[$a]['count'] * $atakujacy_ilosc / $wrog_ilosc);
						$atakujacy_moc = $atakujacy_moc - $TargetSet[$a]["tarcza"];
						$wrog_tarcza = $wrog_tarcza + $TargetSet[$a]["tarcza"];

						$ile_zdjac = floor(($atakujacy_moc / (($pricelist[$a]['metal'] + $pricelist[$a]['crystal']) / 10)));

						if ($ile_zdjac > $max_zdjac)
							$ile_zdjac = $max_zdjac;

						$wrog_n[$a]['count'] = ceil($TargetSet[$a]['count'] - $ile_zdjac);

						if ($wrog_n[$a]['count'] <= 0)
							$wrog_n[$a]['count'] = 0;
					}
					else
					{
						$wrog_n[$a]['count'] = $TargetSet[$a]['count'];
						$wrog_tarcza = $wrog_tarcza + $atakujacy_moc;
					}
				}
				else
				{
					$wrog_n[$a]['count'] = $TargetSet[$a]['count'];
					$wrog_tarcza = $wrog_tarcza + $atakujacy_moc;
				}
			}

			foreach($CurrentSet as $a => $b)
			{
				foreach ($CombatCaps[$a]['sd'] as $c => $d)
				{
					if (isset($TargetSet[$c]))
					{
						$wrog_n[$c]['count'] = $wrog_n[$c]['count'] - floor($d * rand(50, 100) / 100);
						if ($wrog_n[$c]['count'] <= 0)
							$wrog_n[$c]['count'] = 0;
					}
				}
			}

			foreach($TargetSet as $a => $b)
			{
				foreach ($CombatCaps[$a]['sd'] as $c => $d)
				{
					if (isset($CurrentSet[$c]))
					{
						$atakujacy_n[$c]['count'] = $atakujacy_n[$c]['count'] - floor($d * rand(50, 100) / 100);
						if ($atakujacy_n[$c]['count'] <= 0)
							$atakujacy_n[$c]['count'] = 0;
					}
				}
			}

			$runda[$i]["atakujacy"]["tarcza"] 	= $atakujacy_tarcza;
			$runda[$i]["wrog"]["tarcza"] 		= $wrog_tarcza;
			$TargetSet 							= $wrog_n;
			$CurrentSet 						= $atakujacy_n;
		}

		if (($atakujacy_ilosc == 0) or ($wrog_ilosc == 0))
		{
			if (($atakujacy_ilosc == 0) and ($wrog_ilosc == 0))
				$wygrana = "r";
			else
			if ($atakujacy_ilosc == 0)
				$wygrana = "w";
			else
				$wygrana = "a";
		}
		else
		{
			$i = sizeof($runda);
			$runda[$i]["atakujacy"] = $CurrentSet;
			$runda[$i]["wrog"] = $TargetSet;
			$runda[$i]["atakujacy"]["atak"] = $atakujacy_atak;
			$runda[$i]["wrog"]["atak"] = $wrog_atak;
			$runda[$i]["atakujacy"]['count'] = $atakujacy_ilosc;
			$runda[$i]["wrog"]['count'] = $wrog_ilosc;
			$wygrana = "r";
		}

		$atakujacy_zlom_koniec['metal'] = 0;
		$atakujacy_zlom_koniec['crystal'] = 0;
		if (!is_null($CurrentSet))
		{
			foreach($CurrentSet as $a => $b)
			{
				$atakujacy_zlom_koniec['metal']   = $atakujacy_zlom_koniec['metal'] + $CurrentSet[$a]['count'] * $pricelist[$a]['metal'];
				$atakujacy_zlom_koniec['crystal'] = $atakujacy_zlom_koniec['crystal'] + $CurrentSet[$a]['count'] * $pricelist[$a]['crystal'];
			}
		}

		$wrog_zlom_koniec['metal'] = 0;
		$wrog_zlom_koniec['crystal'] = 0;
		if (!is_null($TargetSet))
		{
			foreach($TargetSet as $a => $b)
			{
				if ($a < 300)
				{
					$wrog_zlom_koniec['metal'] = $wrog_zlom_koniec['metal'] + $TargetSet[$a]['count'] * $pricelist[$a]['metal'];
					$wrog_zlom_koniec['crystal'] = $wrog_zlom_koniec['crystal'] + $TargetSet[$a]['count'] * $pricelist[$a]['crystal'];
				}
				else
				{
					$wrog_zlom_koniec_obrona['metal'] = $wrog_zlom_koniec_obrona['metal'] + $TargetSet[$a]['count'] * $pricelist[$a]['metal'];
					$wrog_zlom_koniec_obrona['crystal'] = $wrog_zlom_koniec_obrona['crystal'] + $TargetSet[$a]['count'] * $pricelist[$a]['crystal'];
				}
			}
		}
		$ilosc_wrog = 0;
		$straty_obrona_wrog = 0;

		if (!is_null($TargetSet))
		{
			foreach($TargetSet as $a => $b)
			{
				if ($a > 300)
				{
					$straty_obrona_wrog = $straty_obrona_wrog + (($wrog_poczatek[$a]['count'] - $TargetSet[$a]['count']) * ($pricelist[$a]['metal'] + $pricelist[$a]['crystal']));
					$TargetSet[$a]['count'] = $TargetSet[$a]['count'] + (($wrog_poczatek[$a]['count'] - $TargetSet[$a]['count']) * rand(60, 80) / 100);
					$ilosc_wrog = $ilosc_wrog + $TargetSet[$a]['count'];
				}
			}
		}

		if (($ilosc_wrog > 0) && ($atakujacy_ilosc == 0))
			$wygrana = "w";

		$zlom['metal']    = ((($atakujacy_zlom_poczatek['metal']   - $atakujacy_zlom_koniec['metal'])   + ($wrog_zlom_poczatek['metal']   - $wrog_zlom_koniec['metal']))   * ($game_config['Fleet_Cdr'] / 100));
		$zlom['crystal']  = ((($atakujacy_zlom_poczatek['crystal'] - $atakujacy_zlom_koniec['crystal']) + ($wrog_zlom_poczatek['crystal'] - $wrog_zlom_koniec['crystal'])) * ($game_config['Fleet_Cdr'] / 100));

		$zlom['metal']   += ((($atakujacy_zlom_poczatek['metal']   - $atakujacy_zlom_koniec['metal'])   + ($wrog_zlom_poczatek['metal']   - $wrog_zlom_koniec['metal']))   * ($game_config['Defs_Cdr'] / 100));
		$zlom['crystal'] += ((($atakujacy_zlom_poczatek['crystal'] - $atakujacy_zlom_koniec['crystal']) + ($wrog_zlom_poczatek['crystal'] - $wrog_zlom_koniec['crystal'])) * ($game_config['Defs_Cdr'] / 100));

		$zlom["atakujacy"] = (($atakujacy_zlom_poczatek['metal'] - $atakujacy_zlom_koniec['metal']) + ($atakujacy_zlom_poczatek['crystal'] - $atakujacy_zlom_koniec['crystal']));
		$zlom["wrog"]      = (($wrog_zlom_poczatek['metal']      - $wrog_zlom_koniec['metal'])      + ($wrog_zlom_poczatek['crystal']      - $wrog_zlom_koniec['crystal']) + $straty_obrona_wrog);

		return array("atakujacy" => $CurrentSet, "wrog" => $TargetSet, "wygrana" => $wygrana, "dane_do_rw" => $runda, "zlom" => $zlom);
	}

	protected function RestoreFleetToPlanet ($FleetRow, $Start = true)
	{
		global $resource;

      //fix resource by jstar
		$targetPlanet = doquery("SELECT * FROM {{table}} WHERE `universe` = ". intval($FleetRow['fleet_start_universe']) ." AND`galaxy` = ". intval($FleetRow['fleet_start_galaxy']) ." AND `system` = ". intval($FleetRow['fleet_start_system']) ." AND `planet_type` = ". intval($FleetRow['fleet_start_type']) ." AND `planet` = ". intval($FleetRow['fleet_start_planet']) .";",'planets', true);
		$targetUser   = doquery('SELECT * FROM {{table}} WHERE id='.intval($targetPlanet['id_owner']),'users', true);
		PlanetResourceUpdate ( $targetUser, $targetPlanet, time() );
		//
		
      $FleetRecord         = explode(";", $FleetRow['fleet_array']);
		$QryUpdFleet         = "";
		foreach ($FleetRecord as $Item => $Group)
		{
			if ($Group != '')
			{
				$Class        = explode (",", $Group);
				$QryUpdFleet .= "`". $resource[$Class[0]] ."` = `".$resource[$Class[0]]."` + '".$Class[1]."', \n";
			}
		}

		$QryUpdatePlanet   = "UPDATE {{table}} SET ";
		if ($QryUpdFleet != "")
			$QryUpdatePlanet  .= $QryUpdFleet;

		$QryUpdatePlanet  .= "`metal` = `metal` + '". $FleetRow['fleet_resource_metal'] ."', ";
		$QryUpdatePlanet  .= "`crystal` = `crystal` + '". $FleetRow['fleet_resource_crystal'] ."', ";
		$QryUpdatePlanet  .= "`deuterium` = `deuterium` + '". $FleetRow['fleet_resource_deuterium'] ."' ";
		$QryUpdatePlanet  .= "WHERE ";

		if ($Start == true)
		{
		  //start mod
		  $QryUpdatePlanet  .= "`universe` = '". $FleetRow['fleet_start_universe'] ."' AND ";
			//end mod
      $QryUpdatePlanet  .= "`galaxy` = '".   $FleetRow['fleet_start_galaxy']   ."' AND ";
			$QryUpdatePlanet  .= "`system` = '". $FleetRow['fleet_start_system'] ."' AND ";
			$QryUpdatePlanet  .= "`planet` = '". $FleetRow['fleet_start_planet'] ."' AND ";
			$QryUpdatePlanet  .= "`planet_type` = '". $FleetRow['fleet_start_type'] ."' ";
		}
		else
		{
		  //start mod
		  $QryUpdatePlanet  .= "`universe` = '". $FleetRow['fleet_end_universe'] ."' AND ";
			//end mod
			$QryUpdatePlanet  .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
			$QryUpdatePlanet  .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
			$QryUpdatePlanet  .= "`planet` = '". $FleetRow['fleet_end_planet'] ."' AND ";
			$QryUpdatePlanet  .= "`planet_type` = '". $FleetRow['fleet_end_type'] ."' ";
		}
		$QryUpdatePlanet  .= "LIMIT 1;";
		doquery( $QryUpdatePlanet, 'planets');
	}

	protected function StoreGoodsToPlanet ($FleetRow, $Start = false)
	{
	   //fix resource by jstar
		$targetPlanet = doquery("SELECT * FROM {{table}} WHERE `universe` = ". intval($FleetRow['fleet_start_universe']) ." AND`galaxy` = ". intval($FleetRow['fleet_start_galaxy']) ." AND `system` = ". intval($FleetRow['fleet_start_system']) ." AND `planet_type` = ". intval($FleetRow['fleet_start_type']) ." AND `planet` = ". intval($FleetRow['fleet_start_planet']) .";",'planets', true);
		$targetUser   = doquery('SELECT * FROM {{table}} WHERE id='.intval($targetPlanet['id_owner']),'users', true);
		PlanetResourceUpdate ( $targetUser, $targetPlanet, time() );
		//
		$QryUpdatePlanet   = "UPDATE {{table}} SET ";
		$QryUpdatePlanet  .= "`metal` = `metal` + '". $FleetRow['fleet_resource_metal'] ."', ";
		$QryUpdatePlanet  .= "`crystal` = `crystal` + '". $FleetRow['fleet_resource_crystal'] ."', ";
		$QryUpdatePlanet  .= "`deuterium` = `deuterium` + '". $FleetRow['fleet_resource_deuterium'] ."' ";
		$QryUpdatePlanet  .= "WHERE ";

		if ($Start == true)
		{
		  //start mod
		  $QryUpdatePlanet  .= "`universe` = '". $FleetRow['fleet_start_universe'] ."' AND ";
			//end mod
      $QryUpdatePlanet  .= "`galaxy` = '". $FleetRow['fleet_start_galaxy'] ."' AND ";
			$QryUpdatePlanet  .= "`system` = '". $FleetRow['fleet_start_system'] ."' AND ";
			$QryUpdatePlanet  .= "`planet` = '". $FleetRow['fleet_start_planet'] ."' AND ";
			$QryUpdatePlanet  .= "`planet_type` = '". $FleetRow['fleet_start_type'] ."' ";
		}
		else
		{
		  //start mod
		  $QryUpdatePlanet  .= "`universe` = '". $FleetRow['fleet_end_universe'] ."' AND ";
			//end mod
      $QryUpdatePlanet  .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
			$QryUpdatePlanet  .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
			$QryUpdatePlanet  .= "`planet` = '". $FleetRow['fleet_end_planet'] ."' AND ";
			$QryUpdatePlanet  .= "`planet_type` = '". $FleetRow['fleet_end_type'] ."' ";
		}

		$QryUpdatePlanet  .= "LIMIT 1;";
		doquery( $QryUpdatePlanet, 'planets');
	}
	
	protected function storeDebreeToGalaxy($metal,$crystal,$universe,$galaxy,$system,$planet)
   {
      $QryUpdateGalaxy = "UPDATE {{table}} SET ";
		$QryUpdateGalaxy .= "`metal` = `metal` +'".$metal . "', ";
		$QryUpdateGalaxy .= "`crystal` = `crystal` + '" .$crystal. "' ";
		$QryUpdateGalaxy .= "WHERE ";
		$QryUpdateGalaxy .= "`universe`= '". $universe . "' AND ";
      $QryUpdateGalaxy .= "`galaxy` = '" . $galaxy . "' AND ";
		$QryUpdateGalaxy .= "`system` = '" . $system . "' AND ";
		$QryUpdateGalaxy .= "`planet` = '" . $planet . "' ";
		$QryUpdateGalaxy .= "LIMIT 1;";
		doquery($QryUpdateGalaxy , 'galaxy');
   }
   
   protected function tryMoon($totalDebree,$universe,$galaxy,$planet,$targetUserID,$startTime,$MoonChance)
   {
			if ( mt_rand(1, 100) <= $MoonChance )
				return CreateOneMoonRecord ($universe,$galaxy,$planet,$targetUserID,$startTime, '', $MoonChance );
         return NULL;      
   }
   
   protected function getMoonProb($totalDebree)
   {
      if($totalDebree < 100000)
		   return 0;
      elseif($totalDebree > 2000000)
			return 20;
      else
         return $totalDebree / 100000;
   }

   protected function isArriveToDestination($FleetRow){
      if ($FleetRow['fleet_start_time'] <= time && $FleetRow['fleet_mess'] == 0)
         return true;
      return false;  
   }
   protected function isReturnedToHome($FleetRow){
      if($FleetRow['fleet_end_time'] <= time())
         return true;
      return false;
   }
   protected function isMissionEnded($FleetRow){
      if($FleetRow['fleet_mess'] == 1)
         return true;
      return false;
   }
   protected function isStayEnded($FleetRow){
     if ($FleetRow['fleet_end_stay'] <= time())
      return true;
     return false; 
   }
   
   protected function getStartPlanetInfoInCache($FleetRow){
      global $planetrow;
      if( isset($planetrow) && $planetrow['id'] == $FleetRow['fleet_owner'] )
         return $planetrow;
      return doquery('SELECT * FROM {{table}} WHERE `universe` = '.$FleetRow['fleet_start_universe'].' AND `galaxy` = '.$FleetRow['fleet_start_galaxy'].' AND `system` = '.$FleetRow['fleet_start_system'].' AND `planet` = '.$FleetRow['fleet_start_planet'].' AND `planet_type` = '.$FleetRow['fleet_start_type'], 'planets', true);     
   }
   protected function getTargetPlanetInfoInCache($FleetRow){     
     global $planetrow;
      if( isset($planetrow) && $planetrow['id'] == $FleetRow['fleet_target_owner'] )
        return  $UserPlanet = $planetrow;
      return  doquery('SELECT * FROM {{table}} WHERE `universe` = '.$FleetRow['fleet_end_universe'].' AND `galaxy` = '.$FleetRow['fleet_end_galaxy'].' AND `system` = '.$FleetRow['fleet_end_system'].' AND `planet` = '.$FleetRow['fleet_end_planet'].' AND `planet_type` = '.$FleetRow['fleet_end_type'], 'planets', true); 
   } 
   protected function getStartUser($FleetRow){ //only if you need more than ID,otherwise use fleet['fleet_owner']
    global $user;
      if( isset($user) && $user['id']==$FleetRow['fleet_owner'] )
         return $user;
      return doquery("SELECT * FROM {{table}} WHERE `id` = '".$FleetRow['fleet_owner']."';", 'users', true);
   }
   protected function getTargetUser($FleetRow){ //only if you need more than ID,otherwise use fleet['fleet_target_owner']
    global $user;
      if( isset($user) && $user['id']==$FleetRow['fleet_target_owner'] )
         return $user;
      return doquery("SELECT * FROM {{table}} WHERE `id` = '".$FleetRow['fleet_target_owner']."';", 'users', true);
   }
   	

	

	public function __construct (&$planet)
	{
		global $resource,$user,$xgp_root,$phpEx;

		//doquery("LOCK TABLE {{table}}aks WRITE, {{table}}rw WRITE, {{table}}errors WRITE, {{table}}messages WRITE, {{table}}fleets WRITE,  {{table}}planets WRITE, {{table}}galaxy WRITE ,{{table}}users WRITE", "");
    doquery("LOCK TABLE {{table}}aks WRITE, {{table}}rw WRITE, {{table}}errors WRITE, {{table}}messages WRITE, {{table}}statpoints WRITE, {{table}}fleets WRITE,   {{table}}planets WRITE, {{table}}galaxy WRITE ,{{table}}users WRITE",  "");
		
      $QryFleet   = "SELECT * FROM {{table}} ";
		$QryFleet  .= "WHERE (";
		$QryFleet  .= "( ";
		//start mod
		$QryFleet  .= "`fleet_start_universe` = ". $planet['universe']      ." AND ";
		//end mod
		$QryFleet  .= "`fleet_start_galaxy` = ". $planet['galaxy']      ." AND ";
		$QryFleet  .= "`fleet_start_system` = ". $planet['system']      ." AND ";
		$QryFleet  .= "`fleet_start_planet` = ". $planet['planet']      ." AND ";
		$QryFleet  .= "`fleet_start_type` = ".   $planet['planet_type'] ." ";
		$QryFleet  .= ") OR ( ";
		//start mod
		$QryFleet  .= "`fleet_end_universe` = ". $planet['universe']      ." AND ";
		//end mod
		$QryFleet  .= "`fleet_end_galaxy` = ".   $planet['galaxy']      ." AND ";
		$QryFleet  .= "`fleet_end_system` = ".   $planet['system']      ." AND ";
		$QryFleet  .= "`fleet_end_planet` = ".   $planet['planet']      ." ) AND ";
		$QryFleet  .= "`fleet_end_type`= ".      $planet['planet_type'] ." ) AND ";
		$QryFleet  .= "( `fleet_start_time` < '". time() ."' OR `fleet_end_time` < '". time() ."' );";
		$fleetquery = doquery( $QryFleet, 'fleets' );
      
		$lang_memo=array();
		while ($CurrentFleet = mysql_fetch_array($fleetquery))
		{
			switch ($CurrentFleet["fleet_mission"])
			{
				case 1:
               include($xgp_root . 'includes/classes/missions/MissionCaseAttack.' . $phpEx);  
					new MissionCaseAttack($CurrentFleet);
					break;

				case 2:
				   include($xgp_root . 'includes/classes/missions/MissionCaseACS.' . $phpEx);
					new MissionCaseACS($CurrentFleet);
					break;

				case 3:
				   include($xgp_root . 'includes/classes/missions/MissionCaseTransport.' . $phpEx);  
				   new MissionCaseTransport($CurrentFleet);
					break;

				case 4:
               include($xgp_root . 'includes/classes/missions/MissionCaseStay.' . $phpEx);  
					new MissionCaseStay($CurrentFleet);
					break;

				case 5:
				   include($xgp_root . 'includes/classes/missions/MissionCaseStayAlly.' . $phpEx);
					new MissionCaseStayAlly($CurrentFleet);
					break;

				case 6:
               include($xgp_root . 'includes/classes/missions/MissionCaseSpy.' . $phpEx);  
					new MissionCaseSpy($CurrentFleet);
					break;

				case 7:
               include($xgp_root . 'includes/classes/missions/MissionCaseColonisation.' . $phpEx);  
					new MissionCaseColonisation($CurrentFleet);
					break;

				case 8:
               include($xgp_root . 'includes/classes/missions/MissionCaseRecycling.' . $phpEx);  
					new MissionCaseRecycling($CurrentFleet);
					break;

				case 9:
               include($xgp_root . 'includes/classes/missions/MissionCaseDestruction.' . $phpEx);  
					new MissionCaseDestruction($CurrentFleet);
					break;

				case 10: 
               include($xgp_root . 'includes/classes/missions/MissionCaseMIP.' . $phpEx); 
					new MissionCaseMIP($CurrentFleet);
					break;

				case 15:
				   include($xgp_root . 'includes/classes/missions/MissionCaseExpedition.' . $phpEx);
					new MissionCaseExpedition($CurrentFleet);
					break;

				default:
					doquery("DELETE FROM {{table}} WHERE `fleet_id` = '". $CurrentFleet['fleet_id'] ."';", 'fleets');

			}
		}

		doquery("UNLOCK TABLES", "");

	}
}
?>