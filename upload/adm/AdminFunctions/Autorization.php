<?php
##############################################################################
# *																			 #
# * XG PROYECT																 #
# *  																		 #
# * @copyright Copyright (C) 2008 - 2009 By Neko from xgproyect.net	         #
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
include_once('LogFunction.' . $phpEx);

if ($user['authlevel'] < 1){die();}

$QueryModeration	=	doquery("SELECT * FROM {{table}} WHERE `config_name` = 'moderation'", "config", true);
$QueryModerationEx	=	explode(";", $QueryModeration[1]);
$Moderator			=	explode(",", $QueryModerationEx[0]);
$Operator			=	explode(",", $QueryModerationEx[1]);
$Administrator		=	explode(",", $QueryModerationEx[2]);

if ($user['authlevel'] == 1)
{
	$Observation	=	$Moderator[0];
	$EditUsers		=	$Moderator[1];
	$ConfigGame		=	$Moderator[2];
	$ToolsCanUse	=	$Moderator[3];
	$LogCanWork		=	$Moderator[4];
}

if ($user['authlevel'] == 2)
{
	$Observation	=	$Operator[0];
	$EditUsers		=	$Operator[1];
	$ConfigGame		=	$Operator[2];
	$ToolsCanUse	=	$Operator[3];
	$LogCanWork		=	$Operator[4];
}

if ($user['authlevel'] == 3)
{
	$Observation	=	1;
	$EditUsers		=	1;
	$ConfigGame		=	1;
	$ToolsCanUse	=	1;
	$LogCanWork		=	$Administrator[0];
}
?>