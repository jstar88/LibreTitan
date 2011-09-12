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

$xgp_root = '../';
include($xgp_root . 'extension.inc.php');
include($xgp_root . 'common.' . $phpEx);

if ($ConfigGame != 1) die(message ($lang['404_page']));

$parse = $lang;

if (!$_POST)
{
	$Tablas = doquery("SHOW TABLES","todas");
	while ($row = mysql_fetch_assoc($Tablas))
	{
		foreach ($row as $opcion => $tabla)
		{
			$parse['tabla'] .= "<tr>";
			$parse['tabla'] .= "<th width=\"50%\">".$tabla."</th><th width=\"50%\"><font color=aqua>".$lang['od_select_action']."</font></th>";
			$parse['tabla'] .= "</tr>";
		}
	}
}
else
{
	$Tablas = doquery("SHOW TABLES",'todas');
	
	while ($row = mysql_fetch_assoc($Tablas))
	{
		foreach ($row as $opcion => $tabla)
		{
			if ($_POST['Optimize']){
				doquery("OPTIMIZE TABLE {$tabla}", "$tabla");
				$Message	=	$lang['od_opt'];
				$Log	=	"\n".$lang['log_database_title']."\n".$lang['log_the_user'].$user['username'].$lang['log_database_view'].":\n".$lang['log_data_optimize']."\n";}
				
			if ($_POST['Repair']){
				doquery("REPAIR TABLE {$tabla}", "$tabla");
				$Message	=	$lang['od_rep'];
				$Log	=	"\n".$lang['log_database_title']."\n".$lang['log_the_user'].$user['username'].$lang['log_database_view'].":\n".$lang['log_data_repair']."\n";}
				
			if ($_POST['Check']){
				doquery("CHECK TABLE {$tabla}", "$tabla");
				$Message	=	$lang['od_check_ok'];
				$Log	=	"\n".$lang['log_database_title']."\n".$lang['log_the_user'].$user['username'].$lang['log_database_view'].":\n".$lang['log_data_check']."\n";}
				
			if (mysql_errno())
			{
				$parse['tabla'] .= "<tr>";
				$parse['tabla'] .= "<th width=\"50%\">".$tabla."</th>";
				$parse['tabla'] .= "<th width=\"50%\" style=\"color:red\">".$lang['od_not_opt']."</th>";
				$parse['tabla'] .= "</tr>";
			}
			else
			{
				$parse['tabla'] .= "<tr>";
				$parse['tabla'] .= "<th width=\"50%\">".$tabla."</th>";
				$parse['tabla'] .= "<th width=\"50%\" style=\"color:lime\">".$Message."</th>";
				$parse['tabla'] .= "</tr>";
			}
		}
	}
		
	LogFunction($Log, "GeneralLog", $LogCanWork);
}

display(parsetemplate(gettemplate('adm/DataBaseViewBody'), $parse), false, '', true, false);
?>