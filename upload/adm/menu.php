<?PHP

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
include($xgp_root . 'common.'.$phpEx);

if ($user['authlevel'] < 1) die(message ($lang['404_page']));

$parse			=	$lang;


$ConfigTable	= parsetemplate(gettemplate('adm/table/configTable'), $parse);		
$EditTable	  = parsetemplate(gettemplate('adm/table/editTable'), $parse);		
$ViewTable	  = parsetemplate(gettemplate('adm/table/viewTable'), $parse);		
$ToolsTable  	= parsetemplate(gettemplate('adm/table/toolsTable'), $parse);
$UniverseTable= parsetemplate(gettemplate('adm/table/universeTable'), $parse);


// MODERADORES
if($user['authlevel'] == 1)
{
	if($Observation == 1) $parse['ViewTable']	=	$ViewTable;
	if($EditUsers 	== 1) $parse['EditTable']	=	$EditTable;
	if($ConfigGame 	== 1) $parse['ConfigTable']	=	$ConfigTable;
	if($ToolsCanUse == 1) $parse['ToolsTable']	=	$ToolsTable;
	if($ConfigGame == 1) $parse['UniverseTable']	=	$UniverseTable;
}

// OPERADORES
if($user['authlevel'] == 2)
{
	if($Observation == 1) $parse['ViewTable']	   =	$ViewTable; 
	if($EditUsers 	== 1) $parse['EditTable']	   =	$EditTable;
	if($ConfigGame 	== 1) $parse['ConfigTable']	 =	$ConfigTable;
	if($ToolsCanUse == 1) $parse['ToolsTable']	 =	$ToolsTable;
	if($ConfigGame == 1) $parse['UniverseTable'] =	$UniverseTable;
}

//ADMINISTRADORES
if($user['authlevel'] == 3)
{
	$parse['ViewTable']		  =	$ViewTable;
	$parse['EditTable']		  =	$EditTable;
	$parse['ConfigTable']	  =	$ConfigTable;
	$parse['ToolsTable']	  =	$ToolsTable;
	$parse['UniverseTable']	=	$UniverseTable;
}



display( parsetemplate(gettemplate('adm/menu'), $parse), false, '', true, false);
?>