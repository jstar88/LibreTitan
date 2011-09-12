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


$mode      = $_POST['mode'];

if ($mode == 'agregar')
{
   	$id            = $_POST['id'];
   	$universe        = $_POST['universe'];
    $galaxy        = $_POST['galaxy'];
    $system        = $_POST['system'];
    $planet        = $_POST['planet'];

	$i	=	0;
	$QueryS	=	doquery("SELECT * FROM {{table}} WHERE `universe` = '".$universe."' AND `galaxy` = '".$galaxy."' AND `system` = '".$system."' AND `planet` = '".$planet."'", "galaxy", true);
	$QueryS2	=	doquery("SELECT * FROM {{table}} WHERE `id` = '".$id."'", "users", true);
	if (is_numeric($_POST['id']) && isset($_POST['id']) && !$QueryS && $QueryS2)
	{
    	if ($universe < 1 or $galaxy < 1 or $system < 1 or $planet < 1 or !is_numeric($universe) or !is_numeric($galaxy) or !is_numeric($system) or !is_numeric($planet)){
    		$Error	.=	'<tr><th colspan="2"><font color=red>'.$lang['po_complete_all'].'</font></th></tr>';
			$i++;}

		if ($universe > MAX_UNIVERSE_IN_WORLD or $system > MAX_SYSTEM_IN_GALAXY or $planet > MAX_PLANET_IN_SYSTEM){
			$Error	.=	'<tr><th colspan="2"><font color=red>'.$lang['po_complete_all2'].'</font></th></tr>';
			$i++;}

		if ($i	==	0)
		{
			CreateOnePlanetRecord ($universe,$galaxy, $system, $planet, $id, '', '', false) ;
			$QueryS3	=	doquery("SELECT * FROM {{table}} WHERE `id_owner` = '".$id."'", "planets", true);
			doquery("UPDATE {{table}} SET `id_level` = '".$QueryS3['id_level']."' WHERE
			`universe` = '".$universe."' AND `galaxy` = '".$galaxy."' AND `system` = '".$system."' AND `planet` = '".$planet."' AND `planet_type` = '1'", "planets");
    		$parse['display']	=	'<tr><th colspan="2"><font color=lime>'.$lang['po_complete_succes'].'</font></th></tr>';
		}
		else
		{
			$parse['display']	=	$Error;
		}
	}
	else
	{
		$parse['display']	=	'<tr><th colspan="2"><font color=red>'.$lang['po_complete_all'].'</font></th></tr>';
	}
}
elseif ($mode == 'borrar')
{
	$id	=	$_POST['id'];
	if (is_numeric($id) && isset($id))
	{
		$QueryS	=	doquery("SELECT * FROM {{table}} WHERE `id` = '".$id."'", "planets", true);

		if ($QueryS)
		{
			if ($QueryS['planet_type'] == '1')
			{
				$QueryS2	=	doquery("SELECT * FROM {{table}} WHERE `id_planet` = '".$id."'", "galaxy", true);
				if ($QueryS2['id_luna'] > 0)
				{
					doquery("DELETE FROM {{table}} WHERE `universe` = '".$QueryS['universe']."' AND `galaxy` = '".$QueryS['galaxy']."' AND `system` = '".$QueryS['system']."' AND
						`planet` = '".$QueryS['planet']."' AND `planet_type` = '3'", "planets");
				}
				doquery("DELETE FROM {{table}} WHERE `id` = '".$id."'", 'planets');
    			doquery("DELETE FROM {{table}} WHERE `id_planet` ='".$id."'", 'galaxy');
				$Error	.=	'<tr><th colspan="2"><font color=lime>'.$lang['po_complete_succes2'].'</font></th></tr>';
			}
			else
			{
				$Error	.=	'<tr><th colspan="2"><font color=red>'.$lang['po_complete_invalid3'].'</font></th></tr>';
			}
		}
		else
		{
			$Error	.=	'<tr><th colspan="2"><font color=red>'.$lang['po_complete_invalid2'].'</font></th></tr>';
		}
	}
	else
	{
		$Error	.=	'<tr><th colspan="2"><font color=red>'.$lang['po_complete_invalid'].'</font></th></tr>';
	}

	$parse['display2']	=	$Error;
}


display (parsetemplate(gettemplate('adm/PlanetOptionsBody'),  $parse), false, '', true, false);
?>