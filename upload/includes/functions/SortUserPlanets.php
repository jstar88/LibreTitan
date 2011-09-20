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

function SortUserPlanets ($CurrentUser)
{
	$Order = ( $CurrentUser['planet_sort_order'] == 1 ) ? "DESC" : "ASC" ;
	$Sort  = $CurrentUser['planet_sort'];
  //start mod
	$QryPlanets  = "SELECT * FROM {{table}} WHERE `id_owner` = '". intval($CurrentUser['id']) ."' AND `destruyed` = 0 ORDER BY ";
   //end mod
	if($Sort == 0)
		$QryPlanets .= "`id` ". $Order;
	elseif($Sort == 1)
	//start mod
		$QryPlanets .= "`universe`, `galaxy`, `system`, `planet`, `planet_type` ". $Order;
 //end mod
	elseif ($Sort == 2)
		$QryPlanets .= "`name` ". $Order;

	$Planets = doquery($QryPlanets, 'planets');

	return $Planets;
}
?>
