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

$xgp_root = './';
include($xgp_root . 'extension.inc.php');
include($xgp_root . 'common.'.$phpEx);

//start mod
if ($_GET['universe'])
  $u 		= intval($_GET['universe']);  
else
  $u 		= $user['universe'];
//end mod
$g 		= intval($_GET['galaxy']);
$s 		= intval($_GET['system']);
$i 		= intval($_GET['planet']);
$anz 	= intval($_POST['SendMI']);
$pziel 	= mysql_real_escape_string($_POST['Target']);


if ($anz < 0)
{
	$anz = 0;
}

$currentplanet 		= doquery("SELECT * FROM {{table}} WHERE `id` ='".$user['current_planet']."'",'planets',true);
$iraks 				= $currentplanet['interplanetary_misil'];
$tempvar1 			= abs($s - $currentplanet['system']);
$tempvar2 			= ($user['impulse_motor_tech'] * 2) - 1;
//start mod
$tempvar3 			= doquery("SELECT * FROM {{table}} WHERE 
`universe` = '".$u."' AND
`galaxy` = '".$g."' AND 
`system` = '".$s."' AND 
`planet` = '".$i."' AND 
`planet_type` = '1' limit 1", 'planets',true);
//end mod
$tempvar4 			= doquery("SELECT * FROM {{table}} WHERE `id` = '".$tempvar3['id_owner']. "' limit 1",'users', true);

$protection 		  = $game_config['noobprotection'];
$protectiontime 	= $game_config['noobprotectiontime'];
$protectionmulti 	= $game_config['noobprotectionmulti'];

$UserPoints 		= doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user['id'] ."';", 'statpoints', true);
$User2Points 		= doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $tempvar3['id_owner'] ."';", 'statpoints', true);

$CurrentPoints 		= $UserPoints['total_points'];
$RowUserPoints 		= $User2Points['total_points'];
$MyGameLevel 		= $CurrentPoints * $protectionmulti['config_value'];
$HeGameLevel 		= $RowUserPoints * $protectionmulti['config_value'];

if ($currentplanet['silo'] < 4)
	$error = $lang['ma_silo_level'];
elseif ($user['impulse_motor_tech'] == 0)
	$error = $lang['ma_impulse_drive_required'];
elseif ($tempvar1 >= $tempvar2 || $g != $currentplanet['galaxy'])
	$error = $lang['ma_not_send_other_galaxy'];
elseif (!$tempvar3)
	$error = $lang['ma_planet_doesnt_exists'];
elseif ($anz > $iraks)
	$error = $lang['ma_cant_send'] . $anz . $lang['ma_missile'] . $iraks;
elseif ((!is_numeric($pziel) && $pziel != "all") OR ($pziel < 0 && $pziel > 7 && $pziel != "all"))
	$error = $lang['ma_wrong_target'];
elseif ($iraks==0)
	$error = $lang['ma_no_missiles'];
elseif ($anz==0)
	$error = $lang['ma_add_missile_number'];
elseif ($tempvar4['onlinetime'] >= (time()-60 * 60 * 24 * 7)){
	if(($MyGameLevel > ($HeGameLevel * $protectionmulti)) && $protection == 1 && ($HeGameLevel < ($protectiontime * 1000)))
		$error = $lang['fl_week_player'];
	elseif ((($MyGameLevel * $protectionmulti) < $HeGameLevel) && $protection == 1 && ($MyGameLevel < ($protectiontime * 1000)))
		$error = $lang['fl_strong_player'];
}
elseif ($tempvar4['urlaubs_modus']==1)
	$error = $lang['fl_in_vacation_player'];

if ($error != "")
//start mod
	exit ( message ( $error, "game.php?page=galaxy&mode=2&universe=".$u."&galaxy=".$g."&system=".$s."&planet=".$i."&current=".$user['current_planet']."", 2 ) );
//end mod
$ziel_id = $tempvar3["id_owner"];

$flugzeit = round(((30 + (60 * $tempvar1)) * 2500) / $game_config['fleet_speed']);

$DefenseLabel =
array(
'0' => $lang['ma_misil_launcher'],
'1' => $lang['ma_small_laser'],
'2' => $lang['ma_big_laser'],
'3' => $lang['ma_gauss_canyon'],
'4' => $lang['ma_ionic_canyon'],
'5' => $lang['ma_buster_canyon'],
'6' => $lang['ma_small_protection_shield'],
'7' => $lang['ma_big_protection_shield'],
'all' => $lang['ma_all']);

//start mod
doquery("INSERT INTO {{table}} SET
fleet_owner = ".$user['id'].",
fleet_mission = 10,
fleet_amount = ".$anz.",
fleet_array = '503,".$anz."',
fleet_start_time = '".(time() + $flugzeit)."',
fleet_start_universe = '".$currentplanet['universe']."',
fleet_start_galaxy = '".$currentplanet['galaxy']."',
fleet_start_system = '".$currentplanet['system']."',
fleet_start_planet ='".$currentplanet['planet']."',
fleet_start_type = 1,
fleet_end_time = '".(time() + $flugzeit+1)."',
fleet_end_stay = 0,
fleet_end_universe = '".$u."',
fleet_end_galaxy = '".$g."',
fleet_end_system = '".$s."',
fleet_end_planet = '".$i."',
fleet_end_type = 1,
fleet_target_obj = '".$pziel."',
fleet_resource_metal = 0,
fleet_resource_crystal = 0,
fleet_resource_deuterium = 0,
fleet_target_owner = '".$ziel_id."',
fleet_group = 0,
fleet_mess = 0,
start_time = ".time().";", 'fleets');
//end mod
doquery("UPDATE {{table}} SET interplanetary_misil = (interplanetary_misil - ".$anz.") WHERE `id` = '".$user['current_planet']."'", 'planets');

message("<b>".$anz."</b>". $lang['ma_missiles_sended'] .$DefenseLabel[$pziel], "game.php?page=overview", 3);
?>