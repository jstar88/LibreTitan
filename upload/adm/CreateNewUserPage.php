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
include($xgp_root . 'common.' . $phpEx);
include('AdminFunctions/Autorization.' . $phpEx);

if ($EditUsers != 1) die();

$parse	=	$lang;

$name		=	$_POST['name'];
$pass 		= 	md5($_POST['password']);
$email 		= 	$_POST['email'];
$universe		=	$_POST['universe'];
$galaxy		=	$_POST['galaxy'];
$system		=	$_POST['system'];
$planet		=	$_POST['planet'];
$auth		=	$_POST['authlevel'];
$time		=	time();
$i			=	0;
if ($_POST)
{
	$CheckUser = doquery("SELECT `username` FROM {{table}} WHERE `username` = '" . mysql_escape_string($_POST['name']) . "' LIMIT 1", "users", true);
	$CheckMail = doquery("SELECT `email` FROM {{table}} WHERE `email` = '" . mysql_escape_string($_POST['email']) . "' LIMIT 1", "users", true);
	$CheckRows = doquery("SELECT * FROM {{table}} WHERE `universe` = '".$universe."' AND`galaxy` = '".$galaxy."' AND `system` = '".$system."' AND `planet` = '".$planet."' LIMIT 1", "galaxy", true);


	if (!is_numeric($universe) &&!is_numeric($galaxy) &&  !is_numeric($system) && !is_numeric($planet)){
		$parse['display']	.=	'<tr><th colspan="2" class="red">'.$lang['new_only_numbers'].'</tr></th>';
		$i++;}
	elseif ($universe > MAX_UNIVERSE_IN_WORLD || $galaxy > MAX_GALAXY_IN_WORLD || $system > MAX_SYSTEM_IN_GALAXY || $planet > MAX_PLANET_IN_SYSTEM || $universe < 1 || $galaxy < 1 || $system < 1 || $planet < 1){
		$parse['display']	.=	'<tr><th colspan="2" class="red">'.$lang['new_error_coord'].'</tr></th>';
		$i++;}

	if (!$name || !$pass || !$email || !$universe || !$galaxy || !$system || !$planet){
		$parse['display']	.=	'<tr><th colspan="2" class="red">'.$lang['new_complete_all'].'</tr></th>';
		$i++;}

	if (!is_email(strip_tags($email))){
		$parse['display']	.=	'<tr><th colspan="2" class="red">'.$lang['new_error_email2'].'</tr></th>';
		$i++;}

	if ($CheckUser){
		$parse['display']	.=	'<tr><th colspan="2" class="red">'.$lang['new_error_name'].'</tr></th>';
		$i++;}

	if ($CheckMail){
		$parse['display']	.=	'<tr><th colspan="2" class="red">'.$lang['new_error_email'].'</tr></th>';
		$i++;}

	if ($CheckRows){
		$parse['display']	.=	'<tr><th colspan="2" class="red">'.$lang['new_error_galaxy'].'</tr></th>';
		$i++;}


	if ($i	==	'0'){
		$Query1  = "INSERT INTO {{table}} SET ";
		$Query1 .= "`username` = '" . mysql_escape_string(strip_tags($name)) . "', ";
		$Query1 .= "`email` = '" . mysql_escape_string($email) . "', ";
		$Query1 .= "`email_2` = '" . mysql_escape_string($email) . "', ";
		$Query1 .= "`ip_at_reg` = '" . $_SERVER["REMOTE_ADDR"] . "', ";
		$Query1 .= "`id_planet` = '0', ";
		$Query1 .= "`register_time` = '" .$time. "', ";
		$Query1 .= "`onlinetime` = '" .$time. "', ";
		$Query1 .= "`authlevel` = '" .$auth. "', ";
		$Query1 .= "`password`='" . $pass . "';";
		doquery($Query1, "users");

		doquery("UPDATE {{table}} SET `config_value` = config_value + '1' WHERE `config_name` = 'users_amount';", 'config');

		$ID_USER 	= doquery("SELECT `id` FROM {{table}} WHERE `username` = '" . mysql_escape_string($name) . "' LIMIT 1", "users", true);

		CreateOnePlanetRecord ($universe,$galaxy, $system, $planet, $ID_USER['id'], $UserPlanet, true);

		$ID_PLANET 	= doquery("SELECT `id` FROM {{table}} WHERE `id_owner` = '". $ID_USER['id'] ."' LIMIT 1" , "planets", true);

		doquery("UPDATE {{table}} SET `id_level` = '".$auth."' WHERE `id` = '".$ID_PLANET['id']."'", "planets");

		$QryUpdateUser = "UPDATE {{table}} SET ";
		$QryUpdateUser .= "`id_planet` = '" . $ID_PLANET['id'] . "', ";
		$QryUpdateUser .= "`current_planet` = '" . $ID_PLANET['id'] . "', ";
		$QryUpdateUser .= "`universe` = '" . $universe . "', ";
		$QryUpdateUser .= "`galaxy` = '" . $galaxy . "', ";
		$QryUpdateUser .= "`system` = '" . $system . "', ";
		$QryUpdateUser .= "`planet` = '" . $planet . "' ";
		$QryUpdateUser .= "WHERE ";
		$QryUpdateUser .= "`id` = '" . $ID_USER['id'] . "' ";
		$QryUpdateUser .= "LIMIT 1;";
		doquery($QryUpdateUser, "users");

		$parse['display']	=	'<tr><th colspan="2"><font color=lime>'.$lang['new_user_success'].'</font></tr></th>';
	}
}



display(parsetemplate(gettemplate('adm/CreateNewUserBody'), $parse), false, '', true, false);
?>