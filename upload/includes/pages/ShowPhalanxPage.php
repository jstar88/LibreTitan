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


function ShowPhalanxPage($CurrentUser, $CurrentPlanet)
{
	global $xgp_root, $phpEx, $lang;

	include_once($xgp_root . 'includes/functions/InsertJavaScriptChronoApplet.' . $phpEx);
	include_once($xgp_root . 'includes/classes/class.FlyingFleetsTable.' . $phpEx);
	include_once($xgp_root . 'includes/classes/class.GalaxyRows.' . $phpEx);

	$FlyingFleetsTable 	= new FlyingFleetsTable();
	$GalaxyRows 		   = new GalaxyRows();

	$parse = $lang;
    /* range */
	$radar_menzil_min = $CurrentPlanet['system'] - Formules::getPhalanxRange ( $CurrentPlanet['phalanx'] );
	$radar_menzil_max = $CurrentPlanet['system'] + Formules::getPhalanxRange ( $CurrentPlanet['phalanx'] );
	$radar_menzil_min=max($radar_menzil_min,1);
   $radar_menzil_max=min($radar_menzil_max,MAX_SYSTEM_IN_GALAXY);
	/* input validation */
	$DoScan=false;
	//start mod
	$Universe     =(isset($_GET['universe']))
  ? (int)$_GET['universe']
  : $CurrentPlanet['universe'];
  $UniverseInWar=isUniverseInWar($Universe,$CurrentPlanet['universe']); 
  //end mod
	$Galaxy  = intval($_GET["galaxy"]);
	$System  = intval($_GET["system"]);
	$Planet  = intval($_GET["planet"]);
	$PlType  = intval($_GET["planettype"]);
	/* cheater detection */
   if ( 
   $System < $radar_menzil_min         ||  
   $System > $radar_menzil_max         || 
   $Galaxy != $CurrentPlanet['galaxy'] ||
   $PlType != 1                        || 
   $CurrentPlanet['planet_type'] != 3
   )
	  die(message('out of range!'));
	 /* main page */
	if ($CurrentPlanet['deuterium'] > 10000)
	{
		doquery ("UPDATE {{table}} SET `deuterium` = `deuterium` - '10000' WHERE `id` = '". $CurrentUser['current_planet'] ."';", 'planets');
		
		$QryTargetInfo   = "SELECT ";
      $QryTargetInfo  .= "`name`, ";
      $QryTargetInfo  .= "`id_owner` ";
      $QryTargetInfo  .= "FROM {{table}} WHERE ";
      $QryTargetInfo  .= "`universe` = '". $Universe ."' AND "; 
      $QryTargetInfo  .= "`galaxy` = '". $Galaxy ."' AND "; 
      $QryTargetInfo  .= "`system` = '". $System ."' AND "; 
      $QryTargetInfo  .= "`planet` = '". $Planet ."' AND "; 
      $QryTargetInfo  .= "`planet_type` = '". $PlType ."' ";
		
      $TargetInfo= doquery($QryTargetInfo,'planets', true);
      $TargetName = $TargetInfo['name'];
		$TargetID   = $TargetInfo['id_owner'];

		$QryLookFleets  = "SELECT * ";
		$QryLookFleets .= "FROM {{table}} ";
		$QryLookFleets .= "WHERE ( ( ";
		$QryLookFleets .= "`fleet_start_universe` = '". $Universe ."' AND ";
		$QryLookFleets .= "`fleet_start_galaxy` = '". $Galaxy ."' AND ";
		$QryLookFleets .= "`fleet_start_system` = '". $System ."' AND ";
		$QryLookFleets .= "`fleet_start_planet` = '". $Planet ."' AND ";
		$QryLookFleets .= "`fleet_start_type` = '". $PlType ."' ";
		$QryLookFleets .= ") OR ( ";
		$QryLookFleets .= "`fleet_end_universe` = '". $Universe ."' AND ";
		$QryLookFleets .= "`fleet_end_galaxy` = '". $Galaxy ."' AND ";
		$QryLookFleets .= "`fleet_end_system` = '". $System ."' AND ";
		$QryLookFleets .= "`fleet_end_planet` = '". $Planet ."' AND ";
		$QryLookFleets .= "`fleet_end_type` = '". $PlType ."' ";
		$QryLookFleets .= ") ) ";
		$QryLookFleets .= "ORDER BY `fleet_start_time`;";

		$FleetToTarget  = doquery( $QryLookFleets, 'fleets' );

      $Record=0;
		$fpage=array();
		while ($FleetRow = mysql_fetch_array($FleetToTarget))
		{
		   $Record++;

		   $ArrivetoTargetTime   = $FleetRow['fleet_start_time'];
			$EndStayTime          = $FleetRow['fleet_end_stay'];
			$ReturnTime           = $FleetRow['fleet_end_time'];
			$Mission              = $FleetRow['fleet_mission'];
         $myFleet =($FleetRow['fleet_owner'] == $TargetID)? true : false;
			$FleetRow['fleet_resource_metal']     = 0;
			$FleetRow['fleet_resource_crystal']   = 0;
			$FleetRow['fleet_resource_deuterium'] = 0;
         $isStartedfromThis= $FleetRow['fleet_start_universe'] == $Universe &&
                             $FleetRow['fleet_start_galaxy'] == $Galaxy && 
                             $FleetRow['fleet_start_system'] == $System && 
                             $FleetRow['fleet_start_planet'] == $Planet;
            
			
         /* 1)the arrive to target fleet table event 
         * you can see start-fleet event only if this is a planet 
         * and if the fleet mission started from this planet is different from hold 
         * or if it's a enemy mission.
         */			
			if ($ArrivetoTargetTime > time())
         {
			   if($isStartedfromThis && $FleetRow['fleet_start_type'] == 1)
            {
			      if($Mission != 4)
               {
                  $Label = "fs";
                  $fpage[$ArrivetoTargetTime] = $FlyingFleetsTable->BuildFleetEventTable ( $FleetRow, 0, $myFleet, $Label, $Record );      
               }
            }
            elseif($FleetRow['fleet_end_type'] == 1)
            {
               $Label = "fs";
               $fpage[$ArrivetoTargetTime] = $FlyingFleetsTable->BuildFleetEventTable ( $FleetRow, 0, $myFleet, $Label, $Record );
            }  
         }
         /* 2)the stay fleet table event 
         * you can see stay-fleet event only if the target is a planet
         */
         if ($EndStayTime > time() && $Mission== 5 && $FleetRow['fleet_end_type'] == 1)
         {
            $Label = "ft";
				$fpage[$EndStayTime] = $FlyingFleetsTable->BuildFleetEventTable ( $FleetRow, 1, $myFleet, $Label, $Record );
         }
         /* 3)the return fleet table event 
         * you can see the return fleet if this is the started planet
         * but no if it is a hold mission or mip         
         */
         if (  $ReturnTime > time() && 
               $Mission != 4        && 
               $Mission != 10       &&
               $isStartedfromThis   &&
               $FleetRow['fleet_end_type'] == 1
            )
         {
				$Label = "fe";
            $fpage[$ReturnTime]  = $FlyingFleetsTable->BuildFleetEventTable ( $FleetRow, 2, $myFleet, $Label, $Record );
			}	       
   	}
		foreach ($fpage as $FleetTime => $FleetContent)
				$Fleets .= $FleetContent ."\n";

		$parse['phl_fleets_table'] = $Fleets;
		$parse['phl_er_deuter'] = "";
	}
	else
	  $parse['phl_er_deuter'] = $lang['px_no_deuterium'];
       
   $parse['phl_pl_universe']    = $CurrentPlanet['universe'];
   $parse['phl_pl_galaxy']    = $CurrentPlanet['galaxy'];
	$parse['phl_pl_system']    = $CurrentPlanet['system'];
	$parse['phl_pl_place']     = $CurrentPlanet['planet'];
	$parse['phl_pl_name']      = $CurrentUser['username'];	

	if ($UniverseInWar)
    return display(parsetemplate(gettemplate('galaxy/phalanx_body_extended'), $parse), false, '', false, false); 
	return display(parsetemplate(gettemplate('galaxy/phalanx_body'), $parse), false, '', false, false);
}
?>
