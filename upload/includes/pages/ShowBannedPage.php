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

function ShowBannedPage()
{
	global $lang;

	$parse = $lang;
	$query = doquery("SELECT * FROM {{table}} ORDER BY `id`;",'banned');

	$i=0;

	while($u = mysql_fetch_array($query))
	{
		$parse['banned'] .=
	        "<tr><td class=b><center><b>".$u[1]."</center></td></b>".
		"<td class=b><center><b>".$u[2]."</center></b></td>".
		"<td class=b><center><b>".gmdate("d/m/Y G:i:s",$u[4])."</center></b></td>".
		"<td class=b><center><b>".gmdate("d/m/Y G:i:s",$u[5])."</center></b></td>".
		"<td class=b><center><b>".$u[6]."</center></b></td></tr>";
		$i++;
	}

	if ($i == 0)
		$parse['banned'] .= "<tr><th class=b colspan=6>".$lang['bn_no_players_banned']."</th></tr>";
	else
	  	$parse['banned'] .= "<tr><th class=b colspan=6>".$lang['bn_exists'] . $i . $lang['bn_players_banned']."</th></tr>";

	return display(parsetemplate(gettemplate('banned_body'), $parse));
}
?>