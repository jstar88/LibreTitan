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

function ShowFleet3Page($CurrentUser, $CurrentPlanet)
{
	global $resource, $pricelist, $reslist, $phpEx, $lang, $xgp_root, $game_config;
	include_once($xgp_root . 'includes/functions/IsVacationMode.' . $phpEx);

	$parse = $lang;
	//start mod
	$thisUniverse=$CurrentPlanet['universe'];
	
	$universe             = intval($_POST['universe']);
	$in_war=isUniverseInWar($universe,$thisUniverse)==1;
	//end mod

	if (IsVacationMode($CurrentUser))
		exit(message($lang['fl_vacation_mode_active'],"game.php?page=overview",2));
	

	$fleet_group_mr = 0;
	if($_POST['fleet_group'] > 0)
	{
		if($_POST['mission'] == 2)
		{
		//start mod
		if($in_war)
			$target = "u".$universe."g".intval($_POST["galaxy"])."s".intval($_POST["system"])."p".intval($_POST["planet"])."t".intval($_POST["planettype"]);
		else
		  $target = "g".intval($_POST["galaxy"])."s".intval($_POST["system"])."p".intval($_POST["planet"])."t".intval($_POST["planettype"]);
    //end mod
    	if($_POST['acs_target_mr'] == $target)
			{
				$aks_count_mr = mysql_result(doquery ("SELECT count(*) FROM {{table}} WHERE `id` = '". intval($_POST['fleet_group']) ."'", 'aks'), 0);
            //$aks_count_mr = doquery("SELECT * FROM {{table}} WHERE id = '".intval($_POST['fleet_group'])."'",'aks');
				if ($aks_count_mr > 0)
				{
					$fleet_group_mr = $_POST['fleet_group'];
				}
			}
		}
	}

	if(($_POST['fleet_group'] == 0) && ($_POST['mission'] == 2))
		$_POST['mission'] = 1;

  //start mod
  if($universe==0)
   $universe=$thisUniverse;
   
	$TargetPlanet  		= doquery("SELECT `id_owner`,`id_level`,`destruyed`,`ally_deposit` FROM {{table}} WHERE 
  `universe` = '". $universe ."' AND
  `galaxy` = '". intval($_POST['galaxy']) ."' AND 
  `system` = '". intval($_POST['system']) ."' AND 
  `planet` = '". intval($_POST['planet']) ."' AND 
  `planet_type` = '". intval($_POST['planettype']) ."';", 'planets', true);
  
  //end mod
	$MyDBRec       		= doquery("SELECT `id`,`onlinetime`,`ally_id`,`urlaubs_modus` FROM {{table}} WHERE `id` = '". intval($CurrentUser['id'])."';", 'users', true);

	$protection      	= $game_config['noobprotection'];
	$protectiontime  	= $game_config['noobprotectiontime'];
	$protectionmulti 	= $game_config['noobprotectionmulti'];

	if ($protectiontime < 1)
		$protectiontime = 9999999999999999;

	$fleetarray  = unserialize(base64_decode(str_rot13($_POST["usedfleet"])));

	if($TargetPlanet["destruyed"] != 0)
		exit(header("Location: game.php?page=fleet"));

	if (!is_array($fleetarray))
		exit(header("Location: game.php?page=fleet"));

	foreach ($fleetarray as $Ship => $Count)
	{
		if ($Count > $CurrentPlanet[$resource[$Ship]])
			exit(header("location:game." . $phpEx . "?page=fleet"));
	}

	$error              = 0;

	$galaxy             = intval($_POST['galaxy']);
	$system             = intval($_POST['system']);
	$planet             = intval($_POST['planet']);
	$planettype         = intval($_POST['planettype']);
	$fleetmission       = intval($_POST['mission']);

   //fix by jstar
	if ( $fleetmission == 7 && ( count ( $fleetarray ) != 1 or !isset($fleetarray[208] ) ) )
	{
		exit(header("location:game." . $phpEx . "?page=fleet"));
	}
	if ($planettype != 1 && $planettype != 2 && $planettype != 3)
		exit(header("location:game." . $phpEx . "?page=fleet"));

	if ($fleetmission == 8)
	{
		$YourPlanet = false;
		$UsedPlanet = false;
		//start mod
		$select     = doquery("SELECT * FROM {{table}} WHERE universe = '". $universe ."' AND galaxy = '". $galaxy ."' AND system = '". $system ."' AND planet = '". $planet ."'", "planets");
      $select2    = doquery("SELECT metal, crystal FROM {{table}} WHERE universe = '". $universe ."' AND galaxy = '". $galaxy ."' AND system = '". $system ."' AND planet = '". $planet ."'", "galaxy",true);
		if($select2['metal'] == 0 && $select2['crystal'] == 0)
		{
			exit(header("location:game." . $phpEx . "?page=fleet"));
		}
    //end mod
	}
	else
	{
		$YourPlanet = false;
		$UsedPlanet = false;
		//start mod
		$select     = doquery("SELECT * FROM {{table}} WHERE
    universe = '". $universe ."' AND 
    galaxy = '". $galaxy ."' AND 
    system = '". $system ."' AND 
    planet = '". $planet ."' AND 
    planet_type = '". $planettype ."'", "planets");
    //end mod
	}

	if (
	//start mod
	$CurrentPlanet['universe'] == $universe &&
	//end mod
  $CurrentPlanet['galaxy'] == $galaxy && 
  $CurrentPlanet['system'] == $system &&
	$CurrentPlanet['planet'] == $planet && 
  $CurrentPlanet['planet_type'] == $planettype
  )
		exit(header("location:game." . $phpEx . "?page=fleet"));

	if ($_POST['mission'] != 15)
	{
		if (mysql_num_rows($select) < 1 && $fleetmission != 7)
			exit(header("location:game." . $phpEx . "?page=fleet"));
		elseif ($fleetmission == 9 && mysql_num_rows($select) < 1)
			exit(header("location:game." . $phpEx . "?page=fleet"));
	}
	else
	{
		$MaxExpedition      = $CurrentUser[$resource[124]];

		if ($MaxExpedition >= 1)
		{
			$maxexpde  			= doquery("SELECT COUNT(fleet_owner) AS `expedi` FROM {{table}} WHERE `fleet_owner` = '".intval($CurrentUser['id'])."' AND `fleet_mission` = '15';", 'fleets', true);
			$ExpeditionEnCours  = $maxexpde['expedi'];
			$EnvoiMaxExpedition = 1 + floor( $MaxExpedition / 3 );
		}
		else
		{
			$ExpeditionEnCours 	= 0;
			$EnvoiMaxExpedition = 0;
		}

		if($EnvoiMaxExpedition == 0 )
			message ("<font color=\"red\"><b>".$lang['fl_expedition_tech_required']."</b></font>", "game." . $phpEx . "?page=fleet", 2);
		elseif ($ExpeditionEnCours >= $EnvoiMaxExpedition )
			message ("<font color=\"red\"><b>".$lang['fl_expedition_fleets_limit']."</b></font>", "game." . $phpEx . "?page=fleet", 2);
	}

	$select = mysql_fetch_array($select);

	if ($select['id_owner'] == $CurrentUser['id'])
	{
		$YourPlanet = true;
		$UsedPlanet = true;
	}
	elseif (!empty($select['id_owner']))
	{
		$YourPlanet = false;
		$UsedPlanet = true;
	}
	else
	{
		$YourPlanet = false;
		$UsedPlanet = false;
	}

	//fix by jstar
	if($fleetmission == 9)
	{
		$countfleettype = count ( $fleetarray );

		if($YourPlanet or !$UsedPlanet or $planettype != 3)
		{
			exit(header("location:game." . $phpEx . "?page=fleet"));
		}
		elseif($countfleettype==1 && !(isset($fleetarray[214]) or isset($fleetarray[216])))
		{
			exit(header("location:game." . $phpEx . "?page=fleet"));
		}
		elseif($countfleettype==2 && !(isset($fleetarray[214]) && isset($fleetarray[216])))
		{
			exit(header("location:game." . $phpEx . "?page=fleet"));
		}
		elseif($countfleettype>2)
		{
			exit(header("location:game." . $phpEx . "?page=fleet"));
		}
	if (empty($fleetmission))
		exit(header("location:game." . $phpEx . "?page=fleet"));

	if ($TargetPlanet['id_owner'] == '')
		$HeDBRec = $MyDBRec;
	elseif ($TargetPlanet['id_owner'] != '')
		$HeDBRec = doquery("SELECT `id`,`onlinetime`,`ally_id`,`urlaubs_modus` FROM {{table}} WHERE `id` = '". intval($TargetPlanet['id_owner']) ."';", 'users', true);

	$UserPoints    = doquery("SELECT `total_points` FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". intval($MyDBRec['id']) ."';", 'statpoints', true);
	$User2Points   = doquery("SELECT `total_points` FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". intval($HeDBRec['id']) ."';", 'statpoints', true);

	$MyGameLevel  = $UserPoints['total_points'];
	$HeGameLevel  = $User2Points['total_points'];

	if($HeDBRec['onlinetime'] >= (time()-60 * 60 * 24 * 7))
	{
		if ($MyGameLevel > ($HeGameLevel * $protectionmulti)
			&& $TargetPlanet['id_owner'] != ''
			&& ($_POST['mission'] == 1 or $_POST['mission'] == 6 or $_POST['mission'] == 9)
			&& $protection == 1
			&& $HeGameLevel < $protectiontime )
			message("<font color=\"lime\"><b>".$lang['fl_week_player']."</b></font>", "game." . $phpEx . "?page=fleet", 2);

		if (($MyGameLevel * $protectionmulti) < $HeGameLevel
			&& $TargetPlanet['id_owner'] != ''
			&& ($_POST['mission'] == 1 or $_POST['mission'] == 5 or $_POST['mission'] == 6 or $_POST['mission'] == 9)
			&& $protection == 1
			&& $MyGameLevel < $protectiontime )
			message("<font color=\"red\"><b>".$lang['fl_strong_player']."</b></font>", "game." . $phpEx . "?page=fleet", 2);
	}

	if ($HeDBRec['urlaubs_modus'] && $_POST['mission'] != 8)
		message("<font color=\"lime\"><b>".$lang['fl_in_vacation_player']."</b></font>", "game." . $phpEx . "?page=fleet", 2);

	$FlyingFleets = mysql_fetch_assoc(doquery("SELECT COUNT(fleet_id) as Number FROM {{table}} WHERE `fleet_owner`='".intval($CurrentUser['id'])."'", 'fleets'));
	$ActualFleets = $FlyingFleets["Number"];

	if ((1 + $CurrentUser[$resource[108]]) + ($CurrentUser['rpg_commandant'] * COMMANDANT) <= $ActualFleets)
	{
		message($lang['fl_no_slots'], "game." . $phpEx . "?page=fleet", 1);
	}

	if ($_POST['resource1'] + $_POST['resource2'] + $_POST['resource3'] < 1 && $_POST['mission'] == 3)
		message("<font color=\"lime\"><b>".$lang['fl_empty_transport']."</b></font>", "game." . $phpEx . "?page=fleet", 1);

	if ($_POST['mission'] != 15)
	{
		if ($TargetPlanet['id_owner'] == '' && $_POST['mission'] < 7)
			exit(header("location:game." . $phpEx . "?page=fleet"));

		if ($TargetPlanet['id_owner'] != '' && $_POST['mission'] == 7)
			message ("<font color=\"red\"><b>".$lang['fl_planet_populed']."</b></font>", "game." . $phpEx . "?page=fleet", 2);

		if ($HeDBRec['ally_id'] != $MyDBRec['ally_id'] && $_POST['mission'] == 4)
			message ("<font color=\"red\"><b>".$lang['fl_stay_not_on_enemy']."</b></font>", "game." . $phpEx . "?page=fleet", 2);

		if (($TargetPlanet["id_owner"] == $CurrentPlanet["id_owner"]) && (($_POST["mission"] == 1) or ($_POST["mission"] == 6)))
			exit(header("location:game." . $phpEx . "?page=fleet"));

		if (($TargetPlanet["id_owner"] != $CurrentPlanet["id_owner"]) && ($_POST["mission"] == 4))
			message ("<font color=\"red\"><b>".$lang['fl_deploy_only_your_planets']."</b></font>","game." . $phpEx . "?page=fleet", 2);
	   if($_POST['mission'] == 5)
		{
			$buddy = doquery("SELECT count(*) FROM {{table}} WHERE `owner` = '". intval($TargetPlanet['id_owner']) ."' OR `sender`='".intval($TargetPlanet['id_owner'])."' AND `active` = '1';", 'buddy');

			if ($_POST['planettype']==3)
			{
				$x = doquery("SELECT `ally_deposit` FROM {{table}} WHERE `universe` = '". intval($_POST['universe']) ."' AND `galaxy` = '". intval($_POST['galaxy']) ."' AND `system` = '". intval($_POST['system']) ."' AND `planet` = '". intval($_POST['planet']) ."' AND `planet_type` = 1;", 'planets', true);
			}
			else
			{
				$x = $TargetPlanet;
			}

			if (($HeDBRec['ally_id'] != $MyDBRec['ally_id'] && $buddy<1) ||  $x['ally_deposit'] < 1)
			{
				message ("<font color=\"red\"><b>".$lang['fl_stay_not_on_enemy']."</b></font>", "game." . $phpEx . "?page=fleet", 2);
			}

		}
   }

	$missiontype = array(
		1 => $lang['type_mission'][1],
		2 => $lang['type_mission'][2],
		3 => $lang['type_mission'][3],
		4 => $lang['type_mission'][4],
		5 => $lang['type_mission'][5],
		6 => $lang['type_mission'][6],
		7 => $lang['type_mission'][7],
		8 => $lang['type_mission'][8],
		9 => $lang['type_mission'][9],
		15 => $lang['type_mission'][15],
		);

	$speed_possible = array(10, 9, 8, 7, 6, 5, 4, 3, 2, 1);
	$AllFleetSpeed  = GetFleetMaxSpeed ($fleetarray, 0, $CurrentUser);
	$GenFleetSpeed  = $_POST['speed'];
	$SpeedFactor    = $game_config['fleet_speed'] / 2500;
	$MaxFleetSpeed  = min($AllFleetSpeed);

	if (!in_array($GenFleetSpeed, $speed_possible))
		exit(header("location:game." . $phpEx . "?page=fleet"));

	if ($MaxFleetSpeed != $_POST['speedallsmin'])
		exit(header("location:game." . $phpEx . "?page=fleet"));

	if (!$_POST['planettype'])
		exit(header("location:game." . $phpEx . "?page=fleet"));

  //start mod	
	if (
  !$universe || 
  !is_numeric($universe) || 
  $universe > MAX_UNIVERSE_IN_WORLD || 
  $universe < 1
  )
		exit(header("location:game." . $phpEx . "?page=fleet"));	
  //end mod

	if (!$_POST['galaxy'] || !is_numeric($_POST['galaxy']) || $_POST['galaxy'] > MAX_GALAXY_IN_WORLD || $_POST['galaxy'] < 1)
		exit(header("location:game." . $phpEx . "?page=fleet"));

	if (!$_POST['system'] || !is_numeric($_POST['system']) || $_POST['system'] > MAX_SYSTEM_IN_GALAXY || $_POST['system'] < 1)
		exit(header("location:game." . $phpEx . "?page=fleet"));

	if (!$_POST['planet'] || !is_numeric($_POST['planet']) || $_POST['planet'] > (MAX_PLANET_IN_SYSTEM + 1) || $_POST['planet'] < 1)
		exit(header("location:game." . $phpEx . "?page=fleet"));
   //start mod
	if($in_war){
   if (
	  $_POST['thisuniverse'] != $CurrentPlanet['universe'] |
    $_POST['thisgalaxy'] != $CurrentPlanet['galaxy'] |
		$_POST['thissystem'] != $CurrentPlanet['system'] |
		$_POST['thisplanet'] != $CurrentPlanet['planet'] |
		$_POST['thisplanettype'] != $CurrentPlanet['planet_type']){
		exit(header("location:game." . $phpEx . "?page=fleet"));}
  }
  else if (
    $_POST['thisgalaxy'] != $CurrentPlanet['galaxy'] |
		$_POST['thissystem'] != $CurrentPlanet['system'] |
		$_POST['thisplanet'] != $CurrentPlanet['planet'] |
		$_POST['thisplanettype'] != $CurrentPlanet['planet_type'])
		exit(header("location:game." . $phpEx . "?page=fleet"));
		 //end mod
		

	if (!isset($fleetarray))
		exit(header("location:game." . $phpEx . "?page=fleet"));
  //start mod
	$distance      = GetTargetDistance($universe, $_POST['universe'], $_POST['thisgalaxy'], $_POST['galaxy'], $_POST['thissystem'], $_POST['system'], $_POST['thisplanet'], $_POST['planet']);
	//end mod
  $duration      = GetMissionDuration($GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor);
	$consumption   = GetFleetConsumption($fleetarray, $SpeedFactor, $duration, $distance, $MaxFleetSpeed, $CurrentUser);

	$fleet['start_time'] = $duration + time();
	// START CODE BY JSTAR
	if ($_POST['mission'] == 15)
	{
		$StayDuration	= floor($_POST['expeditiontime']);

		if ( $StayDuration <= floor ( sqrt ( $CurrentUser['expedition_tech'] ) ) && $StayDuration > 0 )
		{
			$StayDuration    = $StayDuration  * 3600;
			$StayTime        = $fleet['start_time'] + $StayDuration;
		}
		else
		{
			exit(header("location:game." . $phpEx . "?page=fleet"));

		}
	} // END CODE BY JSTAR
	elseif ($_POST['mission'] == 5)
	{
		$StayDuration    = $_POST['holdingtime'] * 3600;
		$StayTime        = $fleet['start_time'] + $_POST['holdingtime'] * 3600;
	}
	else
	{
		$StayDuration    = 0;
		$StayTime        = 0;
	}

	$fleet['end_time']   = $StayDuration + (2 * $duration) + time();
	$FleetStorage        = 0;
	$FleetShipCount      = 0;
	$fleet_array         = "";
	$FleetSubQRY         = "";

	foreach ($fleetarray as $Ship => $Count)
	{
		$FleetStorage    += $pricelist[$Ship]["capacity"] * $Count;
		$FleetShipCount  += $Count;
		$fleet_array     .= $Ship .",". $Count .";";
		$FleetSubQRY     .= "`".$resource[$Ship] . "` = `" . $resource[$Ship] . "` - " . $Count . ", ";
	}

	$FleetStorage        -= $consumption;
	$StorageNeeded        = 0;

	$_POST['resource1'] = max(0, (int)trim($_POST['resource1']));
	$_POST['resource2'] = max(0, (int)trim($_POST['resource2']));
	$_POST['resource3'] = max(0, (int)trim($_POST['resource3']));

	if ($_POST['resource1'] < 1)
		$TransMetal      = 0;
	else
	{
		$TransMetal      = $_POST['resource1'];
		$StorageNeeded  += $TransMetal;
	}

	if ($_POST['resource2'] < 1)
		$TransCrystal    = 0;
	else
	{
		$TransCrystal    = $_POST['resource2'];
		$StorageNeeded  += $TransCrystal;
	}
	if ($_POST['resource3'] < 1)
		$TransDeuterium  = 0;
	else
	{
		$TransDeuterium  = $_POST['resource3'];
		$StorageNeeded  += $TransDeuterium;
	}

	$StockMetal      = $CurrentPlanet['metal'];
	$StockCrystal    = $CurrentPlanet['crystal'];
	$StockDeuterium  = $CurrentPlanet['deuterium'];
	$StockDeuterium -= $consumption;

	$StockOk         = false;
	if ($StockMetal >= $TransMetal)
		if ($StockCrystal >= $TransCrystal)
			if ($StockDeuterium >= $TransDeuterium)
				$StockOk         = true;
	if (!$StockOk)
		message ("<font color=\"red\"><b>". $lang['fl_no_enought_deuterium'] . pretty_number($consumption) ."</b></font>", "game." . $phpEx . "?page=fleet", 2);

	if ( $StorageNeeded > $FleetStorage)
		message ("<font color=\"red\"><b>". $lang['fl_no_enought_cargo_capacity'] . pretty_number($StorageNeeded - $FleetStorage) ."</b></font>", "game." . $phpEx . "?page=fleet", 2);

	if ($TargetPlanet['id_level'] > $CurrentUser['authlevel'] && $game_config['adm_attack'] == 0)
		message($lang['fl_admins_cannot_be_attacked'], "game." . $phpEx . "?page=fleet",2);

	if ($fleet_group_mr != 0)
	{
		$AksStartTime = doquery("SELECT MAX(`fleet_start_time`) AS Start FROM {{table}} WHERE `fleet_group` = '". $fleet_group_mr . "';", "fleets", true);

		if ($AksStartTime['Start'] >= $fleet['start_time'])
		{
			$fleet['end_time']        += $AksStartTime['Start'] -  $fleet['start_time'];
			$fleet['start_time']     = $AksStartTime['Start'];
		}
		else
		{
			$QryUpdateFleets = "UPDATE {{table}} SET ";
			$QryUpdateFleets .= "`fleet_start_time` = '". $fleet['start_time'] ."', ";
			$QryUpdateFleets .= "`fleet_end_time` = fleet_end_time + '".($fleet['start_time'] - $AksStartTime['Start'])."' ";
			$QryUpdateFleets .= "WHERE ";
			$QryUpdateFleets .= "`fleet_group` = '". $fleet_group_mr ."';";
			doquery($QryUpdateFleets, 'fleets');
			$fleet['end_time']         += $fleet['start_time'] -  $AksStartTime['Start'];
		}
	}

	$QryInsertFleet  = "INSERT INTO {{table}} SET ";
	$QryInsertFleet .= "`fleet_owner` = '". intval($CurrentUser['id']) ."', ";
	$QryInsertFleet .= "`fleet_mission` = '".intval($_POST['mission'])."',  ";
	$QryInsertFleet .= "`fleet_amount` = '". intval($FleetShipCount) ."', ";
	$QryInsertFleet .= "`fleet_array` = '". $fleet_array ."', ";
	$QryInsertFleet .= "`fleet_start_time` = '". $fleet['start_time'] ."', ";
	//start mod
	 $QryInsertFleet .= "`fleet_start_universe` = '". $thisUniverse ."', ";
	//end mod
	$QryInsertFleet .= "`fleet_start_galaxy` = '". intval($_POST['thisgalaxy']) ."', ";
	$QryInsertFleet .= "`fleet_start_system` = '". intval($_POST['thissystem']) ."', ";
	$QryInsertFleet .= "`fleet_start_planet` = '". intval($_POST['thisplanet']) ."', ";
	$QryInsertFleet .= "`fleet_start_type` = '". intval($_POST['thisplanettype']) ."', ";
	$QryInsertFleet .= "`fleet_end_time` = '". intval($fleet['end_time']) ."', ";
	$QryInsertFleet .= "`fleet_end_stay` = '". intval($StayTime) ."', ";
	//start mod
	$QryInsertFleet .= "`fleet_end_universe` = '". $universe ."', ";
	//end mod
	$QryInsertFleet .= "`fleet_end_galaxy` = '". intval($_POST['galaxy']) ."', ";
	$QryInsertFleet .= "`fleet_end_system` = '". intval($_POST['system']) ."', ";
	$QryInsertFleet .= "`fleet_end_planet` = '". intval($_POST['planet']) ."', ";
	$QryInsertFleet .= "`fleet_end_type` = '". intval($_POST['planettype']) ."', ";
	$QryInsertFleet .= "`fleet_resource_metal` = '". $TransMetal ."', ";
	$QryInsertFleet .= "`fleet_resource_crystal` = '". $TransCrystal ."', ";
	$QryInsertFleet .= "`fleet_resource_deuterium` = '". $TransDeuterium ."', ";
	$QryInsertFleet .= "`fleet_target_owner` = '". intval($TargetPlanet['id_owner']) ."', ";
	$QryInsertFleet .= "`fleet_group` = '".intval($fleet_group_mr)."',  ";
	$QryInsertFleet .= "`start_time` = '". time() ."';";
	doquery( $QryInsertFleet, 'fleets');

	$QryUpdatePlanet  = "UPDATE `{{table}}` SET ";
	$QryUpdatePlanet .= $FleetSubQRY;
	$QryUpdatePlanet .= "`metal` = `metal` - ". $TransMetal .", ";
	$QryUpdatePlanet .= "`crystal` = `crystal` - ". $TransCrystal .", ";
	$QryUpdatePlanet .= "`deuterium` = `deuterium` - ". ($TransDeuterium + $consumption) ." ";
	$QryUpdatePlanet .= "WHERE ";
	$QryUpdatePlanet .= "`id` = ". intval($CurrentPlanet['id']) ." LIMIT 1;";
	doquery ($QryUpdatePlanet, "planets");

	$parse['mission'] 		= $missiontype[$_POST['mission']];
	$parse['distance'] 		= pretty_number($distance);
	$parse['speedallsmin'] 	= pretty_number($_POST['speedallsmin']);
	$parse['consumption'] 	= pretty_number($consumption);
	//start mod
	if($in_war){
    $parse['from']	 		= $_POST['thisuniverse'] .":".$_POST['thisgalaxy'] .":". $_POST['thissystem']. ":". $_POST['thisplanet'];
	  $parse['destination']	= $_POST['universe'] .":".$_POST['galaxy'] .":". $_POST['system'] .":". $_POST['planet'];
  }
  else{
	  $parse['from']	 		= $_POST['thisgalaxy'] .":". $_POST['thissystem']. ":". $_POST['thisplanet'];
    $parse['destination']	= $universe .":". $_POST['system'] .":". $_POST['planet'];
  }
  
  //end mod
	$parse['start_time'] 	= date("M D d H:i:s", $fleet['start_time']);
	$parse['end_time'] 		= date("M D d H:i:s", $fleet['end_time']);

	foreach ($fleetarray as $Ship => $Count)
	{
		$fleet_list .= "</tr><tr height=\"20\">";
		$fleet_list .= "<th>". $lang['tech'][$Ship] ."</th>";
		$fleet_list .= "<th>". pretty_number($Count) ."</th>";
	}

	$parse['fleet_list'] 	= $fleet_list;

   if($in_war){
    display(parsetemplate(gettemplate('fleet/fleet3_table_extended'), $parse),false);
  } 
  else{
	  display(parsetemplate(gettemplate('fleet/fleet3_table'), $parse),false);
  }
}
?>