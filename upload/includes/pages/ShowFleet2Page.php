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

function ShowFleet2Page($CurrentUser, $CurrentPlanet)
{
	global $resource, $pricelist, $reslist, $phpEx, $lang;

	$parse			= $lang;
	//start mod
	$universe     	= intval($_POST['universe']);
	$in_war=isUniverseInWar($universe,$CurrentPlanet['universe'])==1;
	//end mod
	$galaxy     	= intval($_POST['galaxy']);
	$system     	= intval($_POST['system']);
	$planet     	= intval($_POST['planet']);
	$planettype 	= intval($_POST['planettype']);
	$fleet_group_mr = intval($_POST['fleet_group']);

	$YourPlanet 	= false;
	$UsedPlanet 	= false;
	//start mod
	if($universe==0){
    $universe=$CurrentPlanet['universe'];
  } 
	if(!$in_war && $universe!= $CurrentPlanet['universe'])
  {
		header("location:game.php?page=fleet");
	}
	
	$select       	= doquery("SELECT `id_owner`,`universe`,`galaxy`,`system`,`planet`,`planet_type` FROM `{{table}}`", "planets");
  //end mod
	while ($row = mysql_fetch_array($select))
	{
		if (  //start mod
		    $universe == $row['universe'] && 
		    //end mod
        $galaxy == $row['galaxy'] && 
        $system == $row['system'] && 
        $planet == $row['planet'] && 
        $planettype == $row['planet_type']
        )
		{
			if ($row['id_owner'] == $CurrentUser['id'])
			{
				$YourPlanet = true;
				$UsedPlanet = true;
			}
			else
				$UsedPlanet = true;

			break;
		}
	}

	if ($_POST['planettype'] == 2)
	{
		if ($_POST['ship209'] >= 1)
			$missiontype = array(8 => $lang['type_mission'][8]);
		else
			$missiontype = array();
	}
	elseif ($_POST['planettype'] == 1 or $_POST['planettype'] == 3)
	{
		if ($_POST['ship208'] >= 1 && !$UsedPlanet)
			$missiontype = array(7 => $lang['type_mission'][7]);
		elseif ($_POST['ship210'] >= 1 && !$YourPlanet)
			$missiontype = array(6 => $lang['type_mission'][6]);

		if ($_POST['ship202'] >= 1 ||
			$_POST['ship203'] >= 1 ||
			$_POST['ship204'] >= 1 ||
			$_POST['ship205'] >= 1 ||
			$_POST['ship206'] >= 1 ||
			$_POST['ship207'] >= 1 ||
			$_POST['ship210'] >= 1 ||
			$_POST['ship211'] >= 1 ||
			$_POST['ship213'] >= 1 ||
			$_POST['ship214'] >= 1 ||
			$_POST['ship215'] >= 1 ||
			$_POST['ship216'] >= 1 ||
			$_POST['ship217'] >= 1 ||
			$_POST['ship218'] >= 1 ||
			$_POST['ship219'] >= 1 ||
			$_POST['ship220'] >= 1 ||
			$_POST['ship221'] >= 1 ||
			$_POST['ship222'] >= 1 ||
			$_POST['ship223'] >= 1 ||
			$_POST['ship224'] >= 1 ||
			$_POST['ship225'] >= 1 ||
		$_POST['ship226'] >= 1) {

			if (!$YourPlanet)
				$missiontype[1] = $lang['type_mission'][1];

			$missiontype[3] = $lang['type_mission'][3];
			$missiontype[5] = $lang['type_mission'][5];
		}
	}
	elseif ($_POST['ship209'] >= 1 || $_POST['ship208'])
		$missiontype[3] = $lang['type_mission'][3];

	if ($YourPlanet)
		$missiontype[4] = $lang['type_mission'][4];

	if (($_POST['planettype'] == 3 || $_POST['planettype'] == 1) && ($fleet_group_mr > 0) && ($UsedPlanet))
	{
		$missiontype[2] = $lang['type_mission'][2];
	}

	if($_POST['planettype'] == 3 && $_POST['ship214'] >= 1 && !$YourPlanet && $UsedPlanet && $CurrentUser['rpg_empereur'] == 1)
		$missiontype[9] = $lang['type_mission'][9];

	$fleetarray    		= unserialize(base64_decode(str_rot13($_POST["usedfleet"])));
	$mission       		= $_POST['target_mission'];
	$SpeedFactor   		= $_POST['speedfactor'];
	$AllFleetSpeed 		= GetFleetMaxSpeed ($fleetarray, 0, $CurrentUser);
	$GenFleetSpeed 		= $_POST['speed'];
	$MaxFleetSpeed 		= min($AllFleetSpeed);
	//start mod
	$distance      		= GetTargetDistance($CurrentPlanet['universe'], $universe, $_POST['thisgalaxy'], $_POST['galaxy'], $_POST['thissystem'], $_POST['system'], $_POST['thisplanet'], $_POST['planet']);
	//end mod
  $duration      		= GetMissionDuration($GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor);
	$consumption   		= GetFleetConsumption($fleetarray, $SpeedFactor, $duration, $distance, $MaxFleetSpeed, $CurrentUser);
	$MissionSelector	= "";

	if (count($missiontype) > 0)
	{
		if ($planet == 16)
		{
			$MissionSelector .= "<tr height=\"20\">";
			$MissionSelector .= "<th>";
			$MissionSelector .= "<input type=\"radio\" name=\"mission\" value=\"15\" checked=\"checked\">". $lang['type_mission'][15] ."<br /><br />";
			$MissionSelector .= "<font color=\"red\">".$lang['fl_expedition_alert_message']."</font>";
			$MissionSelector .= "</th>";
			$MissionSelector .= "</tr>";
		}
		else
		{
			$i = 0;
			foreach ($missiontype as $a => $b)
			{
				$MissionSelector .= "<tr height=\"20\">";
				$MissionSelector .= "<th>";
				$MissionSelector .= "<input id=\"inpuT_".$i."\" type=\"radio\" name=\"mission\" value=\"".$a."\"". ($mission == $a ? " checked=\"checked\"":"") .">";
				$MissionSelector .= "<label for=\"inpuT_".$i."\">".$b."</label><br>";
				$MissionSelector .= "</th>";
				$MissionSelector .= "</tr>";
				$i++;
			}
		}
	}
	else
	{
		header("location:game.php?page=fleet");
	}
   //start mod
	if($_POST['thisplanettype'] == 1){
	 if($in_war)
		 $parse['title'] = "". $_POST['thisuniverse'] .":". $_POST['thisgalaxy'] .":". $_POST['thissystem'] .":". $_POST['thisplanet'] ." - ".$lang['fl_planet']."";
   else
      $parse['title'] = "". $_POST['thisgalaxy'] .":". $_POST['thissystem'] .":". $_POST['thisplanet'] ." - ".$lang['fl_planet']."";
	}
  else if ($_POST['thisplanettype'] == 3)
		if($in_war)
		  $parse['title'] = "". $_POST['thisuniverse'] .":". $_POST['thisgalaxy'] .":". $_POST['thissystem'] .":". $_POST['thisplanet'] ." - ".$lang['fl_moon']."";
    else
      $parse['title'] = "". $_POST['thisgalaxy'] .":". $_POST['thissystem'] .":". $_POST['thisplanet'] ." - ".$lang['fl_moon']."";
   //end mod
	$parse['metal'] 			= floor($CurrentPlanet["metal"]);
	$parse['crystal'] 			= floor($CurrentPlanet["crystal"]);
	$parse['deuterium'] 		= floor($CurrentPlanet["deuterium"]);
	$parse['consumption'] 		= $consumption;
	$parse['distance']			= $distance;
	$parse['speedfactor'] 		= $_POST['speedfactor'];
	//start mod
	$parse['thisuniverse'] 		= $CurrentPlanet['universe'];
	//end mod
	$parse['thisgalaxy'] 		= $_POST["thisgalaxy"];
	$parse['thissystem'] 		= $_POST["thissystem"];
	$parse['thisplanet'] 		= $_POST["thisplanet"];
	//start mod
	$parse['universe'] 			= $universe;
	//end mod
	$parse['galaxy'] 			= $_POST["galaxy"];
	$parse['system'] 			= $_POST["system"];
	$parse['planet'] 			= $_POST["planet"];
	$parse['thisplanettype']	= $_POST["thisplanettype"];
	$parse['planettype'] 		= $_POST["planettype"];
	$parse['speedallsmin'] 		= $_POST["speedallsmin"];
	$parse['speed'] 			= $_POST['speed'];
	$parse['speedfactor'] 		= $_POST["speedfactor"];
	$parse['usedfleet'] 		= $_POST["usedfleet"];
	$parse['maxepedition'] 		= $_POST['maxepedition'];
	$parse['curepedition'] 		= $_POST['curepedition'];
	$parse['fleet_group'] 		= $_POST['fleet_group'];
	$parse['acs_target_mr'] 	= $_POST['acs_target_mr'];

	foreach ($fleetarray as $Ship => $Count)
	{
		$input_extra .= "<input type=\"hidden\" name=\"ship". $Ship ."\"        value=\"". $Count ."\" />\n";
		$input_extra .= "<input type=\"hidden\" name=\"capacity". $Ship ."\"    value=\"". $pricelist[$Ship]['capacity'] ."\" />\n";
		$input_extra .= "<input type=\"hidden\" name=\"consumption". $Ship ."\" value=\"". GetShipConsumption ( $Ship, $CurrentUser ) ."\" />\n";
		$input_extra .= "<input type=\"hidden\" name=\"speed". $Ship ."\"       value=\"". GetFleetMaxSpeed ( "", $Ship, $CurrentUser ) ."\" />\n";
	}

	$parse['input_extra'] 			= $input_extra;
	$parse['missionselector'] 		= $MissionSelector;

	if ($planet == 16)
	{
		$StayBlock .= "<tr height=\"20\">";
		$StayBlock .= "<td class=\"c\" colspan=\"3\">".$lang['fl_hold_time']."</td>";
		$StayBlock .= "</tr>";
		$StayBlock .= "<tr height=\"20\">";
		$StayBlock .= "<th colspan=\"3\">";
		$StayBlock .= "<select name=\"expeditiontime\" >";

		$max = floor ( sqrt ( $CurrentUser['expedition_tech'] ) );

		for ( $i = 1 ; $i <= $max && $i < 10 ; $i++ )
		{
			$StayBlock .= "<option value='".$i."'>". $i."</option>";
		}

		$StayBlock .= "</select>";
		$StayBlock .= $lang['fl_hours'];
		$StayBlock .= "</th>";
		$StayBlock .= "</tr>";
	}
	elseif($missiontype[5] != '')
	{
		$StayBlock .= "<tr height=\"20\">";
		$StayBlock .= "<td class=\"c\" colspan=\"3\">".$lang['fl_hold_time']."</td>";
		$StayBlock .= "</tr>";
		$StayBlock .= "<tr height=\"20\">";
		$StayBlock .= "<th colspan=\"3\">";
		$StayBlock .= "<select name=\"holdingtime\" >";
		$StayBlock .= "<option value=\"0\">0</option>";
		$StayBlock .= "<option value=\"1\">1</option>";
		$StayBlock .= "<option value=\"2\">2</option>";
		$StayBlock .= "<option value=\"4\">4</option>";
		$StayBlock .= "<option value=\"8\">8</option>";
		$StayBlock .= "<option value=\"16\">16</option>";
		$StayBlock .= "<option value=\"32\">32</option>";
		$StayBlock .= "</select>";
		$StayBlock .= "hora(s)";
		$StayBlock .= "</th>";
		$StayBlock .= "</tr>";
	}
	$parse['stayblock'] = $StayBlock;
	display(parsetemplate(gettemplate('fleet/fleet2_table'), $parse));
}
?>