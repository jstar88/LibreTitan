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
include('AdminFunctions/Autorization.' . $phpEx);

if ($Observation != 1) die();

	$parse	= $lang;
	$query 	= doquery("SELECT * FROM {{table}} WHERE `planet_type` = '3'", "planets");
	$i 		= 0;

	while ($u = mysql_fetch_array($query))
	{
		$parse['moon'] .= "<tr>"
		. "<th>" . $u[0] . "</th>"
		. "<th>" . $u[1] . "</th>"
		. "<th>" . $u[2] . "</th>"
		. "<th>" . $u[4] . "</th>"
		. "<th>" . $u[5] . "</th>"
		. "<th>" . $u[6] . "</th>"
		. "</tr>";
		$i++;
	}

	if ($i == "1")
		$parse['moon'] .= "<tr><th class=b colspan=6>".$lang['mt_only_one_moon']."</th></tr>";
	else
		$parse['moon'] .= "<tr><th class=b colspan=6>". $lang['mt_there_are'] . $i . $lang['mt_moons'] ."</th></tr>";

	display(parsetemplate(gettemplate('adm/MoonListBody'), $parse), false, '', true, false);
?>