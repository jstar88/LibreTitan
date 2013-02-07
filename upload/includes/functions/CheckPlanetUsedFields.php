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

	function CheckPlanetUsedFields ( &$CurrentPlanet )
	{
		global $reslist, $resource, $LegacyPlanet;

		foreach($reslist['build'] as $ProdID)
		{
			$cfc	+=	$CurrentPlanet[$resource[$ProdID]];
		}

		if ($CurrentPlanet['field_current'] != $cfc)
		{
			$CurrentPlanet['field_current'] 	= 	$cfc;
			$LegacyPlanet['field_current']		=	'field_current';
		}
	}

?>