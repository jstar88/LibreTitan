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

	function CheckPlanetUsedFields ( &$planet )
	{
		global $resource;

		$cfc  = $planet[$resource[1]]  + $planet[$resource[2]]  + $planet[$resource[3]] ;
		$cfc += $planet[$resource[4]]  + $planet[$resource[12]] + $planet[$resource[14]];
		$cfc += $planet[$resource[15]] + $planet[$resource[21]] + $planet[$resource[22]];
		$cfc += $planet[$resource[23]] + $planet[$resource[24]] + $planet[$resource[31]];
		$cfc += $planet[$resource[33]] + $planet[$resource[34]] + $planet[$resource[44]];
		$cfc += $planet[$resource[45]];

		if ($planet['planet_type'] == '3')
		{
			$cfc += $planet[$resource[41]] + $planet[$resource[42]] + $planet[$resource[43]];
		}

		if ($planet['field_current'] != $cfc)
		{
			$planet['field_current'] = $cfc;
			doquery("UPDATE `{{table}}` SET `field_current`= ".$cfc." WHERE `id` = ".$planet['id']."", 'planets');
		}
	}

?>