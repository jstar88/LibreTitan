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

if ($user['authlevel'] < 1) die(message ($lang['404_page']));

function check_updates()
{
	global $game_config;

	if (function_exists('file_get_contents'))
	{
		$current = @file_get_contents('http://www.xgproyect.net/xgproyect/current.php');

		if ($current > $game_config['VERSION'])
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

$parse	=	$lang;

if(file_exists($xgp_root . 'install/') && defined('IN_ADMIN'))
{
	$Message	.= "<font color=\"red\">".$lang['ow_install_file_detected']."</font><br/><br/>";
	$error++;
}

if ($user['authlevel'] >= 3)
{
	if(@fopen("./../config.php", "a"))
	{
		$Message	.= "<font color=\"red\">".$lang['ow_config_file_writable']."</font><br/><br/>";
		$error++;
	}

	$Errors = doquery("SELECT COUNT(*) AS `errors` FROM {{table}} WHERE 1;", 'errors', true);

	if($Errors['errors'] != 0)
	{
		$Message	.= "<font color=\"red\">".$lang['ow_database_errors']."</font><br/><br/>";
		$error++;
	}

	if(check_updates())
	{
		$Message	.= "<font color=\"red\">".$lang['ow_old_version']."</font><br/><br/>";
		$error++;
	}
}

if($error != 0)
{
	$parse['error_message']		=	$Message;
	$parse['color']				=	"red";}
else
{
	$parse['error_message']		= 	$lang['ow_none'];
	$parse['color']				=	"lime";
}


display( parsetemplate(gettemplate('adm/OverviewBody'), $parse), false, '', true, false);
?>