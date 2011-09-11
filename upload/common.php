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

// SETEADO PARA EVITAR ERRORRES EN VERSION DE PHP MAYORES A 5.3.0
error_reporting(E_ALL & ~E_NOTICE);

if ( isset ( $_GET["xgp_root"] ) or isset ( $_POST["xgp_root"] ) )
{
	die();
}

if(filesize($xgp_root . 'config.php') == 0 && INSTALL != true)
{
	exit ( header ( "location:" . $xgp_root .  "install/" ) );
}

$phpEx			= "php";
$game_config   	= array();
//start mod
$war_config   	= array();
//end mod
$user          	= array();
$lang          	= array();
$link          	= "";
$IsUserChecked 	= false;

include_once($xgp_root . 'includes/constants.'.$phpEx);
include_once($xgp_root . 'includes/GeneralFunctions.'.$phpEx);
include_once($xgp_root . 'includes/vendor/simplehtmldom/simple_html_dom.' . $phpEx);
include_once($xgp_root . 'includes/classes/class.debug.'.$phpEx);
$debug 		= new debug();

if (INSTALL != true)
{
	include($xgp_root . 'includes/vars.'.$phpEx);
	include($xgp_root . 'includes/functions/RoundUp.' . $phpEx);
	include($xgp_root . 'includes/functions/CreateOneMoonRecord.'.$phpEx);
	include($xgp_root . 'includes/functions/CreateOnePlanetRecord.'.$phpEx);
	include($xgp_root . 'includes/functions/SendSimpleMessage.'.$phpEx);
	include($xgp_root . 'includes/functions/calculateAttack.'.$phpEx);
	include($xgp_root . 'includes/functions/formatCR.'.$phpEx);
	include($xgp_root . 'includes/functions/GetBuildingTime.' . $phpEx);
	include($xgp_root . 'includes/functions/HandleElementBuildingQueue.' . $phpEx);
	include($xgp_root . 'includes/functions/PlanetResourceUpdate.' . $phpEx);

	$query = doquery("SELECT * FROM {{table}}",'config');

	while ($row = mysql_fetch_assoc($query))
	{
		$game_config[$row['config_name']] = $row['config_value'];
	}
	
	//start mod
	$query = doquery("SELECT * FROM {{table}}",'universewar');
   $i=0;
	while ($row = mysql_fetch_assoc($query))
	{
	 $one_war['id'] = $row['id'];
	 $one_war['id_universe_in_war1'] = $row['id_universe_in_war1'];  
	 $one_war['id_universe_in_war2'] = $row['id_universe_in_war2'];
	 $one_war['war_start'] = getTimeFromDatabaseDate($row['war_start']);
	 $one_war['war_end']   = getTimeFromDatabaseDate($row['war_end']);
	 $one_war['distance'] = $row['distance'];
	 $one_war['on_time_to_war']=0;
	
   $war_config[$i]=$one_war;	
    $i++;
	}
	
	
	//end mod

	define('DEFAULT_LANG'	, (	$game_config['lang'] 	== ''	) ? "spanish" : 	$game_config['lang']	);
	define('VERSION'		, (	$game_config['VERSION'] == ''	) ? "		" : "v".$game_config['VERSION']	);

	includeLang('INGAME');

	if ($InLogin != true)
	{
		include($xgp_root . 'includes/classes/class.CheckSession.'.$phpEx);

		$Result        	= new CheckSession();
		$Result			= $Result->CheckUser($IsUserChecked);
		$IsUserChecked 	= $Result['state'];
		$user          	= $Result['record'];

		if($game_config['game_disable'] == 0 && $user['authlevel'] == 0)
		{
			message(stripslashes($game_config['close_reason']), '', '', false, false);
		}
	}

	if ( ( time() >= ( $game_config['stat_last_update'] + ( 60 * $game_config['stat_update_time'] ) ) ) )
	{
		include($xgp_root . 'adm/statfunctions.' . $phpEx);
		$result		= MakeStats();
		update_config('stat_last_update', $result['stats_time']);
	}

	


	if (isset($user))
	{
		include($xgp_root . 'includes/classes/class.FlyingFleetHandler.'.$phpEx);
		$_fleets = doquery("SELECT fleet_start_universe,fleet_start_galaxy,fleet_start_system,fleet_start_planet,fleet_start_type FROM {{table}} WHERE `fleet_start_time` <= '".time()."' and `fleet_mess` ='0' order by fleet_id asc;", 'fleets'); // OR fleet_end_time <= ".time()

		while ($row = mysql_fetch_array($_fleets))
		{
			$array = array();
			$array['universe'] 		= $row['fleet_start_universe'];
			$array['galaxy'] 		= $row['fleet_start_galaxy'];
			$array['system'] 		= $row['fleet_start_system'];
			$array['planet'] 		= $row['fleet_start_planet'];
			$array['planet_type'] 	= $row['fleet_start_type'];

			$temp = new FlyingFleetHandler ($array);
		}
		mysql_free_result($_fleets);
		$_fleets = doquery("SELECT fleet_end_universe,fleet_end_galaxy,fleet_end_system,fleet_end_planet ,fleet_end_type FROM {{table}} WHERE `fleet_end_time` <= '".time()." order by fleet_id asc';", 'fleets'); // OR fleet_end_time <= ".time()

		while ($row = mysql_fetch_array($_fleets))
		{
			$array = array();
			//start mod
			$array['universe'] 	= $row['fleet_end_universe'];
			//end mod
			$array['galaxy'] 		= $row['fleet_end_galaxy'];
			$array['system'] 		= $row['fleet_end_system'];
			$array['planet'] 		= $row['fleet_end_planet'];
			$array['planet_type'] 	= $row['fleet_end_type'];

			$temp = new FlyingFleetHandler ($array);
		}

		mysql_free_result($_fleets);
		unset($_fleets);

		if ( defined('IN_ADMIN') )
		{
			includeLang('ADMIN');
			include('../adm/AdminFunctions/Autorization.' . $phpEx);
			$dpath     = "../". DEFAULT_SKINPATH  ;
		}
		else
		{
			$dpath     = (!$user["dpath"]) ? DEFAULT_SKINPATH : $user["dpath"];
		}
        //We include the plugin system 0.3
        include($xgp_root . 'includes/plugins.'.$phpEx);

		include($xgp_root . 'includes/functions/SetSelectedPlanet.' . $phpEx);
		SetSelectedPlanet ($user);

		$planetrow = doquery("SELECT * FROM `{{table}}` WHERE `id` = '".$user['current_planet']."';", "planets", true);

		//include($xgp_root . 'includes/functions/CheckPlanetUsedFields.' . $phpEx);
		//CheckPlanetUsedFields($planetrow);
	}
}
else
{
	$dpath     = "../" . DEFAULT_SKINPATH;
}

include('includes/classes/class.SecurePage.' . $phpEx ); // include the class
$SecureSqlInjection	= new SecureSqlInjection(); // load the class
$SecureSqlInjection->secureGlobals(); // run the main class function

?>