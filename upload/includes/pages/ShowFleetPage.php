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

function ShowFleetPage($CurrentUser, $CurrentPlanet)
{
	global $lang, $reslist, $resource;

	$parse				= $lang;

	$maxfleet  			= doquery("SELECT COUNT(fleet_owner) AS `actcnt` FROM {{table}} WHERE `fleet_owner` = '".intval($CurrentUser['id'])."';", 'fleets', true);
	$MaxFlyingFleets    = $maxfleet['actcnt'];
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

	$MaxFlottes         = (1 + $CurrentUser[$resource[108]]) + ($CurrentUser['rpg_commandant'] * COMMANDANT);

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
		15 => $lang['type_mission'][15]
	);
  //start mod
  $universe         = intval($_GET['universe']);
  $thisUniverse     =$CurrentPlanet['universe'];
  //end mod
	$galaxy         = intval($_GET['galaxy']);
	$system         = intval($_GET['system']);
	$planet         = intval($_GET['planet']);
	$planettype     = intval($_GET['planettype']);
	$target_mission = intval($_GET['target_mission']);
	$ShipData       = "";
   //start mod universe==0
  if (!$universe)
		$universe = $thisUniverse;
		//end mod
	if (!$galaxy)
		$galaxy = $CurrentPlanet['galaxy'];
	if (!$system)
		$system = $CurrentPlanet['system'];
	if (!$planet)
		$planet = $CurrentPlanet['planet'];
	if (!$planettype)
		$planettype = $CurrentPlanet['planet_type'];

	$parse['flyingfleets']			= $MaxFlyingFleets;
	$parse['maxfleets']				= $MaxFlottes;
	$parse['currentexpeditions']	= $ExpeditionEnCours;
	$parse['maxexpeditions']		= $EnvoiMaxExpedition;

	$fq = doquery("SELECT * FROM {{table}} WHERE fleet_owner='".intval($CurrentUser[id])."' AND fleet_mission <> 10", "fleets");
	$i  = 0;

	while ($f = mysql_fetch_array($fq))
	{
		$i++;
		$FleetPageRow .= "<tr height=20>";
		$FleetPageRow .= "<th>".$i."</th>";
		$FleetPageRow .= "<th>";
		$FleetPageRow .= "<a>". $missiontype[$f[fleet_mission]] ."</a>";
		if (($f['fleet_start_time'] + 1) == $f['fleet_end_time'])
			$FleetPageRow .= "<br><a title=\"".$lang['fl_returning']."\">".$lang['fl_r']."</a>";
		else
			$FleetPageRow .= "<br><a title=\"".$lang['fl_onway']."\">".$lang['fl_a']."</a>";
		$FleetPageRow .= "</th>";
		$FleetPageRow .= "<th><a title=\"";

		$fleet = explode(";", $f['fleet_array']);
		$e = 0;
		foreach ($fleet as $a => $b)
		{
			if ($b != '')
			{
				$e++;
				$a = explode(",", $b);
				$FleetPageRow .= $lang['tech'][$a[0]]. ":". $a[1] ."\n";
				if ($e > 1)
				{
					$FleetPageRow .= "\t";
				}
			}
		}
		$FleetPageRow .= "\">". pretty_number($f[fleet_amount]) ."</a></th>";
		$FleetPageRow .= "<th>[".$f[fleet_start_galaxy].":".$f[fleet_start_system].":".$f[fleet_start_planet]."]</th>";
		$FleetPageRow .= "<th>". gmdate("d M Y H:i:s", $f['fleet_start_time']) ."</th>";
	 //start mod
	   if($universe == $thisUniverse)
	      $FleetPageRow .= "<th>[".$f[fleet_end_galaxy].":".$f[fleet_end_system].":".$f[fleet_end_planet]."]</th>";
     else
  	   $FleetPageRow .= "<th>[".$f[fleet_end_universe].":".$f[fleet_end_galaxy].":".$f[fleet_end_system].":".$f[fleet_end_planet]."]</th>";
		//end mod
    $FleetPageRow .= "<th>". gmdate("d M Y H:i:s", $f['fleet_end_time']) ."</th>";
		$FleetPageRow .= "<th><font color=\"lime\"><div id=\"time_0\"><font>". pretty_time(floor($f['fleet_end_time'] + 1 - time())) ."</font></th>";
		$FleetPageRow .= "<th>";

		//now we can view the call back button for ships in maintaing position (2)
		if ($f['fleet_mess'] == 0 or $f['fleet_mess'] == 2) 
		{
				$FleetPageRow .= "<form action=\"SendFleetBack.php\" method=\"post\">";
				$FleetPageRow .= "<input name=\"fleetid\" value=\"". $f['fleet_id'] ."\" type=\"hidden\">";
				$FleetPageRow .= "<input value=\"".$lang['fl_send_back']."\" type=\"submit\" name=\"send\">";
				$FleetPageRow .= "</form>";

			if ($f[fleet_mission] == 1)
			{
				$FleetPageRow .= "<form action=\"game.php?page=fleetACS\" method=\"post\">";
				$FleetPageRow .= "<input name=\"fleetid\" value=\"". $f['fleet_id'] ."\" type=\"hidden\">";
				$FleetPageRow .= "<input value=\"".$lang['fl_acs']."\" type=\"submit\">";
				$FleetPageRow .= "</form>";
			}
		}
		else
			$FleetPageRow .= "&nbsp;-&nbsp;";

		$FleetPageRow .= "</th>";
		$FleetPageRow .= "</tr>";
	}

	if ($i == 0)
	{
		$FleetPageRow .= "<tr>";
		$FleetPageRow .= "<th>-</th>";
		$FleetPageRow .= "<th>-</th>";
		$FleetPageRow .= "<th>-</th>";
		$FleetPageRow .= "<th>-</th>";
		$FleetPageRow .= "<th>-</th>";
		$FleetPageRow .= "<th>-</th>";
		$FleetPageRow .= "<th>-</th>";
		$FleetPageRow .= "<th>-</th>";
		$FleetPageRow .= "<th>-</th>";
		$FleetPageRow .= "</tr>";
	}

	$parse['fleetpagerow'] = $FleetPageRow;

	if ($MaxFlottes == $MaxFlyingFleets)
		$parse['message_nofreeslot'] .= "<tr height=\"20\"><th colspan=\"9\"><font color=\"red\">".$lang['fl_no_more_slots']."</font></th></tr>";

	if (!$CurrentPlanet)
		header("location:game.php?page=fleet");

	foreach ($reslist['fleet'] as $n => $i)
	{
		if ($CurrentPlanet[$resource[$i]] > 0)
		{
			$page .= "<tr height=\"20\">";
			$page .= "<th>";
			$page .= ($i == 212)? "": "<a title=\"" . $lang['fl_speed_title'] . (GetFleetMaxSpeed ( "", $i, $CurrentUser )) ."\">";
			$page .= $lang['tech'][$i] . "</a></th>";
			$page .= "<th>". pretty_number ($CurrentPlanet[$resource[$i]]);
			$ShipData .= "<input type=\"hidden\" name=\"maxship". $i ."\" value=\"". $CurrentPlanet[$resource[$i]] ."\" />";
			$ShipData .= "<input type=\"hidden\" name=\"consumption". $i ."\" value=\"". GetShipConsumption ( $i, $CurrentUser ) ."\" />";
			$ShipData .= "<input type=\"hidden\" name=\"speed" .$i ."\" value=\"" . GetFleetMaxSpeed ("", $i, $CurrentUser) . "\" />";
			$ShipData .= "<input type=\"hidden\" name=\"capacity". $i ."\" value=\"". $pricelist[$i]['capacity'] ."\" />";
			$page .= "</th>";

			if ($i == 212)
				$page .= "<th></th><th></th>";
			else
			{
				$page .= "<th><a href=\"javascript:maxShip('ship". $i ."'); shortInfo();\">".$lang['fl_max']."</a> </th>";
				$page .= "<th><input name=\"ship". $i ."\" size=\"10\" value=\"0\" onfocus=\"javascript:if(this.value == '0') this.value='';\" onblur=\"javascript:if(this.value == '') this.value='0';\" alt=\"". $lang['tech'][$i] . $CurrentPlanet[$resource[$i]] ."\" onChange=\"shortInfo()\" onKeyUp=\"shortInfo()\" /></th>";
			}
			$page .= "</tr>";
		}
		$have_ships = true;
	}

	$btncontinue = "<tr height=\"20\"><th colspan=\"4\"><input type=\"submit\" value=\"".$lang['fl_continue']."\" /></th>";

	$page .= "<tr height=\"20\">";

	if (!$have_ships)
	{
		$page .= "<th colspan=\"4\">".$lang['fl_no_ships']."</th>";
		$page .= "</tr>";
		$page .= $btncontinue;
	}
	else
	{
		$page .= "<th colspan=\"2\"><a href=\"javascript:noShips();shortInfo();noResources();\" >".$lang['fl_remove_all_ships']."</a></th>";
		$page .= "<th colspan=\"2\"><a href=\"javascript:maxShips();shortInfo();\" >".$lang['fl_select_all_ships']."</a></th>";
		$page .= "</tr>";

		if ($MaxFlottes > $MaxFlyingFleets)
			$page .= $btncontinue;
	}
	$parse['body'] 					= $page;
	$parse['shipdata'] 				= $ShipData;
	//start mod
	$parse['universe']				= $universe;
	//end mod
	$parse['galaxy']				= $galaxy;
	$parse['system']				= $system;
	$parse['planet']				= $planet;
	$parse['planettype']			= $planettype;
	$parse['target_mission']		= $target_mission;
	$parse['envoimaxexpedition']	= $EnvoiMaxExpedition;
	$parse['expeditionencours']		= $ExpeditionEnCours;
	$parse['target_mission']		= $target_mission;

	
	//if(isUniverseInWar($CurrentPlanet['universe'],intval($_POST['universe']))){
    display(parsetemplate(gettemplate('fleet/fleet_table'), $parse));
 // } 
  //else{
	//  display(parsetemplate(gettemplate('fleet/fleet_table'), $parse));
//  }
}
?>