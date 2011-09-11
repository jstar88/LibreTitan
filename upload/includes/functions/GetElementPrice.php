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

	function GetElementPrice ($user, $planet, $Element, $userfactor = true, $level = false)
	{
		global $pricelist, $resource, $lang;

		if ($userfactor && ($level === false))
			$level = ($planet[$resource[$Element]]) ? $planet[$resource[$Element]] : $user[$resource[$Element]];

		$is_buyeable = true;

		$array = array(
			'metal'      => $lang['Metal'],
			'crystal'    => $lang['Crystal'],
			'deuterium'  => $lang['Deuterium'],
			'energy_max' => $lang['Energy']
		);

		$text = $lang['fgp_require'];
		foreach ($array as $ResType => $ResTitle)
		{
			if ($pricelist[$Element][$ResType] != 0)
			{
				$text .= $ResTitle . ": ";
				if ($userfactor)
					$cost = floor($pricelist[$Element][$ResType] * pow($pricelist[$Element]['factor'], $level));
				else
					$cost = floor($pricelist[$Element][$ResType]);

				if ($cost > $planet[$ResType])
				{
					$text .= "<b style=\"color:red;\"> <t title=\"-" . pretty_number ($cost - $planet[$ResType]) . "\">";
					$text .= "<span class=\"noresources\">" . pretty_number($cost) . "</span></t></b> ";
					$is_buyeable = false;
				}
				else
					$text .= "<b style=\"color:lime;\">" . pretty_number($cost) . "</b> ";
			}
		}
		return $text;
	}
?>