<?php

##############################################################################
# *																			 #
# * XG PROYECT																 #
# *  																		 #
# * @copyright Copyright (C) 2008 - 2010 By Neko from xgproyect.net	         #
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

if ($user['authlevel'] < 1) die();

function LogFunction ($Text, $Estado, $LogCanWork)
{
	global $lang;

	$Archive	=	"../adm/Log/".$Estado.".php";


	if ($LogCanWork == 1)
	{
		if (!file_exists($Archive))
		{
			fopen($Archive, "w+");
			fclose(fopen($Archive, "w+"));
		}

		$FP		 =	fopen ($Archive, "r+");
		$Date	.=	$Text;
		$Date	.=	$lang['log_operation_succes'];
		$Date	.=	date("d-m-Y H:i:s", time())."\n";
		fputs($FP, $Date);
		fclose($FP);
	}
}
?>
