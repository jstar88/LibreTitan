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

if ($ConfigGame != 1) die(message ($lang['404_page']));
$AreLog	=	$LogCanWork;

function DisplayGameSettingsPage ( $CurrentUser )
{
	global $game_config, $lang, $AreLog;

	if ($_POST['opt_save'] == "1")
	{
		$Log	.=	"\n".$lang['log_the_user'].$user['username'].$lang['log_sett_no1'].":\n";


		if (isset($_POST['closed']) && $_POST['closed'] == 'on') {
		$game_config['game_disable']         = 1;
		$game_config['close_reason']         = addslashes( $_POST['close_reason'] );
		$Log	.=	$lang['log_sett_close'].": ".$lang['log_viewmod2'][1]."\n";
		} else {
		$game_config['game_disable']         = 0;
		$game_config['close_reason']         = addslashes( $_POST['close_reason'] );
		$Log	.=	$lang['log_sett_close'].": ".$lang['log_viewmod2'][0]."\n";
		$Log	.=	$lang['log_sett_close_rea'].": ".$_POST['close_reason']."\n";
		}

		if (isset($_POST['debug']) && $_POST['debug'] == 'on') {
		$game_config['debug'] = 1;
		$Log	.=	$lang['log_sett_debug'].": ".$lang['log_viewmod'][1]."\n";
		} else {
		$game_config['debug'] = 0;
		$Log	.=	$lang['log_sett_debug'].": ".$lang['log_viewmod'][0]."\n";
		}

		if (isset($_POST['game_name']) && $_POST['game_name'] != '') {
		$game_config['game_name'] = $_POST['game_name'];
		$Log	.=	$lang['log_sett_name_game'].": ".$_POST['game_name']."\n";
		}

		if (isset($_POST['forum_url']) && $_POST['forum_url'] != '') {
		$game_config['forum_url'] = $_POST['forum_url'];
		$Log	.=	$lang['log_sett_forum_url'].": ".$_POST['forum_url']."\n";
		}

		if (isset($_POST['game_speed']) && is_numeric($_POST['game_speed'])) {
		$game_config['game_speed'] = (2500 * $_POST['game_speed']);
		$Log	.=	$lang['log_sett_velo_game'].": x".$_POST['game_speed']."\n";
		}

		if (isset($_POST['fleet_speed']) && is_numeric($_POST['fleet_speed'])) {
		$game_config['fleet_speed'] = (2500 * $_POST['fleet_speed']);
		$Log	.=	$lang['log_sett_velo_flottes'].": x".$_POST['fleet_speed']."\n";
		}

		if (isset($_POST['resource_multiplier']) && is_numeric($_POST['resource_multiplier'])) {
		$game_config['resource_multiplier'] = $_POST['resource_multiplier'];
		$Log	.=	$lang['log_sett_velo_prod'].": x".$_POST['resource_multiplier']."\n";
		}

		if (isset($_POST['initial_fields']) && is_numeric($_POST['initial_fields'])) {
		$game_config['initial_fields'] = $_POST['initial_fields'];
		$Log	.=	$lang['log_sett_fields'].": ".$_POST['initial_fields']."\n";
		}

		if (isset($_POST['metal_basic_income']) && is_numeric($_POST['metal_basic_income'])) {
		$game_config['metal_basic_income'] = $_POST['metal_basic_income'];
		$Log	.=	$lang['log_sett_basic_m'].": ".$_POST['metal_basic_income']."\n";
		}

		if (isset($_POST['crystal_basic_income']) && is_numeric($_POST['crystal_basic_income'])) {
		$game_config['crystal_basic_income'] = $_POST['crystal_basic_income'];
		$Log	.=	$lang['log_sett_basic_c'].": ".$_POST['crystal_basic_income']."\n";
		}

		if (isset($_POST['deuterium_basic_income']) && is_numeric($_POST['deuterium_basic_income'])) {
		$game_config['deuterium_basic_income'] = $_POST['deuterium_basic_income'];
		$Log	.=	$lang['log_sett_basic_d'].": ".$_POST['deuterium_basic_income']."\n";
		}

		if (isset($_POST['adm_attack']) && $_POST['adm_attack'] == 'on') {
			$game_config['adm_attack'] = 1;
			$Log	.=	$lang['log_sett_adm_protection'].": ".$lang['log_viewmod'][1]."\n";
		} else {
			$game_config['adm_attack'] = 0;
			$Log	.=	$lang['log_sett_adm_protection'].": ".$lang['log_viewmod'][0]."\n";
		}

		if (isset($_POST['language'])) {
			$game_config['lang'] = $_POST['language'];
			$Log	.=	$lang['log_sett_language'].": ".$_POST['language']."\n";
		} else {
			$game_config['lang'];
		}

		if (isset($_POST['cookie_name']) && $_POST['game_name'] != '') {
			$game_config['COOKIE_NAME'] = $_POST['cookie_name'];
			$Log	.=	$lang['log_sett_name_cookie'].": ".$_POST['cookie_name']."\n";
		}

		if (isset($_POST['Defs_Cdr']) && is_numeric($_POST['Defs_Cdr'])) {
			if ($_POST['Defs_Cdr'] < 0){
				$game_config['Defs_Cdr'] = 0;
				$Number	=	0;}
			else{
				$game_config['Defs_Cdr'] = $_POST['Defs_Cdr'];
				$Number	=	$_POST['Defs_Cdr'];}

			$Log	.=	$lang['log_sett_debris_def'].": ".$Number."%\n";
		}

		if (isset($_POST['Fleet_Cdr']) && is_numeric($_POST['Fleet_Cdr'])) {
			if ($_POST['Fleet_Cdr'] < 0){
				$game_config['Fleet_Cdr'] = 0;
				$Number2	=	0;}
			else{
				$game_config['Fleet_Cdr'] = $_POST['Fleet_Cdr'];
				$Number2	=	$_POST['Fleet_Cdr'];}

			$Log	.=	$lang['log_sett_debris_flot'].": ".$Number2."%\n";
		}

		if (isset($_POST['noobprotection']) && $_POST['noobprotection'] == 'on') {
			$game_config['noobprotection'] = 1;
			$Log	.=	$lang['log_sett_act_noobs'].": ".$lang['log_viewmod'][1]."\n";
		} else {
			$game_config['noobprotection'] = 0;
			$Log	.=	$lang['log_sett_act_noobs'].": ".$lang['log_viewmod'][0]."\n";
		}

		if (isset($_POST['noobprotectiontime']) && is_numeric($_POST['noobprotectiontime'])) {
			$game_config['noobprotectiontime'] = $_POST['noobprotectiontime'];
			$Log	.=	$lang['log_sett_noob_time'].": ".$_POST['noobprotectiontime']."\n";
		}

		if (isset($_POST['noobprotectionmulti']) && is_numeric($_POST['noobprotectionmulti'])) {
			$game_config['noobprotectionmulti'] = $_POST['noobprotectionmulti'];
			$Log	.=	$lang['log_sett_noob_multi'].": ".$_POST['noobprotectionmulti']."\n";
		}


		LogFunction($Log, "ConfigLog", $AreLog);

		doquery("UPDATE {{table}} SET `config_value` = '". $game_config['game_disable']           ."' WHERE `config_name` = 'game_disable';", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '". $game_config['close_reason']           ."' WHERE `config_name` = 'close_reason';", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '". $game_config['game_name']              ."' WHERE `config_name` = 'game_name';", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '". $game_config['forum_url']              ."' WHERE `config_name` = 'forum_url';", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '". $game_config['game_speed']             ."' WHERE `config_name` = 'game_speed';", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '". $game_config['fleet_speed']            ."' WHERE `config_name` = 'fleet_speed';", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '". $game_config['resource_multiplier']    ."' WHERE `config_name` = 'resource_multiplier';", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '". $game_config['initial_fields']         ."' WHERE `config_name` = 'initial_fields';", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '". $game_config['metal_basic_income']     ."' WHERE `config_name` = 'metal_basic_income';", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '". $game_config['crystal_basic_income']   ."' WHERE `config_name` = 'crystal_basic_income';", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '". $game_config['deuterium_basic_income'] ."' WHERE `config_name` = 'deuterium_basic_income';", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '" .$game_config['debug']                  ."' WHERE `config_name` = 'debug'", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '" .$game_config['adm_attack']             ."' WHERE `config_name` = 'adm_attack'", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '" .$game_config['lang']             	  ."' WHERE `config_name` = 'lang'", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '" .$game_config['COOKIE_NAME'] 			  ."' WHERE `config_name` = 'COOKIE_NAME';", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '" .$game_config['noobprotection']         ."' WHERE `config_name` = 'noobprotection'", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '" .$game_config['Defs_Cdr'] 	  		  ."' WHERE `config_name` = 'Defs_Cdr';", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '" .$game_config['Fleet_Cdr'] 	  		  ."' WHERE `config_name` = 'Fleet_Cdr';", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '" .$game_config['noobprotectiontime'] 	  ."' WHERE `config_name` = 'noobprotectiontime';", 'config');
		doquery("UPDATE {{table}} SET `config_value` = '" .$game_config['noobprotectionmulti'] 	  ."' WHERE `config_name` = 'noobprotectionmulti';", 'config');

		header("location:SettingsPage.php");
	}
	else
	{
		$parse								= $lang;
		$parse['game_name']              	= $game_config['game_name'];
		$parse['game_speed']             	= ($game_config['game_speed'] / 2500);
		$parse['fleet_speed']            	= ($game_config['fleet_speed'] / 2500);
		$parse['resource_multiplier']    	= $game_config['resource_multiplier'];
		$parse['forum_url']              	= $game_config['forum_url'];
		$parse['initial_fields']         	= $game_config['initial_fields'];
		$parse['metal_basic_income']     	= $game_config['metal_basic_income'];
		$parse['crystal_basic_income']   	= $game_config['crystal_basic_income'];
		$parse['deuterium_basic_income'] 	= $game_config['deuterium_basic_income'];
		$parse['closed']                 	= ($game_config['game_disable'] == 1) ? " checked = 'checked' ":"";
		$parse['close_reason']           	= stripslashes($game_config['close_reason']);
		$parse['debug']                  	= ($game_config['debug'] == 1)        ? " checked = 'checked' ":"";
		$parse['adm_attack']             	= ($game_config['adm_attack'] == 1)   ? " checked = 'checked' ":"";
		$parse['cookie'] 					= $game_config['COOKIE_NAME'];
		$parse['defenses'] 					= $game_config['Defs_Cdr'];
		$parse['shiips'] 					= $game_config['Fleet_Cdr'];
		$parse['noobprot']            	 	= ($game_config['noobprotection'] == 1)   ? " checked = 'checked' ":"";
		$parse['noobprot2'] 				= $game_config['noobprotectiontime'];
		$parse['noobprot3'] 				= $game_config['noobprotectionmulti'];

		$LangFolder = opendir("./../" . 'language');

		while (($LangSubFolder = readdir($LangFolder)) !== false)
		{
			if($LangSubFolder != '.' && $LangSubFolder != '..' && $LangSubFolder != '.htaccess' && $LangSubFolder != '.svn')
			{
				$parse['language_settings'] .= "<option ";

				if($game_config['lang'] == $LangSubFolder)
					$parse['language_settings'] .= "selected = selected";

				$parse['language_settings'] .= " value=\"".$LangSubFolder."\">".$LangSubFolder."</option>";
			}
		}

		return display (parsetemplate(gettemplate('adm/SettingsBody'),  $parse), false, '', true, false);
	}
}

DisplayGameSettingsPage($user);
?>